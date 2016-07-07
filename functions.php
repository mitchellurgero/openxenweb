<?php
//Auth against local user accounts
function auth($username, $password){
	$password = escapeshellarg($password);
	$result = exec("sudo ./bin/chkpasswd $username $password");
	$users = shell_exec("sudo bash ./bin/listusers.sh");
    $usersAr = explode("\n", $users);
    $usersAr2 = array_filter($usersAr);
    $uAr1 = array();
    foreach($usersAr2 as $u){
    	$u2 = explode(":", $u);
    	array_push($uAr1, $u2[0]);
    }
    if (!in_array($username, $uAr1)) {
    		return false;
    }
	if($result == "Not Authenticated"){
		return false;
	} elseif($result == "Authenticated"){
		return true;
	} else {
		$_SESSION['loginAPIError'] = $result;
		return false;
	}
}
function getCurrentIP($name){
	return exec("/sbin/ifconfig $name | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");
}
function getKVMNet(){
	$logfile = 'virt_functions.log';
    if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
	$conn = libvirt_connect('xen:///', false);
    $connInfo = libvirt_connect_get_sysinfo($conn);
	$networks = libvirt_list_networks($conn, VIR_NETWORKS_ALL);
	return $networks;
}
function getFile($path){
	if ($handle = opendir($path)) {
	$temp = array();
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            array_push($temp, $entry);
        }
    }
    closedir($handle);
	}
	return $temp;
}
function generateString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>