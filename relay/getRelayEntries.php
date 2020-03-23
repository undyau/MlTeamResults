  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
  <TITLE>Load Eventor entries</TITLE>
<?php
/* This script reads event entries into the entry table
   Each event entry should be matchable with an order timestamp
   from the corresponding payment (imported to payment table via a spreadsheet).
   
   Unfortunately the timestamps aren't synced so some fixup is required.
   Finally we have best shot at entries and matchable order time.
  */
require_once('/home/bigfooto/public_html/relay/mysqli_connect.php');
require_once('/home/bigfooto/public_html/relay/trace.php');

$baseUrl = "https://eventor.orienteering.asn.au/api/";
global $DEBUGME;
$DEBUGME = true;
$event = 2194;
$classes = array();

getClasses("eventclasses?eventId=$event");
processQuery("entries?eventIds=$event&includePersonElement=true&includeEntryFees=true&includeOrganisationElement=true");
fixOrderTimes();

function fixOrderTimes()
{
	global $mysqli;
	$rows = array(array());
	$query = "select id, ordertime, name from entry where ordertime not in (select ordertime from payment) ";
	if ($result = $mysqli->query($query)) 
		$rows = $result->fetch_all(MYSQLI_ASSOC);

	$result->free();
	
	foreach ($rows as $row)
		{
		Trace("Processing ".$row['name']);
		$query = "select min(p.ordertime) as newtime from payment p, entry e where e.id = ".$row['id']." and ";
		$query .= "TIMESTAMPDIFF(SECOND, e.ordertime, p.ordertime) <= 5 and  TIMESTAMPDIFF(SECOND, e.ordertime, p.ordertime) >= 0";
		Trace($query);
		$result = $mysqli->query($query);
		if (!$result)
			{
			trigger_error($mysqli->error);
			return;
			}

		if ($result->num_rows != 1 || is_null($result->fetch_object()->newtime))
			{
			$query = "select min(p.ordertime) as newtime from payment p, entry e where e.id = ".$row['id']." and ";
			$query .= "TIMESTAMPDIFF(HOUR, e.ordertime, p.ordertime) <= 2 and  TIMESTAMPDIFF(HOUR, e.ordertime, p.ordertime) >= -2 and ";
			$query .= "UPPER(LEFT(p.customer,".strlen($row['name']).")) = '".strtoupper($row['name'])."'";
		Trace($query);
			$result = $mysqli->query($query);
			if (!$result)
				{
				trigger_error($mysqli->error);
				return;
				}
			}
			
		if ($result->num_rows != 1)
			continue;
			
		$value = $result->fetch_object();
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
	
	$query = $mysqli->prepare('insert ignore into entry(ordertime, name, sportident, class, club, state, sex) values (?,?,?,?,?,?,?)');
	if (!$query)
		{
		trigger_error($mysqli->error);
		return;
		}
	$query->bind_param('ssissis', $orderTime, $name, $si, $class, $club, $state, $sex);
	foreach ($entries->Entry as $entry)
		{
		$name = $mysqli->real_escape_string((string)$entry->Competitor->Person->PersonName->Given)." ".
			$mysqli->real_escape_string((string)$entry->Competitor->Person->PersonName->Family);
		$si = getSI($entry->Competitor);
		$club = $mysqli->real_escape_string((string)$entry->Competitor->Organisation->Name);
		$class = getClass((integer)$entry->EntryClass->EventClassId);
		$state = (integer)$entry->Competitor->Organisation->ParentOrganisation->OrganisationId;
		$orderTime = (string)$entry->EntryDate->Date." ".((string)$entry->EntryDate->Clock);
		//Trace ("Before: $orderTime");
		$date = new DateTime($orderTime);
		$date->add(new DateInterval('PT11H'));
		$orderTime = $date->format('Y-m-d H:i:s');
        $sex = (string)$entry->Competitor->Person['sex']; 
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
