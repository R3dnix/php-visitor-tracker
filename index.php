<?php
require_once "VisitorLog.php";

// Initialize tracker..
$VisitorLog = new VisitorLog;

// Do not log when viewed by admin..
if(!isset($_GET["token"]) || $_GET["token"] != $token) $VisitorLog->start();
?>

Just a regular website where you want to implement the tracking system..
