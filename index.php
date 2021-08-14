<?php
//echo "here";
define('SERVER', 'localhost');
        define('USERNAME', 'root');
        define('PASSWORD', '');
        define('DATABASE', 'industry');
require_once('Industry.php');
if(!isset($_REQUEST['action'])){
	$_REQUEST['action'] = '';
}
switch ($_REQUEST['action']) {
	case 'addDept':
		$addDept = Industry::getInstance()->insertDept($_REQUEST);
		break;
	case 'addEmp':
		$addEmp = Industry::getInstance()->addEmployee();
		break;
	case 'insertEmp':
		$insEmp = Industry::getInstance()->insertEmployee($_REQUEST);
		break;
	case 'editEmp':
		$editEmp = Industry::getInstance()->updateEmployee($_REQUEST);
		break;
	case 'listEmp':
		$listEmp = Industry::getInstance()->listEmployee($_REQUEST);
		break;
	case 'listDept':
		$listDept = Industry::getInstance()->listDepartment($_REQUEST);
		break;
	default:
		$listDept = Industry::getInstance()->listDepartment($_REQUEST);
		$dept =json_decode($listDept,true);
		$listEmp = Industry::getInstance()->listEmployee($_REQUEST);
		$emp =json_decode($listEmp,true);
		include_once("index.html");
		break;
}