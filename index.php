<?php
session_start();
include("functions.php");
include("config.php");
//Check if login
if(isset($_POST['username'])){
	$u = $_POST['username'];
	$p = $_POST['password'];
	$res = auth($u, $p);
	if($res){
		$_SESSION['username'] = $u;
		$_SESSION['p'] = $p;
	} else {
		$_SESSION['error'] = "Invalid username or password, please try again!";
	}
}
//Check Session is available
head();
if(isset($_SESSION['username'])){
	body();
} else {
	loginPage();
}
foot();
function head(){
	echo '';
	?>
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>OpenVirt | Server Web Management</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/custom.css" rel="stylesheet">
		<script src="js/jquery-1.12.3.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</head>
	<?php
}
function loginPage(){
	echo '';
	?>
	<br />
	<br />
	<br />
	<br />
	<br />
	
	<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 text-center">
			<h1>OpenVirt Manager <text class="">|</text> <small><?php echo exec("hostname");?></small></h1>
			<form role="form" method="POST" action="index.php">
				<fieldset class="form-group">
    				<label for="username" class="col-md-2">Username</label>
    				<input type="username" class="form-control" id="username" name="username" placeholder="Enter Username" >
  				</fieldset>
  				<fieldset class="form-group">
    				<label for="password" class="col-md-2">Password</label>
    				<input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
  				</fieldset>
  				<fieldset class="form-group">
  					<button type="submit" class="btn btn-primary pull-right">Sign In</button>
  				</fieldset>
  				<fieldset class="form-group">
  					<p style="color:red"><?php if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']); } ?></p>
  				</fieldset>
			</form>
		</div>
		<div class="col-md-6 col-md-offset-3 text-center">
			<p><small>Copyright &copy; 2016 URGERO.ORG</small></p>
		</div>
	</div>
	</div>
	<?php
}
function body(){
	echo '';
	?>
	<body>
	<script>window.onload = function () { pageLoad("dashboard"); }</script>
	<div class="container">
		 <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./">OpenVirt Web Management</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="./">Dashboard</a></li>
              <li><a href="#" onclick="pageLoad('network')">Network</a></li>
              <li><a href="#" onclick="pageLoad('storage')">Storage</a></li>
              <li><a href="#" onclick="pageLoad('iso')">ISOs</a></li>
              <li><li class="divider"></li></li>
              <li><a href="#" onclick="pageLoad('users')">Users</a></li>
              <li><a href="#" onclick="pageLoad('shell')">Web Shell</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="./logout.php"><span class="glyphicon glyphicon-user"></span> Logout <?php echo $_SESSION['username'];?></a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
	</div>
	<div class="container">
		<div id="pageContent">
		</div>
	</div>
<!-- General Modal for info's/warning's/error's -->
		<div class="modal " id="genModal">
  			<div class="modal-dialog">
    			<div class="modal-content">
      			<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        			<h4 class="modal-title" id="genModalHeader">Modal title</h4>
      			</div>
      			<div class="modal-body" id="genModalBody">
        			<p>One fine body…</p>
      			</div>
      			<div class="modal-footer">
        			<button type="button" class="btn btn-raised btn-primary" data-dismiss="modal">Close Message</button>
      			</div>
    			</div>
  			</div>
		</div>
		<div class="modal " id="newVM">
  			<div class="modal-dialog">
    			<div class="modal-content">
      			<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        			<h4 class="modal-title" id="newVMHead">New Virtual Machine</h4>
      			</div>
      			<div class="modal-body" id="newVMBody">
        			<p>
        				<table class="table">
        					<tr><td>VM Name</td><td><input type="text" class="form-control" id="vm_name" name="vm_name" placeholder="VM Name" ></input></td></tr>
        					<tr><td>Memory(MB)</td><td><input type="number" class="form-control" id="vm_mem" name="vm_mem" placeholder="Max Memory" value="1024" max="<?php echo round(exec("cat /proc/meminfo | grep MemTotal | awk '{ print $2 }'") / 1000);?>" min="1"></input></td></tr>
        					<tr><td>CPU Architecture</td><td><select id="vm_arch" name="vm_arch" class="form-control"><option value="i686">x86</option><option value="x86_64">x64</option></select></td></tr>
        					<tr><td>Network</td><td><select id="vm_net" name="vm_net" class="form-control">
        						<?php
        						$nets = getKVMNet();
        						foreach($nets as $net){
        							echo '<option value='.$net.'>'.$net.'</option>';
        						}
        						?>
        						<option value="dev">DEVICE</option></select><br /><input type="text" class="form-control" id="vm_net_dev" name="vm_net_dev" placeholder="Adapter name(Only if 'Device' is selected)"></input></td></tr>
        					<tr><td>vCPU Cores</td><td><input type="number" class="form-control" id="vm_core" name="vm_core" value="1" max="<?php echo exec("cat /proc/cpuinfo | grep processor | wc -l"); ?>" min="1"></input></td></tr>
        					<tr><td>HDD Size(GB)</td><td><input type="number" class="form-control" id="vm_hdd" name="vm_hdd" value="30" min="1" max="10000"></input></td></tr>
        					<tr><td>Installation ISO</td><td><select id="iso" name="iso" class="form-control"><option value="dummy.iso">NONE</option>
        						<?php
									$logfile = 'virt_functions.log';
    								if (!libvirt_logfile_set($logfile)){die('Cannot set the log file');}
										$conn = libvirt_connect('xen:///', false);
										$iso = getFile("/var/iso");
										foreach($iso as $img){
											if($img == "dummy.iso") { continue; }
											//$fs = round(filesize("/var/iso/$img") / 1024 / 1024,1);
											echo '<option value="'.$img.'">'.$img.'</option>';
										}
								?>
        					</select></td></tr>
        					<tr><td>Autostart with Host</td><td><input id="vm_auto" name="vm_auto" type="checkbox" value="autostart"></td></tr>
        					<tr><td>Flags(Advanced)</td><td><input type="text" class="form-control" id="vm_flags" name="vm_flags" placeholder="Advanced Options"></input></td></tr>
        				</table>
        				<button class="btn btn-primary pull-right" onClick="createVM()">Create VM</button>
        				<br />
        				<br />
        			</p>
      			</div>
      			<div class="modal-footer">
        			<button class="btn btn-warning" onClick="clearNewVM()">Clear</button>&nbsp;<button type="button" class="btn btn-raised btn-primary" data-dismiss="modal">Close</button>
      			</div>
    			</div>
  			</div>
		</div>
		<!--<div id="coverlay"></div>-->
		<div class="loading" id="loadAnim">Loading&#8230;</div>
	</body>
	<?php
}
function foot(){
	echo '';
	?>
	<foot>
		<script src="js/functions.js"></script>
		<script>
			 function pageLoad(page){
 				document.title = "Loading..."
 				document.getElementById("pageContent").innerHTML = "<p>Loading " + page + ", Please wait...</p>";
 				load(true);
 				$.ajax({
					method:'post',
					url:'./page.php',
					data:{
						page:page
					},
					success:function(result) {
						document.getElementById("pageContent").innerHTML = result;
						document.title = capitalizeFirstLetter(page);
						load(false);
					}
					}).fail(function(e) {
						document.getElementById("pageContent").innerHTML = "Loading the page failed. Please try again.";
						genModal("Error", "Loading the page failed. Please try again.");
						load(false);
					});
			}
			function load(type){
				if(type === true){
					$("#coverlay").show();
					document.getElementById("loadAnim").style.display = '';
				} else {
					$("#coverlay").hide();
					document.getElementById("loadAnim").style.display = 'none';
				}
			}
			function genModal(head, body){
				document.getElementById("genModalHeader").innerHTML = head;
				document.getElementById("genModalBody").innerHTML = body;
				$("#genModal").modal('show');
			}
		</script>
		
	</foot>
	</html>
	<?php
}

?>