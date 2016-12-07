<?php

require(ABSPATH . 'gpclib/api/app.php');

$scriptId = strip_tags($_GET['sid']);
$scriptEdit = strip_tags($_GET['e']);
$scriptAction = strip_tags($_GET['a']);

if(!empty($scriptId)) $scriptAction="view";
	
switch($scriptAction)
{
	case "view": // view single script
		require(ABSPATH . 'gpclib/views/single.php');
	break;
	
	case "m": // show user script list.
		require(ABSPATH . 'gpclib/views/manage.php');
	break;
	
	default:
		require(ABSPATH . 'gpclib/views/list.php');
	break;
}

?>
