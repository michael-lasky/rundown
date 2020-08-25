<?php    
//make this css compatable
//header("Content-type: text/css; charset: UTF-8");

//setup the database connection
$username="root";
$password="";
$host="localhost";
$db="tonsplus";

//connect ot the database or die trying
mysql_connect($host,
$username,
$password);
mysql_select_db($db) OR die("Unable to select database");

//search for th 6453 ID in the print layout table
$colToPrint = mysql_query("SELECT * FROM `tonsplus-columns` WHERE `uid` = 5 AND `name` != 'grabber' AND `visible` = 1 ORDER BY `index` ASC") or die(mysql_error());

//Vie the results in an array
$order_col = array();

//Define each column contents from the array and loop through to show all the columns assoc with the user
for($i = 0; $columns[$i] = mysql_fetch_assoc($colToPrint); $i++) {
	//define the index for each row
	$order_col[$i] = $columns[$i]['index'];
	//define the name for each row
	$order_name[$i] = $columns[$i]['name'];
	//define the width for each row
	$order_width[$i] = $columns[$i]['width'];	
}

//FOR DEV AND TRAINING!!!!
// TONSPLUS_COLUMNS TABLE Original column name from the database - LASKY
$colFFHeders = array(
"Pg#",
 "Title",
 "Segment",
 "approved",
 "Actual",
 "Est Dur",
 "Fnt Time",
 "Bck Time",
 "Camera",
 "TME",
 "slug",
 "Author",
 "Created",
 "Modified",
 "Hard Hit Time",
 "Tape",
 "References",
 "Flt",
 "Tons ID",
 "Item",
 "itemsDur",
 "Keywords",
 "ff10",
 "ff0",
 "ff1",
 "ff2",
 "ff3",
 "ff4",
 "ff5",
 "ff6",
 "ff7",
 "ff8",
 "ff9",
 "Apr",
 "approval",
 "Obj Slug",
 "Status",
 "Chnl");

// PRINT HEADERS Changes the column names from the database show proper labels...no ff - LASKY
$columnNames = array(
"Page",
 "Title",
 "Segment",
 "Approved",
 "Actual Duration",
 "Est Duration",
 "Front Time",
 "Back Time",
 "Camera",
 "TME",
 "Obj Slug",
 "Author",
 "Created",
 "Modified",
 "Hard Hit Time",
 "Tape",
 "References",
 "Float Checkbox",
 "Tons ID",
 "Items",
 "Items Dur",
 "Keywords",
 "Ticker",
 "Anchor",
 "Production Notes",
 "Writer",
 "Editor",
 "Source",
 "Notes",
 "Graphics",
 "Shot",
 "Anchor",
 "Production Notes",
 "Approved Checkbox",
 /*approval color bar*/ " ",
 "Object Slug",
 "Status",
 "Channel");

// $AllStoryInfo names for the content of the cells. Changes the text from the rundown layout the show proper labels. Make sure this comes from one of the STORY-INDEX, STORIES, STOYRRYPARAMS, or ITEMS TABLE - LASKY
$colDBHeders = array(
"PageNum",
 "title",
 "segment",
 "approved",
 "Actual",
 "EstTime",
 "FrontTime",
 "EndTime",
 "camera",
 "TME",
 "slug",
 "author",
 "created",
 "modified",
 "HardHitTime",
 "Tape",
 "phanchor",
 "Float",
 "objPath",
 "type",
 "itemsDur",
 "phanchor",
 "ff10",
 "ff0",
 "ff1",
 "ff2",
 "ff3",
 "ff4",
 "ff5",
 "ff6",
 "ff7",
 "ff8",
 "ff9",
 "Approved",
 /* matching the approval column so the status bar wil show red or green if i can get that working---> */ "Approved",
 "slug",
 "objAir",
 "mosChannel");

//Replace the database names with user priendly print names in the column headers
$order_nameH = str_replace($colFFHeders,
 $columnNames,
 $order_name);

//close php
?>