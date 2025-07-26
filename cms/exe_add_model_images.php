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

			// Get the next sort_order for this product
			$FieldNames = array("sort_order");
			$ParamArraySort = array($_POST["ProductId"]);
			$maxSortOrder = $obj->MysqliSelect1(
				"SELECT MAX(sort_order) as sort_order FROM model_images WHERE ProductId = ?",
				$FieldNames,
				"i",
				$ParamArraySort
			);

			$nextSortOrder = 1;
			if (!empty($maxSortOrder) && isset($maxSortOrder[0]["sort_order"])) {
				$nextSortOrder = $maxSortOrder[0]["sort_order"] + 1;
			}

			$ParamArray=array();
			$ParamArray[0]=$_POST["ProductId"];
			$ParamArray[1]=$PhotoPath;
			$ParamArray[2]=$nextSortOrder;
			$InputDocId=$obj->fInsertNew("INSERT INTO model_images (ProductId, PhotoPath, sort_order)
			VALUES (?, ?, ?)", "isi",$ParamArray);

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