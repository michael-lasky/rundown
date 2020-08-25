<?php

$username="root";
$password="";
$host="localhost";
$db="tonsplus";

	
$dbhandle = mysql_connect($host, $username, $password) or die("Unable to connect to MySQL");
$selected = mysql_select_db("tonsplus", $dbhandle) or die("Could not select examples");

//We don't want EVERY rundown loaded because then we would get old HOLDS rundowna and the like. So let's just get the rundowns from this week. Since the start date is the timestamp, we need to converrt it from timestamp and then use it.  Then order by the date with the newest on top
$rd_q = "SELECT * FROM `tonsplus-rundowns` WHERE YEARWEEK(FROM_UNIXTIME(`StartTime`)) = YEARWEEK(NOW()) AND`active` = 1 ORDER BY `StartTime` DESC";
$rd_d = mysql_query($rd_q);

$output = "";
$once = 0;
while($r = mysql_fetch_array($rd_d)) {
if($r['id'] > 4 ) {
	if(@$_GET['rid'] == $r['id']) { $selected = " selected"; $once = 1; } else { $selected = ""; }
 	$list[$r['id']] = $r['title'];
	$output .= "<option value=\"".$r['id']."\"".$selected.">".$r['title']."</option>\n";
}
}
	if(!$once) { $selected = " selected"; } else { $selected = ""; }
	$output .= "<option value=\"".$r['id']."\"".$selected.">Select Rundown</option>\n";

print $output;
//print json_encode($list);
?>