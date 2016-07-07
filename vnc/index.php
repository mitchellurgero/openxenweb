<?php
session_start();
if(!isset($_SESSION['username'])){
	//die("You must be logged in to view this page!");
}
genVNC($_GET['host'], $_GET['port']);

function genVNC($ip, $port){
	echo '';
	?>
	<html>
		<head>
			<title>OpenVirt VM Console</title>
		</head>
	<body>
		<object id='Flashlight' width='100%' height='100%' type='application/x-shockwave-flash' data='Flashlight.swf'>
			<param name='movie' value='Flashlight.swf'/>
			<param name='allowScriptAccess' value='always'/>
			<param name='allowFullscreen' value='true'/>
			<param name='wmode' value='opaque'/>
			<param name=FlashVars value='host=<?php echo $ip; ?>&port=<?php echo $port; ?>&autoConnect=true&viewOnly=false&hideSettings=false&scale=true&shared=false'/>
		</object> 
	</body>
	</html>
	<?php
}

?>