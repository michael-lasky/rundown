<? 
@mysql_connect('localhost','root','');
@mysql_select_db('tonsplus');
include_once "/TonsPlus/www/sites/all/modules/common/common.inc.php";

$print1_css = GetVariable("RundownTxtFormat");
$uid = GetUIDFromSession();
$rid = $_GET['rid'];
$rundownTitle = GetRundownTitle($rid);
$labels = GetParamLabels();

$role = '';
if (isset($_GET['role'])) {
	$role = $_GET['role'];
}
else {
	$query = "SELECT `rid` FROM `users_roles` WHERE `uid`='".$uid."' ORDER BY `rid` ASC LIMIT 1; ";	
	$result_query = mysql_query($query);
	if ($result_query && (mysql_num_rows($result_query)>0)) {
		$item = mysql_fetch_assoc($result_query);
		$role = $item["rid"];
	}
	else
		$role = 0;
}

$default_orientation = "";
$q = "SELECT `default_orientation` FROM `tonsplus-default-print-layout` WHERE `uid`='".$uid."' LIMIT 1; ";
$result = mysql_query($q);
$item = mysql_fetch_assoc($result);
$default_orientation = $item["default_orientation"];

$orientation = '';
if (isset($_GET['orientation'])) {
	$orientation = $_GET['orientation'];
}
	
if ( ($orientation == "") && (!$default_orientation) ) 
	$orientation = "portrait";
else if ( ($orientation == "") && ($default_orientation) ) 
	$orientation = $default_orientation;
else {}
	
$op = "";
if (isset($_GET['op'])) 
	$op = $_GET['op'];

$fontSize = "13";
$colWidths = '';
	
$colHeaders = array("pageNum"=>"Page", "title"=>"Title", "segment"=>"Segment", "approved"=>"Approv", "actualDur"=>"Actual Dur", "estDur"=>"Est Duration", "starttime"=>"Front Time", "endtime"=>"Back Time", "camera"=>"Camera", "TME"=>"TME", "slug"=>"Obj Slug", "author"=>"Author", "created"=>"Created", "modified"=>"Modified", "HardHitTime"=>"Hard Hit Time", "Tape"=>"Tape", "references"=>"References", "inFloat"=>"Float", "mosChannel"=>"Channels", "mosStatus"=>"Status", "tonsID"=>"Tons ID", "items"=>"Items", "itemsDur"=>"Items Dur", "keywords"=>"Keywords");

for ($f=0; $f<25; $f++) { 
	if ( isset( $labels[$f]) ) {
		$label = "ff".strval($f);	
		$colHeaders[$label] = "$labels[$f]"; 
	}
}
$colOrder = '';

$q = "SELECT `colsToPrint`, `colWidths`, `orientation`, `fontSize` FROM `tonsplus-print-layout` WHERE `uid`='".$uid."' AND `rid`='".$role."' AND `orientation`='".$orientation."' LIMIT 1; " ; 
		//echo ($q);
$result = mysql_query($q);

if ( (isset($_GET['colOrder'])) && ($op == "select")) {
	$colOrder = $_GET['colOrder'];
	if (isset($_GET['fontSize'])) 
		$fontSize = $_GET['fontSize'];
}
else if ($result && (mysql_numrows($result) > 0)) {
		$data = mysql_fetch_assoc($result);
		$colOrder = $data["colsToPrint"];
		$colWidths = $data["colWidths"];
		$fontSize = $data["fontSize"];
			
	}
else if ( (isset($_GET['colOrder'])) ) {
	$colOrder = $_GET['colOrder'];
	if (isset($_GET['fontSize'])) 
		$fontSize = $_GET['fontSize'];
	if (isset($_GET['colWidths'])) 
		$colWidths = $_GET['colWidths'];
}
else {}

if ($op == "resizeFont") {
	if (isset($_GET['fontSize'])) 
		$fontSize = $_GET['fontSize'];
	if (isset($_GET['colOrder']))
		$colOrder = $_GET['colOrder'];
	if (isset($_GET['colWidths'])) 
		$colWidths = $_GET['colWidths'];
	//	echo "here $colOrder";
}

if ($colOrder == "")
	$colOrder = 'pageNum,title';
$cols = explode(",",$colOrder);

$layoutWidth = '';	
if ($orientation == 'portrait')
	$layoutWidth = 660;
else
	$layoutWidth = 900;

