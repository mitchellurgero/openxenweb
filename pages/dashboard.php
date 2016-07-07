<?php
//Check for valid session:
include('functions.php');
include("config.php");
if(!isset($_SESSION['username'])){
	die("You must be logged in to view this page!");
}
echo '';
?>
<div class="row">
	<div class="col-md-12">
		<h3>Dashboard | <small>List of local Virtual Machines</small>&nbsp;&nbsp; <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newVM">Add VM</button></h3>
	</div>
	<div class="col-md-12">
		
		<table class="table">
			<thead>
				<th>VM Name</th>
				<th>Status</th>
				<th>Network</th>
				<th>ID</th>
			</thead>
			<tbody>
				<?php
					$logfile = 'virt_functions.log';
    				if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
					$conn = libvirt_connect($config['connection'], false);
     				$doms = libvirt_list_domains($conn);
     				$connInfo = libvirt_connect_get_sysinfo($conn);
					foreach($doms as $domain){
						$res = libvirt_domain_lookup_by_name($conn, $domain);
						$info = libvirt_domain_get_info($res);
						$netDev = libvirt_domain_get_interface_devices($res);
						if($netDev == -1 or $netDev == false or $netDev == null or $netDev == array ( "num" => 0 )){
							$netDev = array("","");	
						}
						$id = libvirt_domain_get_id($res);
						if($id == -1){
							$id = "";
						}
						$state = "<text style=\"color:red\">OFFLINE</text>";
						if($info['state'] == 1 ){
							$state = "<text style=\"color:green\">ONLINE</text>";
						} elseif($info['state'] == 3) {
							$state = "<text style=\"color:blue\">PAUSED</text>";
						}
						echo "<tr><td><a href=\"#\" onClick=\"vmInfo('$domain')\">$domain</a></td><td>$state</td><td>".$netDev[0]."</td><td>$id</td></tr>";
					}
					
				?>
			</tbody>
		</table>
	</div>
</div>