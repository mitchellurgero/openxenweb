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
		<h3>Network | <small>List of Network Devices</small> &nbsp; <button class="btn btn-primary" data-toggle="modal" data-target="#netconfig">Edit Network Configuration</button></h3>
		<table class="table">
			<thead>
				<th>Type</th>
				<th>Network Device</th>
				<th>Range</th>
				<th>IP (If available)</th>
				<th>Fwding</th>
				<th>Fwd Device</th>
				<th>DHCP Range</th>
			</thead>
			<tbody>
				<?php
					//First We need to list physical or virtual devices, then KVM devices.
					$phyInterfaces = shell_exec("sudo bash ./bin/list_adapters.sh");
					$bridgeInterfaces = shell_exec("sudo brctl show");
					$br = explode("\n", $bridgeInterfaces);
					$adapters = explode("\n", $phyInterfaces);
    				foreach($adapters as $dev){
	    				if($dev != "" && $dev != "lo"){
	    					$type = "Physical";
	    					foreach($br as $bridge){
	    						$brsplit = explode(" ", $bridge);
	    						$pos = strpos($brsplit[0], $dev);
	    						if($pos !== false){
	    							$type = "Bridge";
	    						}
	    					}
    						echo '<tr><td>'.$type.'</td><td>'.$dev.'</td><td>N/A</td><td>'.getCurrentIP($dev).'</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>';
    					}
    				}
					$logfile = 'virt_functions.log';
    				if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
					$conn = libvirt_connect($config['connection'], false);
     				$connInfo = libvirt_connect_get_sysinfo($conn);
					$networks = libvirt_list_networks($conn, VIR_NETWORKS_ALL);
					foreach($networks as $network){
						$nets = libvirt_network_get($conn, $network);
						$info = libvirt_network_get_information($nets);
						$ip = $info['ip'];
						$range = $info['ip_range'];
						$fwd = $info['forwarding'];
						$fwd_dev = $info['forward_dev'];
						$active = $info['active'];
						$active_str =  $active ? 'Yes' : 'No';
						$dhcp = (array_key_exists('dhcp_start', $info)) ? $info['dhcp_start'].' - '.$info['dhcp_end'] : '-';
						echo "<tr><td>KVM Device</td><td>$network</td><td>$range</td><td>$ip</td><td>$fwd</td><td>$fwd_dev</td><td>$dhcp</td></tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<div id="netconfig" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Network Configuration</h4>
      </div>
      <div class="modal-body">
        <div>
        	<textarea class="form-control" rows="20" cols="100%" name="net_file" id="net_file"><?php echo shell_exec("sudo cat /etc/network/interfaces"); ?></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-warning">Save Configuration</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>