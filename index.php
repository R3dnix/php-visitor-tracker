<?php
require_once "VisitorLog.php";

// Do not log when viewed by admin..
if(!isset($_GET["token"]) || $_GET["token"] != $token) $VisitorLog->start();
?>

Just a regular website where you want to implement the tracking system.. Use your token in the URL to avoid logging your own visits..
