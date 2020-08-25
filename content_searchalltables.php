<?php
header("Content-Type:text/html");
//Connect to to the database - MCFERRIN
$username="root";
$password="";
$host="localhost";
$db="tonsplus";

     mysql_connect($host,$username,$password);
    @mysql_select_db($db) OR die("Unable to select database");

?>

<div id="content">

<?php
//Get the active rundowns from the Opus server - MCFERRIN
$go=0;
if($_GET['rid'] > 0){
                   $r['id'] = $_GET['rid'];
		   $RundownActive = mysql_one_data("SELECT `active` FROM `tonsplus-rundowns` WHERE `id`=" .$r['id']. " LIMIT 1");
		   $RundownTitle = mysql_one_data("SELECT `title` FROM `tonsplus-rundowns` WHERE `id`=" .$r['id']. " LIMIT 1");
//this is the column in the rundow table that keeps trak of the timing bar. currentID is the story that's highlighted yellow in the rundown - LASKY
		   $Current = mysql_one_data("SELECT `CurrentID` FROM `tonsplus-rundowns` WHERE `id`=" .$r['id']. " LIMIT 1");
}

//Display the rundown title on the HTML page - MCFEERRIN
if($_GET['rid'] && $RundownActive){
         echo "<span class=\"navselectedrundown\">$RundownTitle</span>";


//Need info from the tonsplus-story-index table as that's where the rundown info is stored - MCFERRIN
              $si_q = "SELECT * FROM `tonsplus-story-indexs` WHERE `rid` = ".$r['id']." ORDER BY `index`";
              $si_d = mysql_query($si_q);
?>

<!-- Get the table set up with headers. Also, make it 100% so it works both portrait and landscape  - MCFERRIN/LASKY-->
<table class="rundown" border="1px" width="100%";>

<!-- 
This brings in the file for the layout of the table to come from the user 6453 print layout - LASKY
 -->
<?php include 'layout.php'; ?>

<!-- 
Table uses the columns from user 6453's print layout to format the columns from layout.php and we use colH becaue that comes from the replace text in the layout.php - LASKY
 -->
<th width="<?php echo $wide0;?>"><?php echo $colH0;?></th>
<th width="<?php echo $wide1;?>"><?php echo $colH1;?></th>
<th width="<?php echo $wide2;?>"><?php echo $colH2;?></th>
<th width="<?php echo $wide3;?>"><?php echo $colH3;?></th>
<th width="<?php echo $wide4;?>"><?php echo $colH4;?></th>
<th width="<?php echo $wide5;?>"><?php echo $colH5;?></th>
<th width="<?php echo $wide6;?>"><?php echo $colH6;?></th>
<th width="<?php echo $wide7;?>"><?php echo $colH7;?></th>
<th width="<?php echo $wide8;?>"><?php echo $colH8;?></th>
<th width="<?php echo $wide9;?>"><?php echo $colH9;?></th>
<?php


//SEARCH THE STORY-INDEX TABLE FOR RUNDOWN INFO. This makes the rundown lines alternate between white and blue. Every other line will be blue - MCFERRIN
				$y = 0;
				while($s = mysql_fetch_array($si_d)) {
				   $PageNumber =  mysql_one_data("SELECT `index` FROM `tonsplus-story-indexs` WHERE `id`=" .$s['id']. " LIMIT 1");
                if(substr($PageNumber,0,1)) {
                	if ($y % 2 == 0) { $oe = "even"; } else { $oe = "odd"; 
                	}
                   
//  Here's where the rundown info gets displayed. Choose the columns and variables from the database to display from here. - MCRFERRIN/LASKY

//Strip all the bad stuff out and replace it with readable text and characters - LASKY   
				$BadText = array("\r\n" , "\r" , "\n" , "&#13;", "&1;", "&5;", "&2;", "&4;", "\"");
				$GoodText = array("<br />", "<br />", "<br />", "<br />", "&#39;", "*", "'", '"', "\\");
				
				//$AllInfo = mysql_query("SELECT tonsplus-stories.*, tonsplus-story-indexs.*, tonsplus-items.*, tonsplus-story-params.* FROM user u
// 				
 				//$AllInfo = mysql_query("SELECT tonsplus-stories.* AS Stories, tonsplus-story-indexs.* AS Index, tonsplus-story-params.* AS Param WHERE `id`=" .$s['id']. " LIMIT 1");


    // Get all the stories
	//$AllStoryInfo = array();
	
    $StoryStuff = "SELECT a.*, b.*, c.*, d.* FROM `tonsplus-story-indexs` AS a";
    $StoryStuff .= " LEFT JOIN `tonsplus-stories` AS b ON a.id = b.id";
    $StoryStuff .=  " LEFT JOIN `tonsplus-story-params` AS c ON a.id = c.id";
    $StoryStuff .=   " LEFT JOIN `tonsplus-items` AS d ON a.id = d.sid";
    $StoryStuff .=    " WHERE a.`id`=" .$s['id']. " LIMIT 1";
    
    $AllStoryStuff = mysql_query($StoryStuff) or die(mysql_error());;
    
    $AllStoryInfo = mysql_fetch_assoc($AllStoryStuff);
    print_r($AllStoryInfo);
    
//     $query .= " WHERE a.`rid` = '".$rid."' AND b.`Version` = 0 AND c.`Float` = 0 AND c.`Zoo`= 0 ORDER BY a.`index`;"; 
//     //echo($query . "\n\n");
//                                             
//     $result = mysql_query($query);
//     if($result && mysql_numrows($result)>0) {
//         
// 		$count = 0;
//         for ($b=0; $b<mysql_numrows($result); $b++) {
// 			$item = array();
// 			$id = mysql_result($result,$b,"id");
//             $pageNum = mysql_result($result,$b,"PageNum");
// 			$break = mysql_result($result,$b,"break");
// 			
// 			//if (substr($pageNum, 0, 1) != "Z") {  
// 				$script = GetStoryScript($id, "true"); 
// 				$cleanScript = cleanupScript($script);
// 				if ($hideBlankScripts == 'true') {
// 				//Allow breaks in the list of stories
// 					if ( ($cleanScript != "") || ($break) ){
// 						$stories[$count] = $id;
// 						$count++;
// 					}
// 				}
// 				else {
// 					$stories[$count] = $id;
// 					$count++;
// 				}
// 			//}
//         }
//     } 
// 	//else {
// 	//	return '';
// 	//}
// 
//     return $stories;
// }



// SEARCH THE STORIES TABLE				
// Get all rows from the stories table			
// 				$Stories = mysql_query("SELECT * FROM `tonsplus-stories` WHERE `id`=" .$s['id']. " LIMIT 1");
// 				
// makes use of the column name instead of the array number $Stories['title'] not $Story[0] - LASKY/LASHER
// 				$Story = mysql_fetch_assoc($Stories);
// 				
// SEARCH THE STORY-INDEXS TABLE FOR STORY INFO
// Get the Varibale from the table in the database and create the array - LASKY
// 				$Index =  mysql_query("SELECT * FROM `tonsplus-story-indexs` WHERE `id`=" .$s['id']. " LIMIT 1");
// 				
// makes use of the column name instead of the array number $StoryParam['title'] not $StoryParam[0] - LASKY/LASHER
// 				$StoryIndex = mysql_fetch_assoc($Index);				
// 
// SEARCH THE STORY-PARAM TABLE
// Get the Varibale from the table in the database and create the array - LASKY
// 				$Param =  mysql_query("SELECT * FROM `tonsplus-story-params` WHERE `id`=" .$s['id']. " LIMIT 1");
// 				
// makes use of the column name instead of the array number $StoryParam['title'] not $StoryParam[0] - LASKY/LASHER
// 				$StoryParam = mysql_fetch_assoc($Param);
// 				
// 				SEARCH THE ITEMS TABLE mostly for the TONS Channel and Status - USES SID no ID and needs to be serperate from the combined searc
// Get the Varibale from the table in the database and create the array - LASKY
// 				$Items =  mysql_query("SELECT * FROM `tonsplus-items` WHERE `sid`=" .$s['id']. " LIMIT 1");
// 				
// makes use of the column name instead of the array number $StoryParam['title'] not $StoryParam[0] - LASKY/LASHER
// 				$StoryItems = mysql_fetch_assoc($Items);
				

//Replace bad text with good text from above	- LASKY		
				$StoryParam = str_replace($BadText, $GoodText, $StoryParam);

//Let's define some needed varialbles
//Need to get the times into hh:mm:ss - LASKY
				$EstTime = date('i:s', $AllStoryInfo['Estimated']);
				$FrontTime = date('h:i:s', $AllStoryInfo['StartTime']);
								
//Define the Title
				$Title = $Story['title'];
				
				$PageNum = $AllStoryInfo['PageNum'];

				
				
				

//Put the column text into a varible so I don't have to change each line every time -LASKY
				$printLayout = 
				'<td><b>'. $PageNum .'</b></td>
				<td>'. $Title .'</td>
				<td>'. $AllStoryInfo[$col2] .'</td>
				<td>'. $AllStoryInfo[$col3] .'</td>
				<td>'. $AllStoryInfo[$col4] .'</td>
				<td>'. $AllStoryInfo[$col5] .'</td>
				<td>'. $AllStoryInfo[$col6] .'</td>
				<td>'. $AllStoryInfo[$col7] .'</td>
				<td>'. $EstTime .'</td>
				<td>'. $FrontTime .'</td>';

				
//This will print the specific columns into the grid - MCFERRIN/LCOX/LASKY 
// Is this the current story?  If so, it needs to be yellow - LASKY/COX
					if ($Current == $StoryParam['id']) {  
						print "<tr class=\"timing\" height=\"25px\">". $printLayout ."</tr>\n"; 
						$y++;
						

//Or Prints the break line in purple - MCFERRIN/LASKY					
					} else {  
						if($Story['break']) { 
						print "<tr class=\"break\"  height=\"25px\">". $printLayout ."</tr>\n"; 
						$y++;
							

//Or Prints the floated lines in red - LASKY
					} else {
						if($StoryParam['Float']==1) {
						 print "<tr class=\"float\"  height=\"25px\">". $printLayout ."</tr>\n"; 
						 $y++;
						 
						 
//Or removes the story if moved to the Zoo or deleted - LASKY
					} else {
						if($StoryParam['Zoo']==1) {
						 $y=0;
						 
						 
//Or removes the story if moved to the Zoo or deleted - LASKY
// 					} else {
// 						if($StoryParam['Zoo']==1 and $PageNumber !== "Z") {
// 						print "<tr class=\"zoo\"  height=\"25px\">". $printLayout ."</tr>\n"; 
// 						 $y++;
						 
						 
// Print the lines alternating colors so it's easier to read on the grid - MCFERRIN/LASKY
 					} else {							
						print "<tr class=\"" . $oe ."\" height=\"25px\">". $printLayout ."</tr>\n"; 
						$y++;
								   //}
						       }
				 			}
						}
					}
                }
              }
              
               echo "</table><br><br>";
    } elseif($_GET['rid']) {
?>

<!-- If the rundowns aren't active, or get deactivated, need a message telling the user to pick a new one  - MCFERRIN-->
<div class="inner"><span class="glyphicon glyphicon-chevron-up icon-white"></span>
<h3>Rundown not active, select new from above</h3>
  </div>
<?
    } else {
?>
<div class="inner"><span class="glyphicon glyphicon-chevron-up icon-white"></span>
<h3>Select rundown from the menu above</h3>
  </div>
<?
}


//This returns one field from the query quickly  - MCFERRIN
function mysql_one_data($query)
{
   $one=mysql_query($query);
   $r=mysql_fetch_row($one);
   return($r[0]);
}

?>
  </div>