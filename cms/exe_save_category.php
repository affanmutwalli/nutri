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
			
			if($_POST["CategoryId"]!="")
			{
					
					$stmt = $mysqli->prepare("update category_master set CategoryName = ? where CategoryId= ? ");
					$stmt->bind_param("si",$_POST["CategoryName"],$_POST["CategoryId"]);
					$stmt->execute();
					$stmt->close();
	
					
					$_SESSION["QueryStatus"]="UPDATED";
					echo json_encode(array("msg"=> "Record updated successfully","response"=>"S")); 	
			}
			else
			{
					
					$ParamArray=array();
					$ParamArray[0]= $_POST["CategoryName"];
					$InputDocId=$obj->fInsertNew("INSERT INTO category_master (CategoryName)
					VALUES (?)", "s",$ParamArray);
				
					$_SESSION["QueryStatus"]="SAVED";
					
	
					echo json_encode(array("msg"=> "Record Saved successfully","response"=>"S")); 	
			}
		}
		

?>