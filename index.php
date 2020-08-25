<?php
//Connect to the databsae - MCFERRIN
header("Content-Type:text/html");

$username="root";
$password="";
$host="localhost";
$db="tonsplus";

mysql_connect($host,$username,$password);
@mysql_select_db($db) OR die("Unable to select database");

?>

<!-- Some CSS for the rundown display. Odd and Even will be for alternting rows in the rundown.   -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <META HTTP-EQUIV="refresh" CONTENT="10"> -->
		<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
			<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
				<link rel="icon" href="favicon.ico" type="image/x-icon">
				<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
				<title>OPUS Rundown</title>
				<!-- Bootstrap -->
				<link href="css/bootstrap.min.css" rel="stylesheet">
				<!-- link rel='stylesheet' type='text/css' href='layout.php' / hmm... why? -->
				<link href="css/custom.css" rel="stylesheet">
    

				<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
				<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
				<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
				<![endif]-->

				<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
				<!-- jQuery and bootstrap moved into <head> for autorefresh since it uses jQuery and needs it immediately upon page load   - COX -->
				<script src="js/jquery-1.11.1.min.js"></script>
				
				<!-- Include all compiled plugins (below), or include individual files as needed -->
				<script src="js/bootstrap.min.js"></script>
				
				<!-- Auto refresh script -->
				<!-- Script & htmlrundown file split by Gary Cox <gary.cox@fox40.com> -->
				<script>
				function autoRefresh(){
					if($('#rid').val() > 0) { // <!-- Don't refresh if nothing is selected - MCFERRIN -->
						content_url = "content.php?rid=" + $('#rid').val();
						$.get(content_url, function(data) {
						$("#content").replaceWith(data);
						});
						if(document.getElementById('af').checked) {
							$('#timing').scrollView();
						}
					}
					
				};
				// autoRefresh(); NOT NEEDED WITH ABOVE REFRESH CODE - MCFERRIN
				// Change this to desired refresh interval in milliseconds (e.g. 5000 = 5 seconds) - COX
				window.setInterval(function() { autoRefresh(); }, 1000);
				
				function autoRefreshAF(){
						$("#rid").load("dropdown.php?rid="+$('#rid').val());
				};
				// autoRefresh(); NOT NEEDED WITH ABOVE REFRESH CODE - MCFERRIN
				// Change this to desired refresh interval in milliseconds (e.g. 5000 = 5 seconds) - COX
				window.setInterval(function() { autoRefreshAF(); }, 10000);
				</script>
				
				<!-- This Auto refresh script uses jQuery functions $.get() and $.replaceWith() 
				More info at:
				http://api.jquery.com/get/
				http://api.jquery.com/replacewith/
				The standard JS window.setInterval is used to repeatedly run the refresh
				-->
				<!-- set auto follow for the timing bar - LASKY
				<script>
				window.onscroll = function() {myFunction()};

				function myFunction() {
				if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
				document.getElementById("myP").className = "yellow";
				} else {
				document.getElementById("myP").className = "";
				}
				}
				</script>
				-->
		</head>
		<body>
			<body>
				<div class="nav-default">
						<div class="nav-default-inner">
										
							<a class="navbar-brand" href="./"><img src="opuslogo.png" style="width: 40px; top: 40px; margin-top:-10px;"></a>
							<lable><select id="rid">
								  <option>Please choose</option>
							</select>&nbsp;<span class="glyphicon glyphicon-refresh" id="refresh" style="color: #EEE;"></span></label>
							<label id="af_label"><input type="checkbox" id="af" value="1">&nbsp;Auto Follow</label>
						</div>
				</div>

				<div id="wrapper">
					<div id="content">
							<h1><center>Pick from the menu</center></h1>
					</div>
				</div>

			</div>
			<script>
				 	$("#rid").load("dropdown.php");
					$("#rid").change(function() {
						content_url = "content.php?rid=" + $('#rid').val();
						$.get(content_url, function(data) {
							$("#content").replaceWith(data);
						});
					});
					
					$.fn.scrollView = function () {
						return this.each(function () {
							$('html, body').animate({
								scrollTop: $(this).offset().top-135
								}, 300);
						});
					}
					
					$('#refresh').click(function(){
						$("#rid").load("dropdown.php");
					});
			</script>
	</body>
</html>
<!-- Not sure why we had Auto Refresh twice was same code as above - MCFERRIN -->