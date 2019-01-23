<?php
/**
 * Implementation of EditDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for EditDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditDocument extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
		header('Content-Type: application/javascript');

?>

function checkForm()
{
	msg = new Array();
	if ($("#name").val() == "") msg.push("<?php printMLText("js_no_name");?>");
<?php
	if ($strictformcheck) {
	?>
	if ($("#comment").val() == "") msg.push("<?php printMLText("js_no_comment");?>");
	if ($("#keywords").val() == "") msg.push("<?php printMLText("js_no_keywords");?>");
<?php
	}
?>
	if (msg != "")
	{
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}

$(document).ready( function() {
/*
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
*/
	$("#form1").validate({
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>",
			keywords: "<?php printMLText("js_no_keywords");?>"
		}
	});
});

$(document).ready(function() {
	var i=1;
    $("#add_row").click(function(){

        $('#div_attributes_7').append("<div id='attributes_7-"+(i+1)+"'><textarea style='margin-bottom:5px !important;' class='form-control input-xxlarge' id='attributes_7' name='attributes_7'></textarea>");
        i++; 
    });
    $("#delete_row").click(function(){
	    console.log(i);
    	if(i>1){
		    $("#attributes_7-"+(i)).remove();
		    i--;
		}
	});
	
});
<?php
		$this->printKeywordChooserJs('form1');
		
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$attrdefs = $this->params['attrdefs'];
		$strictformcheck = $this->params['strictformcheck'];
		$orderby = $this->params['orderby'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-mini");

		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();		

		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Document content ////
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-12\">";

		$this->startBoxPrimary(getMLText("edit_document_props"));

		if($document->expires())
			$expdate = date('Y-m-d', $document->getExpires());
		else
			$expdate = '';
?>
<div class="table-responsive">
<form action="../op/op.EditDocument.php" name="form1" id="form1" method="post">
	<input type="hidden" name="documentid" value="<?php echo $document->getID() ?>">
	<table class="table-condensed">
		<tr>
			<td class=""><?php printMLText("name");?>:</td>
			<td><input class="form-control" type="text" name="name" id="name" value="<?php print htmlspecialchars($document->getName());?>" size="60" required></td>
		</tr>
		<tr>
			<td valign="top" class=""><?php printMLText("comment");?>:</td>
			<td><textarea class="form-control" name="comment" id="comment" rows="4" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>><?php print htmlspecialchars($document->getComment());?></textarea></td>
		</tr>
		<!--	<tr>
		<td valign="top" class=""><?php //printMLText("keywords");?>:</td>
			<td class="standardText"> -->
				<?php
					//$this->printKeywordChooserHtml('form1', $document->getKeywords());
				?>
		<!--	</td>
		</tr> -->
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td>
        <select class="chzn-select form-control" name="categories[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<?php
			$categories = $dms->getDocumentCategories();
			foreach($categories as $category) {
				echo "<option value=\"".$category->getID()."\"";
				if(in_array($category, $document->getCategories()))
					echo " selected";
				echo ">".$category->getName()."</option>";	
			}
?>
				</select>
      </td>
		</tr>
		<tr>
			
			<td>
				  <input type="hidden" id="expires" name="expires" value="false"<?php if (!$document->expires()) print " checked";?>>
				  
				  
			</td>
		</tr>
<?php
		if ($folder->getAccessMode($user) > M_READ) 
		{
			/*print "<tr>";
			print "<td class=\"\">" . getMLText("sequence") . ":</td>";
			print "<td>";
			$this->printSequenceChooser($folder->getDocuments('s'), $document->getID());
			if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off'); 
			print "</td></tr>";*/
		}
		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
				$arr = $this->callHook('editDocumentAttribute', $document, $attrdef);
				if(is_array($arr)) {
					echo "<tr>";
					echo "<td>".$arr[0].":</td>";
					echo "<td>".$arr[1]."</td>";
					echo "</tr>";
				} else {
?>
		<tr>
			<td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
			<td><?php $this->printAttributeEditField($attrdef, $document->getAttribute($attrdef)) ?></td>
		</tr>
<?php
				}
			}
		}
?>
		<tr>
			<td></td>
			<td><button type="submit" class="btn btn-info"><i class="fa fa-save"></i> <?php printMLText("save")?></button></td>
		</tr>
	</table>
</form>
</div>
<?php
	
		$this->endsBoxPrimary();

		echo "</div>";
		echo "</div>"; 
		echo "</div>"; // Ends row

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
