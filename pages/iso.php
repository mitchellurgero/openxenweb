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
		<h3>ISO's | <small>List of Virtual CDs & DVDs</small></h3>
		<table class="table">
			<thead>
				<th>ISO Name</th>
				<th>Size</th>
				<th>Options</th>
			</thead>
			<tbody>
				<?php
					$logfile = 'virt_functions.log';
    				if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
					$conn = libvirt_connect($config['connection'], false);
					$iso = getFile("/var/iso");
					foreach($iso as $img){
						if($img == "dummy.iso") { continue; }
						$fs = round(filesize("/var/iso/$img") / 1024 / 1024,1);
						echo '<tr><td>'.$img.'</td><td>'.$fs.'MB</td><td></td></tr>'; //<button class="btn btn-danger" onClick="delISO(\''.$img.'\')">Delete ISO</button>
					}
				?>
			</tbody>
		</table>
	</div>
</div>