<?php ob_start(); ?>
<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('../includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
ini_set('max_execution_time',300);
	
sec_session_start();
	
if (login_check($mysqli) == true) 
{
	if($_POST["DBlogId"]!="" )
	{	
				
				$FieldNames=array("PhotoPath");
				$ParamArray=array();
				$ParamArray[0]=$_POST["DBlogId"];
				$Fields=implode(",",$FieldNames);
				$single_data=$obj->MysqliSelect1("Select ".$Fields." from blogs_master where BlogId= ? ",$FieldNames,"i",$ParamArray);

				$filename ="../".end( explode( $siteURL, $BlogsImagesURL.$single_data[0]["PhotoPath"] ) );
				
				
				if (file_exists($filename)) {
					unlink($filename);
				}
				
				
				
				$ParamArray=array();
				$ParamArray[0]=$_POST["DBlogId"];
				$ContestId=$obj->fDeleteNew("Delete From blogs_master Where BlogId = ?", "i",$ParamArray);

				
				$_SESSION["QueryStatus"]="DELETED";
				echo json_encode(array("msg"=> "Record deleted successfully","response"=>"D")); 	

	}
}
?>