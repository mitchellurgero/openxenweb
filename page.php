
<?php
session_start();
if(!isset($_SESSION['username'])){
	die("You must be logged in to view this page!");
}
try{
if(isset($_POST['page'])){
	switch($_POST['page']){
		case "dashboard":
			dashboard();
			break;
		case "VMs":
			vms();
			break;
		case "network":
			network();
			break;
		case "storage":
			storage();
			break;
		case "iso":
			iso();
			break;
		case "users":
			users();
			break;
		case "shell":
			shell();
			break;
		default:
			echo "404 - Page not found!";
			break;
		
	}
}
}catch(Exception $ex){
	echo '<br /><br /><div class="text-center">There has been a fatal error loading the requested page: <br /> '.$ex->getMessage().'</div>';
}
//Simple page functions..
function dashboard(){
	include('pages/dashboard.php');
}
function shell(){
	include('pages/shell.php');
}
function users(){
	include('pages/users.php');
}
function vms(){
	include('pages/vms.php');
}
function network(){
	include('pages/network.php');
}
function storage(){
	include('pages/storage.php');
}
function iso(){
	include('pages/iso.php');
}
?>