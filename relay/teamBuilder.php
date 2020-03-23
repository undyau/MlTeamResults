<!DOCTYPE html>
<title>Relay Team Selector</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="teamBuilder.css"> 
<div class="white-pink">
<?php
require_once('/home/bigfooto/public_html/relay/mysqli_connect.php');
require_once('/home/bigfooto/public_html/relay/trace.php');
$isNol = ($_POST['radType'] == 'NOL');
$logonError = false;
$logicError = false;
$runners = array(array());
$state = 0;

if ($isNol)
	{
	echo '<h2>NOL Relay Team Selector</h2>';
	$logicError = !validateNol();
	}
else
	{
	echo '<h2>Public Relay Team Selector</h2>';
	$logicError = !validatePublic();
	}

$oldTeams = array();
if (!$logicError && !$logonError)
	{
	outputInstructions();
	$ok = $isNol ? getRunnersNol() : getRunnersPublic();
	foreach($runners as $key => $runner)
		if (strlen($runners[$key]['name']) == 0)
			unset($runners[$key]);  // Toss out spurious junk entry
			
	if ($ok)
		{
		$ok = getTeamIds($oldTeams);
		if ($ok)
			{
			getTeams($oldTeams);
			}
		}
	}
else
	echo ('<div onclick="goBack()">retry</div>');
	
function validatePublic()
{
	global $mysqli;
	global $logonError;	
	$receipt1 = (integer)$_POST['receipt1'];
	$receipt2 = (integer)$_POST['receipt2'];
	$query = "select count(reference) as count from payment ";
	if ($receipt1 != -14021964)
		$query .= "where reference = $receipt1 or reference = $receipt2";
	$result = $mysqli->query($query);
	if (!$result)
		{
		trigger_error($mysqli->error);
		return false;
		}
	else
		{
		if ($result->fetch_assoc()['count'] == 0)
			{
			echo $query;
			echo "Unknown receipt numbers - may not have been processed yet";
			$logonError = true;
			}
		}
	$result->free();
	return true;
}
	
function validateNol()
{
	global $mysqli;
	global $logonError;
	global $state;
	
	$query = $mysqli->prepare('select state from selector where email = ? and password = ?');
	if (!$query)
		{
		trigger_error($mysqli->error);
		return false;
		}	
	$pw = sha1($_POST['password']);
	$email = strtolower($_POST['email']);
	$query->bind_param('ss', $email, $pw);
	if (!$query->execute())
		{
		trigger_error($mysqli->error);
		return false;
		}
	$result = $query->get_result();
	
	if ($result->num_rows != 1)
		{
		echo "Invalid user name or password";
		$logonError = true;
		}
	else 
		$state = $result->fetch_assoc()['state'];

	$result->free();
	return true;
}


function outputInstructions()
{
	echo'<ul><li>You should be able to see all of paid-up runners available for your selection.</li> '."\n";
	echo'<li>Drag runners between the pool of available runners and teams.</li>'."\n";
	echo'<li>Runners should be in running order.</li>'."\n";
	echo'<li>You can name teams by typing on the team name. </li>'."\n";
	echo'<li>Teams of two for the public relay, teams '."\n";
	echo'of 4 for the NOL (man, woman, woman, man).</li></ul>'."\n";
	echo'<button type="button" class="button" id="saveButton">Save these teams</button>'."\n";
	echo'<div id="saveResponse"></div>'."\n";
	echo'</div>'."\n";

}

function getRunnersPublic()
{
	global $mysqli;
	global $runners;
	$receipt1 = (integer)$_POST['receipt1'];
	$receipt2 = (integer)$_POST['receipt2'];
	
	$query = "select entry.id as id, name, sex from entry, payment where ";
	$query .= "entry.ordertime = payment.ordertime and class = 'Public Relay' ";
	if ($receipt1 != -14021964)  //admin hack
		$query .= "and (reference = $receipt1 or reference = $receipt2)";
		
	$result = $mysqli->query($query);
	if (!$result)
		{
		trigger_error($mysqli->error);
		return false;
		}
	else
		{
		while ($row = $result->fetch_assoc())
			$runners[$row['id']] = $row;
		}

	$result->free();
	return true;
}

