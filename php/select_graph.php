<?php
	
	ini_set('display_errors',1);
	ini_set('display_startup_errors',true);
	ini_set('error_reporting',E_ALL);
	
	$path = $_SERVER['DOCUMENT_ROOT'].'/sigma/';
	require_once($path.'php/classes/settings.class.php');
	
	if (isset($_POST['graph'])) {
		$pieces = explode('.', $_POST['graph']);
		
		if (end($pieces) == 'gexf') {
			$settings = new Settings();
			$settings->setDefaultGraph($_POST['graph']);
			echo 'Graph modified';
		}
		else {
			echo 'Not a gexf'; // Need to check more than that, only for quick test
		}
	}
	else {
		echo 'No graph sent.';
	}