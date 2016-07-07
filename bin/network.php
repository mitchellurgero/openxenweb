<?php
session_start();
include('../functions.php');
if(!isset($_SESSION['username'])){
	die("You must be logged in to view this page!");
}
setConfig($_POST['data']);

function setConfig($data){
	
}