//print_r($cols);
if (!$colWidths) {
	$width = ceil($layoutWidth/(sizeof($cols)))."px";
	for($i=0; $i<sizeof($cols); $i++)
		$colWidths .= $width.",";
}
$widths = explode(",", $colWidths);
//print_r($widths);
//echo "layoutWidth=".$layoutWidth;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Print Rundown | Opus</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="ID=EmulateIE8" />

<link type="text/css" rel="stylesheet" media="all" href="/themes/zen/zen-internals/css/zen-liquid.css?R" />
<link type="text/css" rel="stylesheet" media="print" href="/external/Print rundown layout/print_rundown_print.css?R" />
<link type="text/css" rel="stylesheet" media="screen" href="/external/Print rundown layout/print_rundown_layout.css?R" />

<script type="text/javascript" src="/scripts/jquery/1.7/jquery.min.js"></script>
<script src="resizeCols/colResizable-1.3.min.js"></script>
<script type="text/javascript" src="resizeCols/jquery.chili-2.2.js"></script>
<script type="text/javascript" src="resizeCols/jquery.ui.core.js"></script>
<script type="text/javascript" src="resizeCols/jquery.ui.widget.js"></script>
<script type="text/javascript" src="resizeCols/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="resizeCols/jquery.ui.sortable.js"></script>
<script type="text/javascript" src="resizeCols/jquery.dragtable.js"></script>
<script src="print_rundown_layout.js"></script>

<script>
function doStoryPrint()
{	
	window.print();
	return false;
}

$(document).ready(function() {
	 	$(function(){	
		var onSampleResized = function(e){
			var columns = $(e.currentTarget).find("th");
			var msg = "";
			columns.each(function(){ msg += $(this).width() + "px,"; })
			$("#widths").html(msg);
			
		};	
	
		$("#table").colResizable({
			liveDrag:true, 
			gripInnerHtml:"<div class='grip'></div>", 
			draggingClass:"dragging", 
			onResize:onSampleResized});
		
	});
	
});
</script>

<script type='text/css'>
@-moz-document url-prefix() {
   .breakHeader {
        visibility:hidden;
    }
}
</script>

