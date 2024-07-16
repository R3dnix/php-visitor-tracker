<?php
/* Simple Administration Panel */
/* -by b3hindy0u */
/*******************************************************************************************************************************/

require_once "include.php";
$token = "6yva46";

// Check if the security token is provided and correct..
if(!isset($_GET["token"]) || $_GET["token"] != $token) die("Sorry but you very very idiot!");

// Ignore repeating IPs and google bots to get accurate unique visitors count..
$unique_visits_sql = ORM::for_table('visitors')->where_not_like('agent', '%google.com%')->find_array();
foreach($unique_visits_sql as $data)
	$unique_visits[] = $data["ip"];
$unique_visits = array_values(array_unique($unique_visits));
?>
<a href="?token=<?=$token?>&countries" style="float: right; margin-right: 15px;">[Gather Countries]</a>

Visits: <?=ORM::for_table("visitors")->count()?><br />
Unique: <?=count($unique_visits)?><br />

	<div style="float: right">Showing last 50 records. Click <a href="?token=<?=$token?>&show_all">here</a> to show all.</div>
	<table cellpadding="4" border="0" style="width: 100%; font-size: 15px;">
		<thead style="background: black; color: snow;">
			<tr align="center">
			<td>Ip</td>
			<td>Location</td>
			<td style="width: 70px">Time</td>
			<td>Device</td>
			<td>Browser</td>
			<td>OS</td>
			<td>Ref Page</td>
			<td>UserAgent</td>
			</tr>
		</thead>
	<tbody>
	<?php if($visitors = $VisitorLog->pull()) { ?>
	<?php if(!isset($_GET["show_all"])) $visitors = array_splice($visitors, 0, 50); ?>
	<?php foreach($visitors as $data) { ?>
		<tr style="text-align: center;">
			<td><?=$data["ip"];?> <b><small>(<?=$data["country"];?>)</small></b></td>
			<td><?=$data["location"];?></td>
			<td><?=get_time_ago($data["time"])?></td>
			<td><?=$data["device"];?></td>
			<td><?=$data["browser"];?></td>
			<td><?=$data["os"];?></td>
			<td><?=$data["ref"];?></td>
			<td style="font-size: 11px;"><?=$data["agent"];?></td>
		</tr>
	<?php }} ?>
	</tbody>
	</table>
