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



function capitalizeFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}
function vmInfo(vm){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"info",
			domain:vm
		},
		success:function(result) {
			genModal(vm,result)
			load(false);
		}
		}).fail(function(e) {
			
			load(false);
		});
}
function startVM(vm){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"start",
			domain:vm
		},
		success:function(result) {
			genModal(vm,result);
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			
			load(false);
		});
}
function stopVM(vm){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"stop",
			domain:vm
		},
		success:function(result) {
			genModal(vm,result);
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			
			load(false);
		});
}
function pauseVM(vm){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"pause",
			domain:vm
		},
		success:function(result) {
			genModal(vm,result);
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			
			load(false);
		});
}
function deleteVM1(vm){
	vm = "'" + vm + "'";
	genModal("Delete " + vm + "?","Are you sure you want to delete " + vm + "?<br />Note: This will not delete the VM's hard drive.<br /><br />" + '<div class="pull-right"><button class="btn btn-sm btn-danger" onClick="deleteVM2(' + vm + ')">Yes, Please delete the VM</button></div><br /><br />');
}
function deleteVM2(vm){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"delete",
			domain:vm
		},
		success:function(result) {
			genModal(vm,result);
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			
			load(false);
		});
}
function delDisk1(pool, disk){
	pool = "'" + pool + "'";
	disk = "'" + disk + "'";
	genModal("Delete " + disk + "?", "Are you sure you want to delete the vDisk " + disk + "?<br />Note: This is permanent, and cannot be undone!<br /><br />" + '<div class="pull-right"><button class="btn btn-sm btn-danger" onClick="delDisk2(' + pool + ',' + disk + ')">Yes, Please delete the vDisk</button></div><br /><br />');
}
function delDisk2(pool, disk){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"delDiskImg",
			pool_name:pool,
			disk_name:disk
		},
		success:function(result) {
			genModal(disk,result);
			load(false);
			pageLoad("storage");
		}
		}).fail(function(e) {
			genModal(disk,e);
			load(false);
		});
}
function delISO(iso){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"delISO",
			file:iso
		},
		success:function(result) {
			genModal("File delete: " + iso,result);
			load(false);
			pageLoad("iso");
		}
		}).fail(function(e) {
			genModal(disk,e);
			load(false);
		});
}
function getScreen(vm){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"screen",
			domain:vm
		},
		success:function(result) {
			genModal(vm,"<div class=\"text-center\"><img src=" + result + " width=\"75%\"></img></div>");
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			
			load(false);
		});
}
function clearNewVM(){
	document.getElementById("vm_hdd").value = "";
	document.getElementById("vm_name").value = "";
	document.getElementById("vm_mem").value = "";
	document.getElementById("vm_core").value = "";
	document.getElementById("vm_net_dev").value = "";
	document.getElementById("vm_flags").value = "";
}
//$_POST['vm_net_dev'], $_POST['vm_flags']
function createVM(){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"create",
			vm_name:document.getElementById("vm_name").value,
			vm_mem:document.getElementById("vm_mem").value,
			vm_arch:$("#vm_arch").val(),
			vm_core:document.getElementById("vm_core").value,
			vm_hdd:document.getElementById("vm_hdd").value,
			vm_net:document.getElementById("vm_net").value,
			vm_net_dev:document.getElementById("vm_net_dev").value,
			vm_flags:document.getElementById("vm_flags").value,
			iso:$("#iso").val(),
			vm_auto:document.getElementById("vm_auto").checked
		},
		success:function(result) {
			genModal("Create VM Log","<p>" + result + "</p>");
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			genModal("Create VM Log","<p>" + e + "</p>");
			load(false);
		});
		$('#newVM').modal('hide');
}
function changeVM(domain){
	load(true);
 	$.ajax({
		method:'post',
		url:'./virt.php',
		data:{
			func:"changeVM",
			vm_name:domain,
			vm_auto_current:document.getElementById("vm_auto_current").checked
		},
		success:function(result) {
			genModal("Change VM Settings: " + domain,"<p>" + result + "</p>");
			load(false);
			pageLoad("dashboard");
		}
		}).fail(function(e) {
			genModal("Create VM Log","<p>" + e + "</p>");
			load(false);
		});
}
function changePwd(username){
	document.getElementById("changeUserPasswd").innerHTML = username;
	document.getElementById("changePwdModalFooter").innerHTML = '<button type="button" class="btn btn-raised btn-danger" data-dismiss="modal">Cancel</button> <button type="button" class="btn btn-raised btn-primary" id="changePasswdModalBtn" onClick="changePasswd();">Change Password</button>';
	$("#changePwdModal").modal('show');
}
function delUser(username){
    			document.getElementById("delUserModalBody").innerHTML = "Are you sure you want to delete " + username + "?";
    			document.getElementById("delUserModalFooter").innerHTML = '<button type="button" class="btn btn-raised btn-danger" data-dismiss="modal">Cancel</button> <button type="button" class="btn btn-raised btn-primary" id="delUserModalBtn" onClick="deleteUser(\'' + username + '\');">Delete User</button>';
    			$("#delUserModal").modal('show');
}
function addUser(){
	
	var username = document.getElementById("newUser1").value;
	var password = document.getElementById("newPasswd1").value;
	var password2 = document.getElementById("newPasswd2").value;
	if(username == ""){
		genModal("Error!", "Username cannot be blank!")
	} else {
		load(true);
		if(password == password2){
			$.ajax({
				method:'post',
				url:'./bin/users.php',
				data:{
					type:'add',
					username:username,
					password:password
				},
				success:function(result) {
					genModal("Results", "<pre>" + result + "</pre>");
					load(false);
					$("#newUserModal").modal('hide');
					pageLoad('users');
				}
				}).fail(function(e) {
					document.getElementById("pageContent").innerHTML = "Loading the page failed. Please try again.";
					load(false);
				});
		} else {
			genModal("Error", "Passwords do not match, try again!");
			load(false);
		}
	}
}
function deleteUser(username){
	$("#delUserModal").modal('hide');
		load(true);
		$.ajax({
			method:'post',
			url:'./bin/users.php',
			data:{
				type:'del',
				username:username
			},
			success:function(result) {
				genModal("Results", "<pre>" + result + "</pre>");
				load(false);
				pageLoad('users');
			}
			}).fail(function(e) {
				document.getElementById("pageContent").innerHTML = "Loading the page failed. Please try again.";
				load(false);
			});
}
function changePasswd(){
	load(true);
	var username = document.getElementById("changeUserPasswd").innerHTML;
	var password = document.getElementById("newPasswd3").value;
	var password2 = document.getElementById("newPasswd4").value;
	if(password == password2){
		$("#changePwdModal").modal('hide');
		$.ajax({
			method:'post',
			url:'./bin/users.php',
			data:{
				type:'change',
				username:username,
				password:password
			},
			success:function(result) {
				genModal("Results", "<pre>" + result + "</pre>");
				load(false);
				pageLoad('users');
			}
			}).fail(function(e) {
				document.getElementById("pageContent").innerHTML = "Loading the page failed. Please try again.";
				load(false);
			});
	} else {
		genModal("Error", "Passwords do not match, try again!");
		load(false);
	}


}

