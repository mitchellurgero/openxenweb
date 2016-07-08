<?php
// Libvirt POST api for web interface
// API documentation: https://libvirt.org/php/api-reference.html or https://libvirt.org/php/dev-api-reference.html

//Change to meet your needs (Usually: qemu:///system -OR- xen:///)


session_start();
include('functions.php');
include('config.php');
if(!isset($_SESSION['username'])){
	die("You must be logged in to view this page!");
}
$logfile = 'virt_functions.log';
//unlink('virt_functions.log');
if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
$conn = libvirt_connect($config['connection'], false);
$doms = libvirt_list_domains($conn);
switch($_POST['func']){
	case "info":
		getDomainInfo($_POST['domain']);
		break;
	case "start":
		startVM($_POST['domain']);
		break;
	case "stop":
		stopVM($_POST['domain']);
		break;
	case "pause":
		pauseVM($_POST['domain']);
		break;
	case "screen":
		screen($_POST['domain']);
		break;
	case "create":
		createVM($_POST['vm_name'], $_POST['vm_mem'], $_POST['vm_arch'], $_POST['vm_core'], $_POST['vm_hdd'], $_POST['iso'], $_POST['vm_net'], $_POST['vm_net_dev'], $_POST['vm_auto'], $_POST['vm_flags']);
		break;
	case "delete":
		removeVM($_POST['domain']);
		break;
	case "delDiskImg":
		delDisk($_POST['pool_name'],$_POST['disk_name']);
		break;
	case "delISO":
		delISO($_POST['file']);
		break;
	case "changeVM":
		changeVM($_POST['vm_name'], $_POST['vm_auto_current']);
		break;
}

//functions