function getRunnersNol()
{
	global $mysqli;
	global $runners;;
	global $state;
	
	$query = "select entry.id as id, name, sex from entry, payment where ";
	$query .= "entry.ordertime = payment.ordertime and class = 'NOL Relay' ";
	if ($state != -1)  //admin hack
		$query .= "and state = $state";
		
	$result = $mysqli->query($query);
	if (!$result)
		{
		trigger_error($mysqli->error);
		return false;
		}
	else
		{
		while ($row = $result->fetch_assoc())
			$runners[$row['id']] = $row;
		}

	$result->free();
	return true;
}

function getTeamIds(&$teams)
{
	global $mysqli;
	global $runners;
	global $state;
	global $isNol;

	$keys = array_keys($runners);
	foreach ($keys as $key)
		$clause .= $key.",";
	$clause = substr($clause, 0, strlen($clause) -1);
	$query = "select distinct teamid from teammember where ";
	$query .= "entryid in ($clause)";
		
	$result = $mysqli->query($query);
	if (!$result)
		{
		trigger_error($mysqli->error.' '.$query);
		return false;
		}
	else
		while($row = $result->fetch_assoc())
			$teams[] = $row['teamid'];

	$result->free();
	return true;
}


function outputRunner($id)
{
	global $runners;
	$gender = $runners[$id]['sex'] == "M" ? "male" : "female";
	
	echo'<div class="runner '.$gender.'" draggable="true">'.stripslashes($runners[$id]['name']).'</div>'."\n";
}

function getOtherTeamNames()
{
	global $oldTeams;
	global $mysqli;
	$clause = "";
	foreach ($oldTeams as $teamid)
		$clause .= $teamid.",";	
	$clause = substr($clause, 0, strlen($clause) - 1);
	if (strlen($clause) == 0)
		$query = "select name from team";
	else
		$query = "select name from team where id not in ($clause)";

	$result = $mysqli->query($query);
	$answer = "";
	if (!$result)
		{
		trigger_error($mysqli->error);
		return "";
		}
	else
		{
		while ($row = $result->fetch_assoc())
			$answer .= '"'.$row['name'].'",';
		$answer = substr($answer, 0, strlen($answer) - 1);
		}
	return $answer;
}

function getTeams($teams)
{
	global $mysqli;
	global $runners;
	global $isNol;

	$newTeamsNeeded = intval($isNol ? ((count($runners)+3)/4) - count($teams) : ((count($runners)+1)/2) - count($teams));
	echo'<div id="columns">'."\n";
	for ($i=0; $i < $newTeamsNeeded; $i++)
		echo'  <div class="column" draggable="false"><header contenteditable="true" >&lt;team name here&gt;</header></div>'."\n";

	$myrunners = $runners;
	foreach ($teams as $team)
		{
		$query = "select team.name as teamname, team.id as teamid, entry.name as entryname,";
		$query .= "entry.id as entryid, teammember.runningorder ";
		$query .= "from entry, team,teammember where team.id = $team and team.id = teammember.teamid ";
		$query .= "and entry.id = teammember.entryid order by runningorder";
		
		$result = $mysqli->query($query);
		if (!$result)
			{
			trigger_error($mysqli->error);
			return false;
			}
		else
			{
			$i = 0;
			while ($row = $result->fetch_assoc())
				{
				if ($i == 0)
					echo '<div class="column" draggable="false"><header contenteditable="true">'.$row['teamname'].'</header>'."\n";
				unset($myrunners[$row['entryid']]);
				outputRunner($row['entryid']);				
				$i++;
				}
			if ($i > 0)
				echo '</div>'."\n";
			}
		}
	echo'</div>'."\n";
	echo'<div id="available"><header>Available</header>'."\n";
	foreach ($myrunners as $runner)
		outputRunner($runner['id']);
	echo'</div>';	
}
?>

