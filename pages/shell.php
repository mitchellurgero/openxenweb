<?php
//Check for valid session:
include('functions.php');
if(!isset($_SESSION['username'])){
	die("You must be logged in to view this page!");
}
echo '';
?>
<div class="row">
    <div class="col-lg-12">
        <h3>Web Shell | <small>Simple Web Console</small></h3>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="frame">
    		<iframe src="pages/web-console.php">
	    		Your browser does not support inline frames.
    		</iframe>
		</div>
	</div>
</div>