function getDomainInfo($domain){
	global $conn;
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	$info = libvirt_domain_get_info($res);
	$state = $info['state'];
    $xmlString = libvirt_domain_get_xml_desc($res, '');
    $xml = simplexml_load_string($xmlString);
    $json = json_encode($xml);
    $data = json_decode($json,TRUE);

    $vnc = intval($data["devices"]["graphics"]["@attributes"]["port"]);
	if($state == 1){
		$state = "online";
	} elseif($state == 3){
		$state = "paused";
	} else { 
		$state = "offline";
	}
	$host= gethostname();
	$ip = gethostbyname($host);
	$checked = "";
	echo '';
	?>
	<p>
		<table class="table">
			<tr><td>Max Memory</td><td><?php echo round($info['maxMem'] / 1000)."MB";?></td></tr>
			<tr><td>Assigned Memory</td><td><?php echo round( $info['memory'] / 1000)."MB";?></td></tr>
			<tr><td>State</td><td><?php echo $state;?></td></tr>
			<tr><td># vCPUs</td><td><?php echo $info['nrVirtCpu'];?></td></tr>
			<tr><td>CPU Usage</td><td><?php echo $info['cpuUsed'];?></td></tr>
			<?php
			if(libvirt_domain_get_autostart($res)){
				$checked = "checked";
			}
			?>
			<tr><td>Autostart with Host</td><td><input id="vm_auto_current" name="vm_auto_current" type="checkbox" value="autostart" <?php echo $checked; ?>></td></tr>
		</table>
		<div class="row">
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
      </button>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">VM Controls
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="#" onClick="startVM('<?php echo $domain; ?>')">Start</a></li>
          <li><a href="#" onClick="pauseVM('<?php echo $domain; ?>')">Pause</a></li>
          <li><a href="#" onClick="stopVM('<?php echo $domain; ?>')">Stop VM</a></li>
          <li><a href="#" onClick="getScreen('<?php echo $domain; ?>')">Screenshot</a></li>
        </ul>
      </li>
      <li><a target="_blank" href="http://<?php echo $_SERVER['SERVER_ADDR'];?>/code/workspace/OpenVirt/vnc/index.php?host=<?php echo $_SERVER['SERVER_ADDR'];?>&port=<?php echo $vnc; ?>">View Console</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#" style="color:red" onclick="deleteVM1('<?php echo $domain; ?>')">Delete VM</a></li>
      </ul>
    </div>
  </div>
</nav>
			<br /> 
			<div class="text-center">
				<button class="btn btn-primary" onClick="changeVM('<?php echo $domain; ?>')">Save Changes</button>
			</div>
		</div>
		</p>
	
	<?php
	//print_r($info);
}
function startVM($domain){
	global $conn;
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	$temp = libvirt_domain_create($res);
	if(!$temp){
		$temp = libvirt_domain_resume($res);
	}
	if($temp == 1){
		$temp = "Starting of $domain was successful!";
	}
	echo $temp;
}
function pauseVM($domain){
	global $conn;
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	$temp = libvirt_domain_suspend($res);
	if($temp == 1){
		$temp = "$domain has been paused!";
	} else {
		$temp = "Something went wrong when pausing $domain, please try again, or look at the logs.";
	}
	echo $temp;
}
function stopVM($domain, $out = true){
	global $conn;
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	$temp = libvirt_domain_destroy($res);
	if($temp == 1){
		$temp = "$domain has been stopped!!";
	} else {
		$temp = "Something went wrong when stopping $domain, please try again, or look at the logs.";
	}
	if($out){ echo $temp; }
}
function removeVM($domain){
	global $conn;
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	$info = libvirt_domain_get_info($res);
	if($info['state'] != 5){
		stopVM($domain);
	}
	$t2 = libvirt_domain_undefine($res);
	$storage = getStorageRes($domain.".qcow2");
	//$t3 = libvirt_storagevolume_delete($storage);
	if($t2 == false){
		echo "There was an error deleting $domain, please check the console or error logs for more details.";
	} else {
		echo "$domain deleted successfully!";
	}
}
function changeVM($domain, $autostart){
	global $conn;
	//vm_auto_current
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	if($autostart == "true"){ $autostart = 1; }else{ $autostart = 0;}
	if(libvirt_domain_set_autostart($res, $autostart)){
		if($autostart == 1){ $autostart = "True"; }else{ $autostart = "False";}
		echo "Auto start has been set to $autostart.";
	} else {
		echo "Change VM failed! Please check logs for more info!";
	}
	
	
}
function screen($domain){
	global $conn, $image;
	
	$hostname = exec("hostname");
	$res = libvirt_domain_lookup_by_name($conn, $domain);
	$data = libvirt_domain_get_screenshot($res, $hostname);
	file_put_contents("test.png", $data);
	//$image = new ImageResize('./screenfirst.png');
	//$image->scale(50);
	//$image->save('./screensecond.png' , IMAGETYPE_PNG);
	echo data_uri("./test.png", "image/png");
	unlink("test.png");
	//unlink("screen_second.png");
}
function data_uri($file, $mime) 
{  
  $contents = file_get_contents($file);
  $base64   = base64_encode($contents); 
  return ('data:' . $mime . ';base64,' . $base64);
}
function createVM($name, $memory, $arch, $cores, $hdd, $iso, $netType, $netDevice, $autostart, $flags = null){
	global $conn;
	if($flags == ""){ $flags = VIR_DOMAIN_FLAG_FEATURE_ACPI; }
	echo "Attempting to build $name <br />";
	echo "Build Virtual Network Device, or Bridge array <br />";
	$net = "";
	if($netDevice != "" & $netType == "bridge"){
		$netType = $netDevice;
		
	} else {
		$net = array("mac" => genMac(), "network" => $netType, "model" => 'rtl8139');
	}
	
	$nets = array( $net );
	$name = preg_replace('/\s+/', '', $name);
	echo "Create Virtual HDD for VM <br />";
	//$hdd_img = libvirt_image_create($conn, $name.".img", $hdd."G", "file");
	$hdd_img = shell_exec("sudo qemu-img create -f file /var/lib/libvirt/images/$name.img $hdd"."G");
	$err = libvirt_get_last_error();
	$storage = getStorageInfo();
	$hdd_path = getStoragePath($name.".img",$storage);
	echo "$hdd_path<br />";
	$hdd_set = array("path" => "$hdd_path", "driver" => 'file', "bus" => 'ide', "dev" => "hda", "size" => $hdd."G",  'flags' => VIR_DOMAIN_DISK_FILE | VIR_DOMAIN_DISK_ACCESS_ALL);
	$hdd_ar = array ($hdd_set);
	if(!$hdd_img){echo "HDD Image creation failed! <br /> $err";}
	echo "ISO: $iso<br />";
	if($iso == "dummy.iso"){
		$iso = "/var/iso/dummy.iso";
	} else {
		$iso = "/var/iso/$iso";
	}
	echo "Adding $iso to VM cdrom...<br />";
	
	$newVM = createXML($name, $arch, $memory, $memory, $cores, $iso, $hdd_set, $net, null);
	if(!$newVM){
		$err = libvirt_get_last_error();
		echo "<br />New VM creation failed! <br /> $err";
	} else {
		//stopVM($name."-install", false);
		echo "<br />VM created! Please Check the dashboard for VM status!<br />";
		if($autostart == "true"){
			$res = libvirt_domain_lookup_by_name($conn, $name);
			if(libvirt_domain_set_autostart($res, 1)){
				echo "Auto start has been enabled!";
			} else {
				echo "Error Setting autostart, please check logs for more info!";
			}
		}
	}
}
function createXML($name, $arch, $memory, $memory, $cores, $iso, $hdd_ar, $nets, $flags = null){
	global $conn;
	$diskPath = $hdd_ar['path'];
	$memory = $memory * 1000;
	$netstr = '';
			if (!empty($nets)) {
					$netstr = "
					    <interface type='bridge'>
					      <mac address='{$nets['mac']}'/>
					      <source bridge='br1'/>
					    </interface>";
			}
	$xml = "<domain type='xen'>
				<name>$name</name>
				<currentMemory>$memory</currentMemory>
				<memory>$memory</memory>
				<os>
					<type arch='x86_64' machine='xenfv'>hvm</type>
					<loader type='rom'>hvmloader</loader>
					<boot dev='cdrom'/>
					<boot dev='hd'/>
				</os>
				<clock offset=\"localtime\"/>
				<on_poweroff>destroy</on_poweroff>
				<on_reboot>destroy</on_reboot>
				<on_crash>destroy</on_crash>
				<vcpu>$cores</vcpu>
				<devices>
					<emulator>/usr/bin/qemu-system-i386</emulator>
					<disk type='file' device='disk'>
                        <source file='$diskPath'/>
                        <target bus='ide' dev='hda' />
                        <address type='drive' controller='0' bus='0' target='0' unit='0'/>
                    </disk>
					<disk type='file' device='cdrom'>
						<driver name='qemu'/>
						<source file='$iso'/>
						<target dev='hdc' bus='ide'/>
						<readonly/>
					</disk>
					$netstr
					<input type='mouse' bus='ps2'/>
					<graphics type='vnc' port='-1' autoport='yes' listen='0.0.0.0' keymap='en-us'/>
					<console type='pty'/>
					<sound model='ac97'/>
					<video>
						<model type='cirrus'/>
					</video>
				</devices>
				</domain>";
	$tmp = libvirt_domain_define_xml($conn, $xml);
	startVM($name);
	return $tmp;
}
function getStorageInfo(){
	global $conn;
	$storage = libvirt_list_storagepools($conn);
	$res = libvirt_storagepool_lookup_by_name($conn, $storage[0]);
	return $res;
}
function getStorageRes($name){
	global $conn;
	$storage = libvirt_list_storagepools($conn);
	$res = libvirt_storagepool_lookup_by_name($conn, $storage[0]);
	$vols = libvirt_storagepool_list_volumes($res);
	$res2 = libvirt_storagevolume_lookup_by_name($res, $name);
	return $res2;
}
function getStoragePath($volume, $pool){
	global $conn;
	libvirt_storagepool_refresh($pool);
	$res2 = libvirt_storagevolume_lookup_by_name($pool, $volume);
	return libvirt_storagevolume_get_path($res2);
}
function genMac(){
	return exec("sudo bash ./bin/macGen.sh");
}
function delDisk($pool, $disk){
	global $conn;
	echo $pool."<br />".$disk;
	$store = libvirt_storagepool_lookup_by_name($conn, $pool);
	var_dump($store);
	echo '<br />';
	$volRes = libvirt_storagevolume_lookup_by_name($store, $disk);
	var_dump($volRes);
	echo '<br />';
	$t3 = libvirt_storagevolume_delete($volRes);
	if(!$t3){
		echo "There was an error trying to delete vDisk $disk! Please look at the logs for more information!";
	} else {
		echo "$disk was deleted successfully!";
	}
}
function delISO($iso){
	if(unlink("/var/iso/$iso")){
		echo "File deleted successfully!";
	} else {
		echo "There was an error deleting the file, please see logs for more information.";
	}
}
?>