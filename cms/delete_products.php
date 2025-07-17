<?php ob_start(); ?>
<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
ini_set('max_execution_time',300);
	
sec_session_start();
	
if (login_check($mysqli) == true) 
{
	if($_POST["DProductId"]!="" )
	{	
				$FieldNames=array("PhotoPath");
				$ParamArray=array();
				$ParamArray[0]=$_POST["DProductId"];
				$Fields=implode(",",$FieldNames);
				$single_data=$obj->MysqliSelect1("Select ".$Fields." from product_master where ProductId= ? ",$FieldNames,"i",$ParamArray);

				if (!empty($single_data) && isset($single_data[0]["PhotoPath"])) {
					$photoPath = $single_data[0]["PhotoPath"];
					$filename = $_SERVER['DOCUMENT_ROOT'] . "/images/products/" . $photoPath;
		
					// Debugging: Check if file exists
					error_log("File path: " . $filename);
					if (file_exists($filename)) {
						unlink($filename);
					}
				}
				
				$ParamArray=array();
				$ParamArray[0]=$_POST["DProductId"];
				$ContestId=$obj->fDeleteNew("Delete From product_master Where ProductId = ?", "i",$ParamArray);

				
				$_SESSION["QueryStatus"]="DELETED";
				echo json_encode(array("msg"=> "Record deleted successfully","response"=>"D")); 	

	}
}
?>