</head>
<body style="width:98%;" class="not-front logged-in node-type-page no-sidebars page-print-stories section-print-stories" style='float:none;'>

  <div id="page-wrapper" style='float:none; width:700px; min-width:665px;'><div id="page" style='float:none;'> 

    <div id="main-wrapper" style='float:none;'><div id="main" class="clearfix" style='float:none;'>

      <div id="content" class="column" style='font-size:10pt; float:none;'>
	  
	 <div style="width:100%; clear:both;"></div>  
	    <div id="rundownTitle" style="width:600px; clear:both;">
	     <div style='float:left;'><img src='/sites/default/files/opus.png' /></div>
	     <div style='text-align:center; font-size:15pt; font-weight:bold;'><?=$rundownTitle?></div>
	     <div style='float:left; clear:both;'>&nbsp;</div>
	    </div>
	  </div>
	  <div style='float:left; clear:both;'> </div>
	  
	  <div id='printscript-tools' class="section">
	 
	  <div style='margin-top:20px; float:none;'> <!-- align='center' -->
	  <div style='text-align:center; width:650px; border:1px solid #CCCCCC; padding:15px 15px 0px 15px;'>
		
		<div style='float:left; clear:both;'> </div>
		<label for="myInput">Select role: </label>  
		<select id="roles" onchange="onRoleSelect('<?echo $rid?>')">
			<!--<option name="empty" value="0"></option>-->
			<?php	
			// Get current user roles
			$q = "SELECT `rid` FROM `users_roles` WHERE `uid`='".$uid."' ORDER BY `rid` ASC; ";	
			$result = mysql_query($q);	
			if ($result && (mysql_numrows($result) > 0)) {
				for ($i=0; $i<mysql_numrows($result); $i++) {
					$role_id = mysql_result($result,$i,"rid");
					$role_name = getRoleName($role_id);
					if ($role_id == $role)
						echo( "<option selected name='".$role_name."' value='".$role_id."'>" );
					else
						echo( "<option name='".$role_name."' value='".$role_id."'>" );
					echo($role_name);
					echo("</option>");
				}
			}
			?>
		</select>  &nbsp;
		
		<label for="orientation">Orientation: </label>
		<select id="layoutOrientation" onchange="onOrientationChange('<?=$rid?>')">
		<? if ($orientation == "portrait") 
				echo ( "<option selected value='portrait'>Portrait</option>" );
			else
				echo ( "<option value='portrait'>Portrait</option>" );
			if ($orientation == "landscape") 
				echo ( "<option selected value='landscape'>Landscape</option>" );
			else
				echo ( "<option value='landscape'>Landscape</option>" );
		?>
		</select> &nbsp;
		
		<label for="fontresizer">Font size: </label>
		<select id="fontsizeSelector" onchange="onFontResize('<?echo $rid?>')">
		<? for($i=8; $i<=25; $i++) {
			if ($i == $fontSize)
				echo ( "<option selected value='".$i."'>".$i."</option>" );
			else
				echo ( "<option value='".$i."'>".$i."</option>" );
			}
		?>
		</select> &nbsp;
	
        <a href='#' onclick='doSelectColumns(<?echo $rid?>)'><img src='/images/gear2.png' alt='Settings' title='Settings' /></a> &nbsp;
		<a href='#' onclick='doSaveLayout("<?echo $colOrder?>", "<?echo $fontSize?>" )'><img src='/images/save.png' alt='Save layout' title='Save layout' /></a> &nbsp;
	   <a href='#' onclick='javascript:window.print()'><img src='/images/printer.png' alt='Print this rundown' title='Print this rundown' /></a> 
	   
	   <?	$visibility = "visible";
		if ($orientation == $default_orientation)
			$visibility = "hidden";
		?>
		<div id="msgDefault" style="font-size:10px; visibility:<?=$visibility;?>;">
			<a href="#" style="text-decoration:none;" onclick="setDefaultOrientation();">Set <?=$orientation;?> as default orientation </a>
		</div>
		<div id="msgDefaultSaved" style="margin-top:-17px; font-size:10px; color:#990000; visibility:hidden;">Successfully saved orientation!</div>
		
	 </div>
	 </div>
	 
	  </div>
	  
      </div>
   <div id="content" class="column" style='float:none;'><div class="section" style='float:none;'>
   
   <div id="msgNotSaved" style="visibility:hidden;">Changes to the layout have not been saved yet...</div>
   <div id="msgSaved" style="visibility:hidden;">Sucessfully saved layout!</div>

   <div id="tableContainer" style="position:relative; border:0px solid green; padding-top:15px;">
	
	<table style="width:<?=$layoutWidth?>px; font-size:<?=$fontSize?>px; <?php echo $print1_css?>;" cellpadding="5" id="table" class="sar-table">
	 <thead>
		<tr>
		<?	for ($i=0; $i < sizeof($cols); $i++) {
				$col= $cols[$i];
		
			?>
				<th style="width:<?=$widths[$i]?>; <?=$print1_css?>;" class="<?=$cols[$i]?>"><div class="colHeader"><?=$colHeaders["$col"]?></div></th>
			
			<? } ?>
				
		</tr>
	</thead>
	<tbody>
		
	<?	$sids = getRundownStories($rid);
		
		$row=0;
		foreach ($sids as $sid) {
			$story_info = getRundownStoryInfo($sid);
			//print_r($story_info);
			if (($row++ & 1) == 0) 
				$bgcolor = "#cccccc";
			else
				$bgcolor = "#ffffff";
			
            if ($story_info['break'] == 1)
				$bgcolor = "#9966ff";
			?>	
			<tr bgcolor="<?=$bgcolor?>">
			<?	for ($i=0; $i < sizeof($cols); $i++) {
					$col = $cols[$i];
					//if ($colWidths) 
					?>
						<td style="width:<?=$widths[$i]?>; <?=$print1_css?>;" class="<?=$cols[$i]?>"><?=$story_info["$col"]?></td>
					<? //else ?>
						
					
					<?
					}
					if ($story_info['break'] == 1) {
					?>
						</tr>
		
						<tr class="pageBreak" style="page-break-before:always;">
						
						
				<?	}
					else ?>
						</tr>
			<?	} ?>
	  </tbody>
	</table>
 
</div>	   
</div>
 <div id='order' style='visibility:hidden;'><?echo $colOrder?></div>
  <div id="widths" style="visibility:hidden;"><?echo $colWidths?></div>
 <div id="fontSize" style="visibility:hidden;"><?echo $fontSize?></div>
 
 	</div> 
  </div>

  </div> <!-- /.block -->

 </div> <!-- /.region -->

</div></div> <!-- /.section, /#content -->
</div></div> <!-- /#main, /#main-wrapper -->


<script language="javascript">
	setTimeout('checkUpdates()', 10000 );
</script>

</body>
</html>
