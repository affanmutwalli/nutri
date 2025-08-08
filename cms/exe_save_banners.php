<?php ob_start(); ?>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$connection = $obj->connection();

// Check if database connection failed
if ($connection === false) {
	error_log("Banner upload: Database connection failed");
	echo json_encode(array("msg"=> "Database connection failed. Please try again later.","response"=>"E"));
	exit;
}

ini_set('max_execution_time',300);

sec_session_start();
	
if (login_check($mysqli) == true)
{
		// Debug: Log that we're in the login check
		error_log("Banner upload: Login check passed");

		$upload_url="images/banners/";
		$ValidFile=true;
	
		if(!empty($_FILES["PhotoPath"]["name"]))
		{
			$valid_formats = array("jpg","jpeg","JPG","JPEG","PNG","png", "webp");
			$name = $_FILES['PhotoPath']['name'];
			$size = $_FILES['PhotoPath']['size'];
			list($txt, $ext) = explode(".", $name);
			if(!in_array($ext,$valid_formats))
			{
				$ValidFile=false;
			}
		} 
		if($ValidFile==true)
		{
			$PhotoPath="";
			
			if($_POST["BannerId"]!="")
			{
					$FieldNames=array("PhotoPath");
					$ParamArray=array();
					$ParamArray[0]=$_POST["BannerId"];
					$Fields=implode(",",$FieldNames);
					$single_data=$obj->MysqliSelect1("Select ".$Fields." from banners where BannerId= ? ",$FieldNames,"i",$ParamArray);
					$PhotoPath = $single_data[0]["PhotoPath"];
					
					if($_FILES["PhotoPath"]["tmp_name"]!="")  
					{
						$tempexp=explode('.', $_FILES["PhotoPath"]["name"]);
						$ext=end($tempexp);
						$UploadPhoto=rand(11111,99999).".".$ext;
						move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url.$UploadPhoto);	 
						$PhotoPath = $UploadPhoto;
					}
					
					// Handle ShowButton checkbox (if not checked, it won't be in $_POST)
					$ShowButton = isset($_POST["ShowButton"]) ? 1 : 0;

					$stmt = $mysqli->prepare("update banners set PhotoPath = ?, Title = ?, ShortDescription = ?, ShowButton = ? where BannerId= ? ");
					$stmt->bind_param("sssii",$PhotoPath,$_POST["Title"],$_POST["ShortDescription"],$ShowButton,$_POST["BannerId"]);
					$stmt->execute();
					$stmt->close();
	
					
					$_SESSION["QueryStatus"]="UPDATED";
					echo json_encode(array("msg"=> "Record updated successfully","response"=>"S")); 	
			}
			else
			{
					if($_FILES["PhotoPath"]["tmp_name"]!="")  
					{
						$tempexp=explode('.', $_FILES["PhotoPath"]["name"]);
						$ext=end($tempexp);
						$UploadPhoto=rand(11111,99999).".".$ext;
						move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url.$UploadPhoto);	 
						$PhotoPath = $UploadPhoto;
					}
					
					// Handle ShowButton checkbox (if not checked, it won't be in $_POST)
					$ShowButton = isset($_POST["ShowButton"]) ? 1 : 0;

					$ParamArray=array();
					$ParamArray[0]=$PhotoPath;
					$ParamArray[1]=$_POST["Title"];
					$ParamArray[2]=$_POST["ShortDescription"];
					$ParamArray[3]=$ShowButton;
					$InputDocId=$obj->fInsertNew("INSERT INTO banners (PhotoPath,Title,ShortDescription,ShowButton)
					VALUES (?, ?, ?, ?)", "sssi",$ParamArray);
				
					$_SESSION["QueryStatus"]="SAVED";
					
	
					echo json_encode(array("msg"=> "Record Saved successfully","response"=>"S")); 	
			}
		}
		else{

			echo json_encode(array("msg"=> "Select valid file","response"=>"E"));
		}
	}
	else
	{
		// Debug: Log login failure
		error_log("Banner upload: Login check failed");
		echo json_encode(array("msg"=> "Authentication failed. Please login again.","response"=>"E"));
	}

?>