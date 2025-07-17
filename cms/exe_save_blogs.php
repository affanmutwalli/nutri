<?php ob_start();
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
	$upload_url="images/blogs/";
	$ValidFile=true;
	
	if(!empty($_FILES["PhotoPath"]["name"]))
	{
		$valid_formats = array("jpg","jpeg","JPG","JPEG","PNG","png","webp");
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
	
		if($_POST["BlogTitle"]!="")
		{	
			$IsActive = "N";
			if(isset($_POST) && array_key_exists("IsActive",$_POST))
			{
				$IsActive = "Y";
			}
			
			if($_POST["BlogId"]!="")
			{
					
					
					$FieldNames=array("PhotoPath");
					$ParamArray=array();
					$ParamArray[0]=$_POST["BlogId"];
					$Fields=implode(",",$FieldNames);
					$single_data=$obj->MysqliSelect1("Select ".$Fields." from blogs_master where BlogId= ? ",$FieldNames,"i",$ParamArray);
					
					$PhotoPath = $single_data[0]["PhotoPath"];
					
					
					if($_FILES["PhotoPath"]["tmp_name"]!="")  
					{
						$tempexp=explode('.', $_FILES["PhotoPath"]["name"]);
						$ext=end($tempexp);
						$UploadPhoto=rand(1111,9999).".".$ext;
						move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url.$UploadPhoto);	 
						$PhotoPath = $UploadPhoto;
					}
					
					$BlogDate = date("Y-m-d",strtotime($_POST['BlogDate']));
					
					$stmt = $mysqli->prepare("update blogs_master set  BlogTitle = ?, BlogDate = ?, Description = ?, PhotoPath = ?, IsActive = ?,SubCategoryId = ? where BlogId= ? ");
					$stmt->bind_param("ssssssi", $_POST['BlogTitle'],$BlogDate,$_POST['Description'],$PhotoPath,$IsActive,$_POST['SubCategoryId'],$_POST["BlogId"]);

					// Log the update attempt
					error_log("Blog Update Attempt - BlogId: " . $_POST["BlogId"] . ", Title: " . $_POST['BlogTitle']);

					if ($stmt->execute()) {
						$affected_rows = $stmt->affected_rows;
						error_log("Blog Update Success - Affected rows: " . $affected_rows);

						if ($affected_rows === 0) {
							error_log("Blog Update Warning - No rows affected (data might be identical)");
						}
					} else {
						error_log("Blog Update Error: " . $stmt->error);
					}

					$stmt->close();
					
					
					$_SESSION["QueryStatus"]="UPDATED";

					// Clear any potential cache
					if (function_exists('opcache_reset')) {
						opcache_reset();
					}

					echo json_encode(array("msg"=> "Record updated successfully","response"=>"S"));
			}
			else
			{
			    
					if($_FILES["PhotoPath"]["tmp_name"]!="")  
					{
						$tempexp=explode('.', $_FILES["PhotoPath"]["name"]);
						$ext=end($tempexp);
						$UploadPhoto=rand(1111,9999).".".$ext;
						move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url.$UploadPhoto);	 
						$PhotoPath = $UploadPhoto;
					}
					
					$BlogDate = date("Y-m-d",strtotime($_POST['BlogDate']));
					
					$ParamArray=array();
					$ParamArray[0]=$_POST['BlogTitle'];
					$ParamArray[1]=$BlogDate;
					$ParamArray[2]=$_POST['Description'];
					$ParamArray[3]=$PhotoPath;
					$ParamArray[4]=$IsActive;
					$ParamArray[5]=$_POST['SubCategoryId'];
					
					$InputDocId=$obj->fInsertNew("INSERT INTO blogs_master (BlogTitle, BlogDate, Description, PhotoPath, IsActive,SubCategoryId)
					VALUES (?, ?, ?, ?, ?, ?)", "ssssss",$ParamArray);
					
					
					$_SESSION["QueryStatus"]="SAVED";
					
	
					echo json_encode(array("msg"=> "Record Saved successfully","response"=>"S")); 	
			}
		}
		else
	
		{
	
			echo json_encode(array("msg"=> "Blog Title is mendetory..!","response"=>"E")); 	
	
		}  
	}
	else{

		echo json_encode(array("msg"=> "Select valid file","response"=>"E")); 	
	}
}
?>