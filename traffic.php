<?php
/* Simple Administration Panel */
/* -by Rednix */
/*******************************************************************************************************************************/

require_once "VisitorLog.php";
$token = "6yva46";

// Check if the security token is provided and correct..
if(!isset($_GET["token"]) || $_GET["token"] != $token) die("Sorry but you very very idiot!");

// Ignore repeating IPs and google bots to get accurate unique visitors count..
$unique_visits_sql = ORM::for_table('visitors')->where_not_like('agent', '%google.com%')->where_not_like('agent', '%GoogleOther%')->find_array();
foreach($unique_visits_sql as $data)
	$unique_visits[] = $data["ip"];
$unique_visits = !empty($unique_visits) ? count(array_values(array_unique($unique_visits))) : 0;

// Gather countries by IPs and populate the database..
if(isset($_GET["countries"])) {
	foreach($VisitorLog->pull() as $data) {
		if($data["country"] == "") {
			$select_log = ORM::for_table("visitors")->where(array("id" => $data["id"]))->find_one();
			$select_log->set(array(
				"country" => ip_to_country($data["ip"])
			));
			$select_log->save();
			echo $data["country"];
		}
	}	
}

// Function to return time of how long ago the visitor was logged..
function get_time_ago($time) {
	$time_difference = time() - $time;
	if($time_difference < 60 * 5) { return "online"; }
	$condition = array(12 * 30 * 24 * 60 * 60 => "year",
				30 * 24 * 60 * 60 => "month",
				24 * 60 * 60 => "day",
				60 * 60 => "hour",
				60 => "minute",
				1 => "second"
	);
	foreach($condition as $secs => $str) {
		$d = $time_difference / $secs;
		if($d >= 1) {
			$t = round( $d );
			//return $t . " ". $str . ( $t > 1 ? "s" : "" ) . " ago";
			return $t . " ". $str . ( $t > 1 ? "s" : "" );
		}
	}
}
?>

<a href="?token=<?=$token?>&countries" style="float: right; margin-right: 15px;">[Gather Countries]</a>

Visits: <?=ORM::for_table("visitors")->count()?><br />
Unique: <?=$unique_visits?><br />

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
