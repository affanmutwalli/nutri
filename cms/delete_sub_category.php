<?php ob_start(); ?>
<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
ini_set('max_execution_time',300);

sec_session_start();
	
if (login_check($mysqli) == true) 
{
	if($_POST["DSubCategoryId"]!="" )
	{	
				$ParamArray=array();
				$ParamArray[0]=$_POST["DSubCategoryId"];
				$ContestId=$obj->fDeleteNew("Delete From sub_category Where SubCategoryId = ?", "i",$ParamArray);

				
				$_SESSION["QueryStatus"]="DELETED";
				echo json_encode(array("msg"=> "Record deleted successfully","response"=>"D")); 	

	}
}
?>