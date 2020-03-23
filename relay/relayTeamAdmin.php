 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
 <TITLE>Administer teams</TITLE>
<?php
require_once('/home/bigfooto/public_html/relay/mysqli_connect.php');
require_once('/home/bigfooto/public_html/relay/trace.php');

$DEBUGME = true;
$teams = array();
$assignedRunners = array(array());
$unassignedRunners = array(array());

getRunners();
getTeams();

function getRunners()
{
	global $mysqli;
	global $runners;
	
	$query = "select id, name from entry, payment where entry.ordertime = payment.ordertime and (reference = ".$_POST['receipt1'];
	if (strlen($_POST(['receipt2'])) > 0)
		$query .= " or reference = ".$_POST(['receipt2']);
	$query .= ") and teamid is NULL";
		
	if ($result = $mysqli->query($query)) 
		{
		$unassignedrunners = $result->fetch_all(MYSQLI_ASSOC);
		foreach ($unassignedrunners as $runner)
			echo ($runner['name']."<br/>");
		}
	else
		{
		trigger_error($mysqli->error);
		return;
		}
		
	$query = "select id, name from entry, payment where entry.ordertime = payment.ordertime and (reference = ".$_POST['receipt1'];
	if (strlen($_POST(['receipt2'])) > 0)
		$query .= " or reference = ".$_POST(['receipt2']);
	$query .= ") and teamid is not NULL";
		
	if ($result = $mysqli->query($query)) 
		{
		$assignedrunners = $result->fetch_all(MYSQLI_ASSOC);
		}
	else
		{
		trigger_error($mysqli->error);
		return;
		}		
}

function getTeams()
{
	global $mysqli;
	global $teams;
	
	$query = "select id, name, class from team where id in (selevt team = payment.ordertime and (reference = ".$_POST['receipt1'];
	if (strlen($_POST(['receipt2'])) > 0)
		$query .= " or reference = ".$_POST(['receipt2']);
	$query .= ")";
		
	if ($result = $mysqli->query($query)) 
		{
		$runners = $result->fetch_all(MYSQLI_ASSOC);
		foreach ($runners as $runner)
			echo ($runner['name']."<br/>");
		}
	else
		{
		trigger_error($mysqli->error);
		return;
		}
}

function fixOrderTimes()
{
	global $mysqli;
	$rows = array(array());
	$query = "select id, ordertime from entry where ordertime not in (select ordertime from payment) ";
	if ($result = $mysqli->query($query)) 
		$rows = $result->fetch_all(MYSQLI_ASSOC);

	$result->free();
	
	foreach ($rows as $row)
		{
		$query = "select min(p.ordertime) as newtime from payment p, entry e where e.id = ".$row['id']." and ";
		$query .= "TIMESTAMPDIFF(SECOND, e.ordertime, p.ordertime) <= 5 and  TIMESTAMPDIFF(SECOND, e.ordertime, p.ordertime) >= 0";
		//Trace($query);
		$result = $mysqli->query($query);
		if (!$result)
			{
			trigger_error($mysqli->error);
			return;
			}
		if ($result->num_rows == 1)
			$value = $result->fetch_object();
		else 
			continue;
		//var_dump($value);
    if (is_null($value->newtime))
      continue;
		$query = "update entry set ordertime = '".$value->newtime."' where id = ".$row['id'];
		Trace($query);
		$result = $mysqli->query($query);
		if (!$result)
			{
			trigger_error($mysqli->error);
			return;
			}
		}
}

function getClasses($query)
	{
	global $classes;
	retrieveQuery($query, $xml);
	//file_put_contents("classes.xml",$xml);
	$classesXml = simplexml_load_string($xml);
	foreach ($classesXml->EventClass as $class)
		{
		$classes[(integer)$class->EventClassId] = (string)$class->Name;
		}
	}

function retrieveQuery($query, &$xml)
	{
	global $baseUrl;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $baseUrl.$query);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,
	array("ApiKey: 33d2fdb0231649faaf3aab993c2c8bbe"));
	$xml = curl_exec($ch);
	curl_close($ch);
	}

function parseQuery($xml)
	{
	global $mysqli;
	$entries = simplexml_load_string($xml);
	//file_put_contents("entries.xml",$xml);
	
	$query = $mysqli->prepare('insert ignore into entry(ordertime, name, sportident, class, club, state) values (?,?,?,?,?,?)');
	if (!$query)
		{
		trigger_error($mysqli->error);
		return;
		}
	$query->bind_param('ssissi', $orderTime, $name, $si, $class, $club, $state);
	foreach ($entries->Entry as $entry)
		{
		$name = $mysqli->escape_string((string)$entry->Competitor->Person->PersonName->Given)." ".
			$mysqli->escape_string((string)$entry->Competitor->Person->PersonName->Family);
		$si = getSI($entry->Competitor);
		$club = $mysqli->escape_string((string)$entry->Competitor->Organisation->Name);
		$class = getClass((integer)$entry->EntryClass->EventClassId);
		$state = (integer)$entry->Competitor->Organisation->ParentOrganisation->OrganisationId;
		$orderTime = (string)$entry->EntryDate->Date." ".((string)$entry->EntryDate->Clock);
		//Trace ("Before: $orderTime");
		$date = new DateTime($orderTime);
		$date->add(new DateInterval('PT11H'));
		$orderTime = $date->format('Y-m-d H:i:s');
		//Trace ("After: $orderTime");
		
		if (!$query->execute())
			{
			trigger_error($mysqli->error);
			return;
			}
		}
	}
	
function getClass($class)
	{
	global $classes;
	return $classes[$class];
	}
	
function getSI($comp)
	{
	foreach($comp->CCard as $card)
		{
		$SiNumber = 0;
		if ((string)$card->PunchingUnitType['value'] == "SI")
			$SiNumber = (int)$card->CCardId;
		if ($SiNumber < 10000)
			break;
		}
	return $SiNumber;
	}
	
function processQuery($query)
	{
	retrieveQuery($query, $xml);
	parseQuery($xml);
	}
?>
