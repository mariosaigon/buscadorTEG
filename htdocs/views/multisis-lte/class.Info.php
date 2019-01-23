<?php
/**
 * Implementation of Info view
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
 * Class which outputs the html page for Info view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Info extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$version = $this->params['version'];
		$availversions = $this->params['availversions'];

		$this->htmlStartPage(getMLText("admin_tools"), "skin-blue sidebar-mini sidebar-collapse");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">

    <?php
    $this->startBoxPrimary(getMLText("version"));

		if($availversions) {
			$newversion = '';
			foreach($availversions as $availversion) {
				if($availversion[0] == 'stable')
					$newversion = $availversion[1];
			}
			if($newversion > $version->_number) {
				$this->warningMsg(getMLText('no_current_version', array('latestversion'=>$newversion)));
			}
		} else {
			$this->warningMsg(getMLText('no_version_check'));
		}
		$this->contentContainerStart();
		echo "SIGJUTEG <br> Sistema de Gestión de Jurisprudencia del TEG. <br> Desarrollado por I&D Consulting en el marco del proyecto PROINTEGRIDAD de USAID. <br> Encargado de desarollo: José Mario López Leiva <br> marioleiva2011@gmail.com";
		$this->contentContainerEnd();
		//$this->contentContainerStart();
		//phpinfo();
		//$this->contentContainerEnd();

		$this->endsBoxSolidPrimary();
		echo "</div>";
		echo "</div>";
		echo "</div>";
		
    $this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
