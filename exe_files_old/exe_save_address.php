<?php 
   session_start();

   include_once '../cms/includes/db_connect.php';
   include_once '../cms/includes/functions.php';
   include('../cms/includes/urls.php');
   include('../cms/database/dbconnection.php');
   
   $obj = new main();
   $mysqli = $obj->connection();
   	
   if (!isset($_SESSION["CustomerId"])) {
       echo json_encode(array("msg" => "Customer not logged in.", "response" => "F"));
       exit;
   }
   
   $CustomerId = $_SESSION["CustomerId"];
   
   
   if ($_SERVER['REQUEST_METHOD'] === 'POST' 
       && isset($_POST['Address']) && !empty($_POST['Address'])
       && isset($_POST['Landmark']) && !empty($_POST['Landmark'])
       && isset($_POST['City']) && !empty($_POST['City'])
       && isset($_POST['State']) && !empty($_POST['State'])
       && isset($_POST['PinCode']) && !empty($_POST['PinCode'])) {
   	
   	
   	
   			if($CustomerId!="")
   			{
                   
                   $FieldNames = array("CustomerId", "Address", "State", "City","PinCode","Landmark");
                   $ParamArray = [$CustomerId];
                   $Fields = implode(",", $FieldNames);
                   
                   // Assuming MysqliSelect1 function handles the query correctly
                   $customerAddress = $obj->MysqliSelect1("SELECT $Fields FROM customer_address WHERE CustomerId = ?", $FieldNames, "i", $ParamArray);
   					
   					if(!empty($customerAddress)) {
   					$stmt = $mysqli->prepare("update customer_address set  Address = ?, Landmark = ?, State = ?, City = ?, PinCode = ? where CustomerId = ? ");
   					$stmt->bind_param("sssssi", $_POST['Address'],$_POST['Landmark'],$_POST['State'],$_POST['City'],$_POST['PinCode'],$CustomerId);
   					$stmt->execute();
   					$stmt->close();
   
   					$_SESSION["QueryStatus"]="UPDATED";
   					echo json_encode(array("msg"=> "Address updated successfully","response"=>"S")); 
   				}
   				else
   					{
   							
   							
   							$ParamArray=array();
   							$ParamArray[0]=$_POST['Address'];
   							$ParamArray[1]=$_POST['Landmark'];
   							$ParamArray[2]=$_POST['State'];
   							$ParamArray[3]=$_POST['City'];
   							$ParamArray[4]=$_POST['PinCode'];
   							$ParamArray[5]=$CustomerId;
   							$InputDocId=$obj->fInsertNew("INSERT INTO customer_address (Address, Landmark, State, City, PinCode,CustomerId)
   							VALUES (?, ?, ?, ?, ?, ?)", "sssssi",$ParamArray);
   							
   							
   							$_SESSION["QueryStatus"]="SAVED";
   							
   			
   							echo json_encode(array("msg"=> "Address Saved successfully","response"=>"S")); 	
   					} 
   						
   			}
   			else{
   				echo json_encode(array("msg"=> "CustomerId required","response"=>"F")); 	
   			}
   			
           }
           else{
               echo json_encode(array("msg"=> "Field Required","response"=>"F")); 	
           }
		   
   ?>