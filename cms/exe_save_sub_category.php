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
		 if (!empty($_POST["SubCategoryName"])) {  
		     $upload_url = "images/products/";
                $valid_formats = array("jpg", "jpeg", "png", "webp");
                $PhotoPath = "";
        
                // Check for file upload
                if (!empty($_FILES["PhotoPath"]["name"])) {
                    $name = $_FILES['PhotoPath']['name'];
                    $size = $_FILES['PhotoPath']['size'];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
                    if (!in_array($ext, $valid_formats)) {
                        echo json_encode(array("msg" => "Select a valid file", "response" => "E"));
                        exit();
                    }
                }
                
			if($_POST["SubCategoryId"]!="")
			{
			    // Prepare to update existing product
                    $FieldNames = array("PhotoPath");
                    $ParamArray = array($_POST["ProductId"]);
                    $Fields = implode(",", $FieldNames);
                    $single_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM sub_category WHERE SubCategoryId = ?", $FieldNames, "i", $ParamArray);
                    $PhotoPath = $single_data[0]["PhotoPath"];
        
                    // Handle new photo upload if available
                    if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                        $UploadPhoto = rand(11111, 99999) . "." . $ext;
                        if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                            $PhotoPath = $UploadPhoto; // Update PhotoPath with new photo
                        }
                    }
					
					$stmt = $mysqli->prepare("update sub_category set SubCategoryName = ?, PhotoPath = ? where SubCategoryId= ? ");
					$stmt->bind_param("ssi",$_POST["SubCategoryName"],$PhotoPath,$_POST["SubCategoryId"]);
					$stmt->execute();
					$stmt->close();
	
					
					$_SESSION["QueryStatus"]="UPDATED";
					echo json_encode(array("msg"=> "Record updated successfully","response"=>"S")); 	
			}
			else
			{
        			    if (!empty($_FILES["PhotoPath"]["tmp_name"])) {
                        $UploadPhoto = rand(11111, 99999) . "." . $ext;
                        if (move_uploaded_file($_FILES["PhotoPath"]["tmp_name"], $upload_url . $UploadPhoto)) {
                            $PhotoPath = $UploadPhoto;
                        }
                    }
					
					$ParamArray=array();
					$ParamArray[0]= $_POST["SubCategoryName"];
					$ParamArray[1]= $PhotoPath;
					$InputDocId=$obj->fInsertNew("INSERT INTO sub_category (SubCategoryName,PhotoPath)
					VALUES (?, ?)", "ss",$ParamArray);
				
					$_SESSION["QueryStatus"]="SAVED";
					
	
					echo json_encode(array("msg"=> "Record Saved successfully","response"=>"S")); 	
			}
		 }
		}
		

?>