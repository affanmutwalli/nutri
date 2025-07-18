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
	if($_POST["DReviewId"]!="" )
	{	
		// Get the photo path before deleting
		$FieldNames = array("PhotoPath");
		$ParamArray = array($_POST["DReviewId"]);
		$Fields = implode(",", $FieldNames);
		$single_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_review WHERE Product_ReviewId = ?", $FieldNames, "i", $ParamArray);

		// Delete the image file if it exists
		if (!empty($single_data) && isset($single_data[0]["PhotoPath"])) {
			$photoPath = $single_data[0]["PhotoPath"];
			$filename = "images/ingredient/" . $photoPath;

			if (file_exists($filename)) {
				unlink($filename);
			}
		}
		
		// Delete the review record
		$ParamArray = array();
		$ParamArray[0] = $_POST["DReviewId"];
		$ContestId = $obj->fDeleteNew("DELETE FROM product_review WHERE Product_ReviewId = ?", "i", $ParamArray);

		$_SESSION["QueryStatus"] = "DELETED";
		echo json_encode(array("msg"=> "Review deleted successfully","response"=>"D")); 

	}
}
?>
