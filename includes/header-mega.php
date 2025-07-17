<?php
$IsHome = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='HOME'");
$IsAboutUs = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='ABOUT US'");
$IsProducts = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='PRODUCTS'");
$IsProjects = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='PROJECTS'");
$IsNewsEvents = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='NEWS & EVENTS'");
$IsBlogs = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='BLOGS'");
$IsCareers = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='CAREERS'");
$IsContactUs = $obj->fSelectRowCountNew("Select IsActive from master_menu where MenuName='CONTACT US'");

$FieldNames=array("MenuId","MenuName","PageURL","DisplayIn","ParentId");
$ParamArray=array();
$ParamArray[0]= "Y";
$Fields=implode(",",$FieldNames);
$custome_menu_data=$obj->MysqliSelect1("Select ".$Fields." from custome_menu Where IsActive = ?",$FieldNames,"s",$ParamArray);

$FieldNames=array("CategoryId","CategoryName","PageHeading","ShortDescription","PhotoPath","ShowProducts","ProductDescription","ShowModels","AboutCategory");
$ParamArray=array();
$ParamArray[0] = "Y";
$Fields=implode(",",$FieldNames);
$all_categories_data=$obj->MysqliSelect1("Select ".$Fields." from category_master Where IsActive = ?",$FieldNames,"s",$ParamArray);

$FieldNames=array("ContactId","MobileNo","LandLineNo","EmailSales","EmailSupport","MapURL","GST","CIN","PhotoPath","WhatsAppNo");
$ParamArray=array();
$ParamArray[0] = 1;
$Fields=implode(",",$FieldNames);
$all_contact_data=$obj->MysqliSelect1("Select ".$Fields." from contact_master Where ContactId = ?",$FieldNames,"i",$ParamArray);
$LogoImage =$ImagesURL.$all_contact_data[0]["PhotoPath"];
$FieldNames=array("LocationId","LocationHeading","LocationAddress","IsActive");
$ParamArray=array();
$ParamArray[0] = "Y";
$Fields=implode(",",$FieldNames);
$all_location_data=$obj->MysqliSelect1("Select ".$Fields." from location_master Where IsActive = ?",$FieldNames,"s",$ParamArray);

$FieldNames=array("ProductId","ProductName");
$ParamArray=array();
$ParamArray[0] = "Y";
$Fields=implode(",",$FieldNames);
$footer_product_master=$obj->MysqliSelect1("Select ".$Fields." from product_master Where InFooter = ?",$FieldNames,"s",$ParamArray);

?>
<!-- Header Start  -->
<div class="stellarnav" style="padding:10px;">
    <ul>
        <li <?php if($IsHome == "N"){echo 'style="display:none;"';}?>><a href="index.php">HOME</a></li>
        <li <?php if($IsAboutUs == "N"){echo 'style="display:none;"';}?>><a href="about.php">ABOUT US</a></li>
        <li <?php if($IsProducts == "N"){echo 'style="display:none;"';}?>  class="mega" data-columns="3"><a href="product.php">PRODUCTS</a>
            <ul>
            	<?php
					for($i=0; $i<count($all_categories_data); $i++)
					{
						$FieldNames=array("ProductId","ProductName");
						$ParamArray=array();
						$ParamArray[0] = $all_categories_data[$i]["CategoryId"];
						$ParamArray[1] = "Y";
						$ParamArray[2] = "0";
						$Fields=implode(",",$FieldNames);
						$all_products_data=$obj->MysqliSelect1("Select ".$Fields." from product_master Where CategoryId = ? AND IsActive = ? AND ParentId=?",$FieldNames,"isi",$ParamArray);
						
						if($all_products_data != NULL)
						{
							echo '<li><a href="#">'.$all_categories_data[$i]["CategoryName"].'</a>';
								 echo '<ul>';
								 for($j=0; $j<count($all_products_data); $j++)
								 {	
								    $Url="";
								    $hasSub = $obj->fSelectRowCountNew("Select COUNT(*) from product_master where ParentId=".$all_products_data[$j]["ProductId"]);
								    if($hasSub >0)
								    {
								      $Url="sub_product.php?ProductId=".$all_products_data[$j]["ProductId"];  
								    }
								    else
								    {
								       $Url="products.php?ProductId=".$all_products_data[$j]["ProductId"];
								    }
									echo '<li><a href="'.$Url.'"><i class="fa fa-circle"></i> '.$all_products_data[$j]["ProductName"].'</a></li>';
								 }	
								 echo '</ul>';
							echo '</li>';
						}
						else
						{
							echo '<li><a href="#">'.$all_categories_data[$i]["CategoryName"].'</a></li>';
						}
					}
				?>
            </ul>
        </li>
        <li <?php if($IsProjects == "N"){echo 'style="display:none;"';}?>><a href="projects.php">PROJECTS</a></li>
      	<li <?php if($IsNewsEvents == "N"){echo 'style="display:none;"';}?>><a href="news-and-events.php">NEWS &amp; EVENTS</a></li>
      	<li <?php if($IsBlogs == "N"){echo 'style="display:none;"';}?>><a href="blogs.php">BLOGS</a></li>
      	<li <?php if($IsCareers == "N"){echo 'style="display:none;"';}?>><a href="careers.php">CAREERS</a></li>
      	<?php
        
            if($custome_menu_data != NULL)
            {
                for($c=0; $c<count($custome_menu_data); $c++)
                {
                    if($custome_menu_data[$c]["DisplayIn"] == "Header" || $custome_menu_data[$c]["DisplayIn"] == "Both")
                    {
                        $url = "page.php?MenuName=".$custome_menu_data[$c]["MenuName"];
                        if($custome_menu_data[$c]["PageURL"] != "")
                        {
                            $url = $custome_menu_data[$c]["PageURL"];
                        }
                        
                        $has_submenus = $obj->fSelectRowCountNew("Select COUNT(*) from custome_menu where ParentId=".$custome_menu_data[$c]["MenuId"]);
                        if($has_submenus > 0)
                        {
                            
                            echo '<li class="has-sub"><a href="'.$url.'">'.$custome_menu_data[$c]["MenuName"].'</a><ul>';
                            $FieldNames=array("MenuId","MenuName","PageURL");
                            $ParamArray=array();
                            $ParamArray[0]= $custome_menu_data[$c]["MenuId"];
                            $Fields=implode(",",$FieldNames);
                            $sub_menu_data=$obj->MysqliSelect1("Select ".$Fields." from custome_menu Where ParentId = ?",$FieldNames,"i",$ParamArray);
                            
                            for($s=0; $s<=count($sub_menu_data); $s++)
                            {
                                $sub_menu_url = "page.php?MenuName=".$sub_menu_data[$s]["MenuName"];
                                if($sub_menu_data[$s]["PageURL"] != "")
                                {
                                    $sub_menu_url = $sub_menu_data[$s]["PageURL"];
                                }
                                echo '<li><a href="'.$sub_menu_url.'">'.$sub_menu_data[$s]["MenuName"].'</a></li>';
                            }
                            echo '</ul></li>';
                        }
                        else
                        {
                            echo '<li><a href="'.$url.'">'.$custome_menu_data[$c]["MenuName"].'</a></li>';
                        }
                    }
                }
            }
          ?>
      	<li <?php if($IsContactUs == "N"){echo 'style="display:none;"';}?>><a href="contact.php">CONTACT US</a></li>
        
    </ul>
</div><!-- .stellarnav -->