<script>
var dragSrcEl = null;
<?php
if ($isNol)
	echo 'var NOL = true;'."\n";
else
	echo 'var NOL = false;'."\n";
?>
var RUNNER_EL_OFFSET = 1;

function handleDragStart(e) {
  this.style.opacity = '0.4';  // this / e.target is the source node.
  dragSrcEl = this;
  e.dataTransfer.setData('Text', this.id); // required otherwise doesn't work
}

function handleDragOver(e) {
  if (e.preventDefault) {
    e.preventDefault(); // Necessary. Allows us to drop.
  }

  e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

  return false;
}

function handleDragEnter(e) {
  // this / e.target is the current hover target.
  if (canAddToTeam(this, dragSrcEl))
	this.classList.add('over');
}

function handleDragLeave(e) {
  this.classList.remove('over');  // this / e.target is previous target element.
}

function addToTeam(target, runnerEl)
{
	if (!NOL)
		{
		target.appendChild(runnerEl);
		return;
		}

//For NOL, want to have man, woman, woman, man
	var fCount = 0;
	var mCount = 0;
	var nodes = target.querySelectorAll('div.runner');
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].classList.contains('female'))
			fCount++;
		else if (nodes[i].classList.contains('male'))
			mCount++;
		}
	switch (mCount + fCount)
		{
		case 0: target.appendChild(runnerEl); break;
		case 1: if (mCount == 1 ||
					runnerEl.classList.contains('female'))
					target.appendChild(runnerEl);
				else 
					target.insertBefore(runnerEl, target.childNodes[0+RUNNER_EL_OFFSET]);
				break;
		case 2: if (mCount == 1) 
					target.appendChild(runnerEl);
				else
					if (mCount == 2)
						target.insertBefore(runnerEl, target.childNodes[1+RUNNER_EL_OFFSET]);
					else
						target.insertBefore(runnerEl, target.childNodes[0+RUNNER_EL_OFFSET]);
				break;
		case 3: if (mCount == 2)
					target.insertBefore(runnerEl, target.childNodes[2+RUNNER_EL_OFFSET]);
				else
					target.appendChild(runnerEl);	
				break;
		}
}

function canAddToTeam(target, runnerEl)
{
	if (!NOL)
		{
		return target.querySelectorAll('div.runner').length < 2;
		}
	else // NOL
		{
		var fCount = 0;
		var mCount = 0;
		var nodes = target.querySelectorAll('div.runner');
		for (var i = 0; i < nodes.length; i++) {
			if (nodes[i].classList.contains('female'))
				fCount++;
			else if (nodes[i].classList.contains('male'))
				mCount++;
		}
		// Need man, woman, woman, man
		if (runnerEl.classList.contains('female'))
			return fCount < 2;
		if (runnerEl.classList.contains('male'))
			return mCount < 2;
		
		return false;
		}
}

function handleDrop(e) {
  // this / e.target is current target element.

  if (e.stopPropagation) {
    e.stopPropagation(); // stops the browser from redirecting.
  }

  if (dragSrcEl != this) {
	// Handle runner to team
	if (this.classList.contains('column') && 
	    dragSrcEl.classList.contains('runner'))
		{
		if (canAddToTeam(this, dragSrcEl))
			addToTeam(this, dragSrcEl);
		}
	else if (this.id == 'available' && dragSrcEl.classList.contains('runner'))
		this.appendChild(dragSrcEl);
		
  }

  return false;
}

function handleDragEnd(e) {
  // this/e.target is the source node.
  this.style.opacity = '1';
  [].forEach.call(cols, function (col) {
    col.classList.remove('over');
  });
  [].forEach.call(runners, function (col) {
    col.classList.remove('over');
  });
  [].forEach.call(pool, function (col) {
    col.classList.remove('over');	
  });  
}


document.getElementById("saveButton").addEventListener("click", saveChanges);

