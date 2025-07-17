<?php

function resize($source,$dest,$width,$height)
{
    error_reporting(E_ALL);



    /*** the image file to thumbnail ***/
	 
    $image = $source;
	//echo $image;

    if(!file_exists($image))
    {
        echo 'No file found';
    }
    else
    {
		echo $image;
        /*** image info ***/
        list($width_orig, $height_orig, $image_type) = getimagesize($image);

        /*** check for a supported image type ***/
        if($image_type !== 3)
        {
            echo 'invalid image';
        }
        else
        {
            /*** thumb image name ***/
            $thumb =$dest;

            /*** maintain aspect ratio ***/
            $height = (int) (($width / $width_orig) * $height_orig);

            /*** resample the image ***/
            $image_p = imagecreatetruecolor($width, $height);
            $image = imageCreateFromJpeg($image);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

            /*** write the file to disc ***/
            if(!is_writeable(dirname($thumb)))
            {
                echo 'unable to write image in ' . dirname($thumb);
            }
            else
            {
                imageJpeg($image_p, $thumb, 1000);
				
            }
        }
    }
}


?>