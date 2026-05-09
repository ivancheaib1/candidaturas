<?php 
/*
function resizeImage($resourceType, $image_width, $image_height, $resizeWidth, $resizeHeight)
{
    // $resizeWidth = 100;
    // $resizeHeight = 100;
    $resizeHeight = $image_height*((($resizeWidth*100)/$image_width)/100);
    $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
    imagecopyresampled($imageLayer, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_width, $image_height);
    return $imageLayer;
}

function cambiartamanio($file , $new_width, $new_height){
        $imageProcess = 0;
 
        $fileName = $_FILES[$file]['tmp_name'];
        $uploadPath = '../imagenes/'.$_GET['ruta'].'/';
        $fileExt = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
        
        $sourceProperties = getimagesize($fileName);
        $resizeFileName = $imageProcess = time(). '.' . $fileExt;  
        $uploadImageType = $sourceProperties[2];
        $sourceImageWidth = $sourceProperties[0];
        $sourceImageHeight = $sourceProperties[1];
        switch ($uploadImageType)
        {
            case IMAGETYPE_JPEG:
                $resourceType = imagecreatefromjpeg($fileName);
                $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_width, $new_height);
                imagejpeg($imageLayer, $uploadPath.$resizeFileName);
            break;

            case IMAGETYPE_GIF:
                $resourceType = imagecreatefromgif($fileName);
                $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_width, $new_height);
                imagegif($imageLayer, $uploadPath.$resizeFileName);
            break;

            case IMAGETYPE_PNG:
                $resourceType = imagecreatefrompng($fileName);
                $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_width, $new_height);
                imagepng($imageLayer, $uploadPath .$resizeFileName);
            break;

            case IMAGETYPE_JPG:
                $resourceType = imagecreatefrompng($fileName);
                $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_width, $new_height);
                imagepng($imageLayer, $uploadPath.$resizeFileName);
            break;

            default:
                $imageProcess = 0;
            break;
        }
        //Subir la imagen original
        //move_uploaded_file($fileName, $uploadPath . $resizeFileName . "." . $fileExt);
        echo $imageProcess;
    
}*/
//cambiartamanio('file', 400, 400);

if(isset($_FILES['file']['name'])){


    //$filename = $_FILES['file']['name'];


    
    $imageFileType = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);
	$filename = time().'.'.$imageFileType;
	$location = '../imagenes/'.$_GET['ruta'].'/'.$filename;
 
    $valid_extensions = array("jpg","jpeg","png", "webp");

    $response = 0;
  
    if(in_array(strtolower($imageFileType), $valid_extensions)) {
 
        if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
            $response = $filename;
        }
    }

    echo $response;
    exit;
}
echo 0;


?>