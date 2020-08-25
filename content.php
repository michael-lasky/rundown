<?php
header("Content-Type:text/html");
//Connect to to the database - MCFERRIN
$username="root";
$password="";
$host="localhost";
$db="tonsplus";

mysql_connect($host,$username,$password);
@mysql_select_db($db) OR die("Unable to select database");


?><div id="content"><?php
	//Check if 'rid' was passed from the main page, if not give instructions to pick a rundown and exit script - MCFERRIN
	if(strlen(@$_GET['rid']) < 1) { print "<h1><center>Pick from the menu</center></h1>"; exit; }

	//Get the active rundowns from the Opus server - MCFERRIN
	$go=0;
	if($_GET['rid'] > 0){
        $r['id'] = $_GET['rid'];
               
		//Select all from the rundown table - LASKY
		$RundownInfo = "SELECT * FROM `tonsplus-rundowns` WHERE `id`=" .$r['id']. " LIMIT 1";
	} 
	 
	//Do the search - LASKY
	$AllRundownStuff = mysql_query($RundownInfo) or die(mysql_error());;
    
	//Get the column names so the items can use names instead of numbers - LASKY  
	$AllRundownInfo = mysql_fetch_assoc($AllRundownStuff);
    	   
	//Get the active rundowns - LASKY
	$RundownActive = $AllRundownInfo['active'];
		   
	//Get the runown titles - LASKY
	$RundownTitle = $AllRundownInfo['title'];	
		
	//Are the page numbers frozen? - LASKY
	$RundownFreeze = $AllRundownInfo['FreezePages'];

	//Display the rundown title on the HTML page - MCFEERRIN/LASKY
	//if($_GET['rid'] && $RundownActive){
	if($_GET['rid']){
		//echo "<span class=\"navselectedrundown\">   Now viewing $RundownTitle </span>";

		//Need info from the tonsplus-story-index table as that's where the rundown info is stored - MCFERRIN
		$si_q = "SELECT * FROM `tonsplus-story-indexs` WHERE `rid` = ".$r['id']." ORDER BY `index`";
		$si_d = mysql_query($si_q);
		// Get the table set up with headers. Also, make it 100% so it works both portrait and landscape  - MCFERRIN/LASKY-
		?>
		<table class="rundown" border="1px" width="100%">
		<?php
		// This brings in the file for the layout of the table to come from the user 6453 print layout - LASKY
		include 'layout.php'; 

		//Get the columns from the rundown layout and print the titles in the table along with the width of each column order_nameH comes from the string replace in layout.php. - MCFERRIN/LASKY
		for($x=0; $x<$i; $x++ ){
			print "<th width=" . $order_width[$x] . ">" . $order_nameH[$x] . "</th>\n" ;
		}

		//SEARCH THE STORY-INDEX TABLE FOR RUNDOWN INFO. This makes the rundown lines alternate between white and gray. Every other line will be blue - MCFERRIN
		$y = 0;
		while($s = mysql_fetch_array($si_d)) {


			//$PageNumberIndex =  mysql_one_data("SELECT `index` FROM `tonsplus-story-indexs` WHERE `id`=" .$s['id']. " LIMIT 1");
			$PageNumberIndex = $si_q['index'];
			if(substr($PageNumberIndex,0,1)) {
				if ($y % 2 == 0) { $oe = "even"; } else { $oe = "odd"; 
				}
                   
				//Strip all the bad stuff ($BadText) out and replace it with readable text and characters ($GoodText)- LASKY   
				$BadText = array("\r\n" , "\r" , "\n" , "&#13;", "&1;", "&5;", "&2;", "&4;", "\"");
				$GoodText = array("<br />", "<br />", "<br />", "<br />", "&#39;", "*", "'", '"', "\\");

				//Set up the parameters to search all the tables that have information in them for the rundown - LASKY	
				$StoryStuff  = "SELECT a.*, b.*, c.*, d.*, e.* FROM `tonsplus-story-indexs` AS a";
				$StoryStuff .= " LEFT JOIN `tonsplus-items` AS b ON a.id = b.sid";
				$StoryStuff .= " LEFT JOIN `tonsplus-story-params` AS c ON a.id = c.id";
				$StoryStuff .= " LEFT JOIN `tonsplus-stories` AS d ON a.id = d.id";
				$StoryStuff .= " LEFT JOIN `tonsplus-objects` AS e ON b.oid = e.id";
				$StoryStuff .= " WHERE a.`id`=" .$s['id']. "";
    
				//Do the search    
				$AllStoryStuff = mysql_query($StoryStuff) or die(mysql_error());;
    
				//Get the column names so the items can use names instead of numbers    
				$AllStoryInfo = mysql_fetch_assoc($AllStoryStuff);

 
				//Print the array to make sure I know what the names are    
				//print_r($AllStoryInfo);
   
				//Replace bad text with good text that's stored in the database	- LASKY		
				$AllStoryInfo = str_replace($BadText, $GoodText, $AllStoryInfo);

				//Let's define some needed varialbles
				//Need to get the times into HH:mm:ss H is for 24 hour time. - LASKY
				$AllStoryInfo['EstTime'] = date('i:s', $AllStoryInfo['Estimated']);
				$AllStoryInfo['FrontTime'] = date('H:i:s', $AllStoryInfo['StartTime']);
				$AllStoryInfo['EndTime'] = date('H:i:s', $AllStoryInfo['EndTime']);
				$AllStoryInfo['Actual'] = date('i:s', $AllStoryInfo['Actual']);
				//Hard Hit TIme reads a GMT when loaded so convert it to GMT time
				$AllStoryInfo['HardHitTime'] = gmdate('H:i:s', $AllStoryInfo['HardHitTime']);

				//Put the column text into a varible so I don't have to change each line every time -LASKY
				$printLayout = "";

				//makes it so the columns cycle through so we have no min or max to the number of rows. - MCFERRIN
				$object=array();
				while($dump = mysql_fetch_assoc($AllStoryStuff)) {
					$object[]=$dump;
				}
				
				//print_r($object);

				$sids=array();
				$sids[]=$AllStoryInfo['sid'];
				foreach($object as $object_item) {
					$sids[]=$object_item['sid'];
				}
				
				//print_r($AllStoryInfo);
				
				// START FOREACH LOOP order_name
				foreach($order_name as $cell) {
					$ff_index = array_search($cell,$colFFHeders);
					$key = $colDBHeders[array_search($cell,$colFFHeders)];
					$style="";
		
					// START SWITCH TO DEFINE STYLES FOR CELLS AND WHAT HAPPENS IN CELLS
					switch($cell) {
						//changes the style of the cell
						case "Pg#": $style="font-weight: bold"; break;
						//CHANGES THE APPROVAL COLUMN FROM RED TO GREEN IF APPROVED
						case "approval": 
						if($AllStoryInfo[$key] == 1) { 
							$AllStoryInfo[$key] = "";
							//GREEN - APPROVED
							$style="background-color: #46a120;";
						} else { 
							//RED - NOT APPROVED
							$AllStoryInfo[$key] = "";
							$style="background-color: #ff2600;";
						}
						//STOPS THE CASE
						break;
						
						//MAKE SURE ALL CHANNELS ARE DISPLAYED
						case "Chnl":
						$objects= "";
						//put the unique object IDs (Tons, viz, dgx, overdrive) into an array
						$oid_arrayA = array();
						//Loop through each object to get all of the objects for the script
						foreach($object as $object_item) {
							//Just pick thoes objects once so it's not an inifinite loop
							if(!in_array($object_item['oid'],$oid_arrayA)) {
								//Add each unique object to the array
								$oid_arrayA[] = $object_item['oid'];
								//Get the mos channel for each object
								$objects .=  "<table class=\"objects\"><td> " . $object_item['mosChannel'] . "</td></table>";
							}
						}
						////MAKE SURE ALL MOS OBJECTS ARE DISPLAYED
						$AllStoryInfo[$key] = $objects;
						break;
			
						//Obj Slug - slug - rinsse and repeat
						case "Obj Slug":
						$objects= "";
						$oid_arrayA = array();
						foreach($object as $object_item) {
							//print_r($object);
							if(!in_array($object_item['oid'],$oid_arrayA)) {
								$oid_arrayA[] = $object_item['oid'];
								$objects .=  "<table class=\"objects\"><td> " . $object_item['slug'] . "</td></table>";
							}
						}
						$AllStoryInfo[$key] = $objects;
						break;
			
						////MAKE SURE ALL MOS STATUS ARE DISPLAYED
						case "Status":
						$objects= "";
						$oid_arrayA = array();
						foreach($object as $object_item) {
							//print_r($object);
							if(!in_array($object_item['oid'],$oid_arrayA)) {
								$oid_arrayA[] = $object_item['oid'];
								$objects .=  "<table class=\"objects\"><td> " . $object_item['objAir'] . "</td></table>";
							}
						}
						$AllStoryInfo[$key] = $objects;
						break;
			
						////MAKE SURE ALL TONS or DGx ID ARE DISPLAYED
						case "Tons ID":
						$objects= "";
						$oid_arrayA = array();
						foreach($object as $object_item) {
							//print_r($object);
							if(!in_array($object_item['oid'],$oid_arrayA)) {
								$oid_arrayA[] = $object_item['oid'];
								$objects .=  "<table class=\"objects\"><td> " . $object_item['objPath'] . "</td></table>";
							}
						}
						$AllStoryInfo[$key] = $objects;
						break;
						
						////MAKE SURE ITEMS ARE DISPLAYED
						case "Item":
						$objects= "";
						$oid_arrayA = array();
						foreach($object as $object_item) {
							//print_r($object);
							if(!in_array($object_item['oid'],$oid_arrayA)) {
								$oid_arrayA[] = $object_item['oid'];
								$objects .=  "<table class=\"objects\"><td> " . $object_item['type'] . "</td></table>";
							}
						}
						$AllStoryInfo[$key] = $objects;
						break;

						default: $style="";
					}
					// END SWITCH
				//Define the setings for the print layout includingthe switch
					$printLayout .= "<td style=\"". $style ."\">". $AllStoryInfo[$key] ."</td>";
				}
				// END FOREACH LOOP order_name
				
				if(in_array($AllRundownInfo['CurrentID'],$sids) || $AllRundownInfo['CurrentID'] == $AllStoryInfo['id']) {  $timing_slug = " id=\"timing\""; $oe="timing"; } else { $timing_slug = ""; }

					// Cleaned up If statments - MCFERRIN
				// START IF STATMENT
				// This will print the specific columns into the grid - MCFERRIN/LCOX/LASKY 
				// Removes the story if moved to the Zoo or deleted - LASKY
				if($AllStoryInfo['Zoo'] == 1) {
					$y=0;
					// Is this the current story?  Check the tonsplus-rundowns table for the currentID using $AllRundownInfo [search above. If it matches the current story ID, it needs to be yellow - LASKY
					//in_array($object_item['oid'],$oid_arrayA)
//				} elseif($AllRundownInfo['CurrentID'] == $AllStoryInfo['id']) {  
//					print "<tr class=\"timing\" height=\"25px\" id=\"timing\">".$printLayout ."</tr>\n"; 
//					$y++;
//				} elseif(in_array($AllRundownInfo['CurrentID'],$sids)) {  
//					print "<tr class=\"timing\" height=\"25px\" id=\"timing\">".$printLayout ."</tr>\n"; 
//					$y++;
//					//Or Prints the break line in purple - MCFERRIN/LASKY
				} elseif($AllStoryInfo['break'] == 1) { 
					print "<tr class=\"break\"  height=\"25px\"".$timing_slug.">". $printLayout ."</tr>\n"; 
					$y++;
					//Or Prints the floated lines in red - LASKY
				} elseif($AllStoryInfo['Float'] == 1) {
					print "<tr class=\"float\"  height=\"25px\">". $printLayout ."</tr>\n"; 
					$y++;
					// Print the lines alternating colors so it's easier to read on the grid - MCFERRIN/LASKY
				} else {							
					//print "<tr class=\"" . $oe ."\" height=\"25px\">". $AllRundownInfo['CurrentID'] ."--". $AllStoryInfo['id']. "- ". $printLayout ."</tr>\n"; 
					print "<tr class=\"" . $oe ."\" height=\"25px\"".$timing_slug.">". $printLayout ."</tr>\n"; 
					$y++;
				}
				// END IF STATEMENT
	
			}
		}
		echo "</table><br><br>";
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