var cols = document.querySelectorAll('div.column');
[].forEach.call(cols, function(col) {
  col.addEventListener('dragenter', handleDragEnter, false);
  col.addEventListener('dragover', handleDragOver, false);
  col.addEventListener('dragleave', handleDragLeave, false);
  col.addEventListener('drop', handleDrop, false);
  col.addEventListener('dragend', handleDragEnd, false);
 // col.addEventListener('dragstart', handleDragStart, false);
});

var runners = document.querySelectorAll('div.runner');
[].forEach.call(runners, function(col) {
  col.addEventListener('dragenter', handleDragEnter, false);
  col.addEventListener('dragover', handleDragOver, false);
  col.addEventListener('dragleave', handleDragLeave, false);
  col.addEventListener('drop', handleDrop, false);
  col.addEventListener('dragend', handleDragEnd, false);
  col.addEventListener('dragstart', handleDragStart, false);  
});

var pool = document.querySelectorAll('#available');
[].forEach.call(pool, function(col) {
  col.addEventListener('dragenter', handleDragEnter, false);
  col.addEventListener('dragover', handleDragOver, false);
  col.addEventListener('dragleave', handleDragLeave, false);
  col.addEventListener('drop', handleDrop, false);
  col.addEventListener('dragend', handleDragEnd, false); 
}

);

function goBack() {
    window.history.back()
}

function validTeams()
{
	var e = document.getElementById("columns");
	var nodes = e.querySelectorAll('.column');
	<?php
	echo("var teams = [");
	echo (getOtherTeamNames());
	echo ("];\n");
	?>
	for (var i = 0; i < nodes.length; i++) {
		var runners = nodes[i].childNodes;
		if (runners.length < 2)
			continue;
		var team = runners[0].innerHTML;
		if (team.length > 4 && team.slice(-4) == "<br>")
			team = team.substring(0, team.length - 4);
		if (team == "&lt;team name here&gt;") {
			window.alert("Set team name by typing on <team name here>");
			return false;
			}

		if (teams.indexOf(team) >= 0) {
			window.alert("There is already a team called " + team);
			return false;
			}
			
		var re = /^[a-zA-Z0-9\s]+$/;  
		if (!re.exec(team)) {
			window.alert("Only use alphanumeric characters and spaces in team name");
			return false;
			}
  
		teams.push(team);
		}
	
	return true;
}

function runnerId(runnerName)
{
<?php
	foreach ($runners as $runner)
		{
		echo 'if (runnerName == "'.$runner['name'].'") return '.$runner[id].';'."\n";
		}
?>
}

function teamsAsList()
{
	var e = document.getElementById("columns");
	var nodes = e.querySelectorAll('.column');
	var count = 0;
	var result="";
	for (var i = 0; i < nodes.length; i++) {
		var runners = nodes[i].querySelectorAll('.runner');
		if (runners.length < 1)
			continue;
		count++;
		var team = nodes[i].firstChild.innerHTML;
		if (team.length > 4 && team.slice(-4) == "<br>")
			team = team.substring(0, team.length - 4);
		result += "&team" + count + "=" + team + ",";
		for (var j = 0; j < runners.length; j++)
			{
			result += runnerId(runners[j].innerHTML); //lookup - PHP must create lookup table
			if (j < runners.length -1)
				result += ",";
			}
		}
	return "teamCount="+count + result;
}

function saveChanges() {
		document.getElementById("saveResponse").innerHTML = "";
    if (!validTeams())
			return;
<?php
foreach ($oldTeams as $teamid)
	$clause .= $teamid.",";
$clause = substr($clause, 0, strlen($clause) -1);
if ($isNol)
	echo 'var str="class=NOL relay&oldTeams='.$clause.'&"+teamsAsList()'."\n";
else
	echo  'var str="class=Public relay&oldTeams='.$clause.'&"+teamsAsList()'."\n";
?>

    if (str == "") {
        document.getElementById("saveResponse").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("saveResponse").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","saveteams.php?"+str,true);
        xmlhttp.send();
    }
}
</script>