<?php
echo "here";
require_once('Industry.php');

switch ($_REQUEST['action']) {
	case 'addDept':
		$addDept = Industry::getInstance()->insertDept($_REQUEST);
		break;
	case 'addEmp':
		$addEmp = Industry::getInstance()->insertEmployee($_REQUEST);
		break;
	case 'editEmp':
		$editEmp = Industry::getInstance()->updateEmployee($_REQUEST);
		break;
	default:
	echo "default";
		$listEmp = Industry::getInstance()->listEmployee($_REQUEST);
		break;
}