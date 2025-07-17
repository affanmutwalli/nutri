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
					
					$stmt = $mysqli->prepare("update banners set PhotoPath = ?, Title = ?, ShortDescription = ? where BannerId= ? ");
					$stmt->bind_param("sssi",$PhotoPath,$_POST["Title"],$_POST["ShortDescription"],$_POST["BannerId"]);
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
					
					$ParamArray=array();
					$ParamArray[0]=$PhotoPath;
					$ParamArray[1]=$_POST["Title"];
					$ParamArray[2]=$_POST["ShortDescription"];
					$InputDocId=$obj->fInsertNew("INSERT INTO banners (PhotoPath,Title,ShortDescription)
					VALUES (?, ?, ?)", "sss",$ParamArray);
				
					$_SESSION["QueryStatus"]="SAVED";
					
	
					echo json_encode(array("msg"=> "Record Saved successfully","response"=>"S")); 	
			}
		}
		else{
	
			echo json_encode(array("msg"=> "Select valid file","response"=>"E")); 	
		}
	}

?>