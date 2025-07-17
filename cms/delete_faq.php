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
	if($_POST["DFAQId"]!="" )
	{	
				$ParamArray=array();
				$ParamArray[0]=$_POST["DFAQId"];
				$ContestId=$obj->fDeleteNew("Delete From faqs Where FAQId = ?", "i",$ParamArray);

				
				$_SESSION["QueryStatus"]="DELETED";
				echo json_encode(array("msg"=> "FAQ deleted successfully","response"=>"D")); 

	}
}
?>
