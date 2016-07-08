<?php
//Check for valid session:
include('virt.php');
if(!isset($_SESSION['username'])){
	die("You must be logged in to view this page!");
}
echo '';
?>
<div class="row">
	<div class="col-md-12">
		<h3>Storage | <small>List of Virtual Storage Devices</small></h3>
		<?php
			$logfile = 'virt_functions.log';
    		if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
			$conn = libvirt_connect($config['connection'], false);
			
			$storePools = libvirt_list_storagepools($conn);
			foreach($storePools as $pool){
				
				echo '<h4>Storage Pool: <text style="color:green">'.$pool.'</text></h4>';
				echo '<table class="table">';
				echo '<thead><th>Disk Image</th><th>Size</th><th>Options</th></thead>';
				$store = libvirt_storagepool_lookup_by_name($conn, $pool);
				libvirt_storagepool_refresh($store);
				$vols = libvirt_storagepool_list_volumes($store);
				foreach($vols as $volume){
					$volRes = libvirt_storagevolume_lookup_by_name($store, $volume);
					$volInfo =libvirt_storagevolume_get_info($volRes);
					$size = $volInfo['capacity'] / 1024 / 1024 / 1024;
					echo '<tr><td>'.$volume.'</td><td>'.$size.'GB</td><td><button class="btn btn-danger" onClick="delDisk1(\''.$pool.'\',\''.$volume.'\')">Delete vDisk</button></td></tr>';
				}
				echo '</table>';
			}
		?>
	</div>
</div>