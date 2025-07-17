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
	$upload_url="images/products/";
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

		if($_FILES["PhotoPath"]["tmp_name"]!="")
		{
			if($_FILES["PhotoPath"]["tmp_name"]!="")
			{
				$tempexp=explode('.', $_FILES["PhotoPath"]["name"]);
				$ext=end($tempexp);
				$UploadPhoto="Img_".rand(1111,9999).".".$ext;
				move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url.$UploadPhoto);
				$PhotoPath = $UploadPhoto;
			}

			$ParamArray=array();
			$ParamArray[0]=$_POST["ProductId"];
			$ParamArray[1]=$PhotoPath;
			$InputDocId=$obj->fInsertNew("INSERT INTO model_images (ProductId, PhotoPath)
			VALUES (?, ?)", "is",$ParamArray);

			$_SESSION["QueryStatus"]="SAVED";

			echo json_encode(array("msg"=> "Record Saved successfully","response"=>"S"));
		}
		else
		{
			echo json_encode(array("msg"=> "Please select image first..!","response"=>"E"));
		}
	}
	else{
		echo json_encode(array("msg"=> "Select valid file","response"=>"E"));
	}
}
else
{
	echo json_encode(array("msg"=> "Access denied","response"=>"E"));
}
?>