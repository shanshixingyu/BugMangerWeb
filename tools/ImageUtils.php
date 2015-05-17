<?php
/**
 *
 * Created by GuLang on 2015-05-05.
 */
namespace app\tools;

use yii\base\Exception;
use yii\helpers\Json;
use yii\web\UploadedFile;

class ImageUtils
{
    public static function deleteImage($imageNames)
    {
        if (isset($imageNames) && trim($imageNames) != '') {
            $imagePaths = Json::decode($imageNames);
            foreach ($imagePaths as $imagePath) {
                unlink(MyConstant::IMAGE_PATH . $imagePath);
            }
        }
    }

    /**
     * 上传文件的处理操作
     * @param $bugForm
     * @return array
     * @throws Exception
     */
    public static function uploadImageOpt($bugForm)
    {
        $imageNames = [];
        $index = 0;
        foreach ($bugForm->images as $image) {
            $tempName = time() . $index . '.' . $image->getExtension();
            $tempFilePath = MyConstant::IMAGE_PATH . "temp/" . $tempName;
            $isSuccess = $image->saveAs($tempFilePath);
            if (!$isSuccess)
                continue;
            $isSuccess = ImageUtils::scaleUploadImage($tempFilePath);
            if ($isSuccess)
                $imageNames[] = $tempName;
            unlink($tempFilePath);
            $index++;
        }
        return $imageNames;
    }


    /**
     * 缩放图片,并且将其放入大小图片文件夹中
     * @param $srcImageFile
     * @param null $dstImageFileName
     * @return bool
     * @throws Exception
     */
    public static function scaleUploadImage($srcImageFile, $dstImageFileName = null)
    {
        /* 原图片文件不存在，抛出异常 */
        if (!file_exists($srcImageFile))
            throw new Exception('缩放图片方法中的原图片不存在');
        if ($dstImageFileName === null || trim($dstImageFileName) == '')
            $dstImageFileName = basename($srcImageFile);

        /* 获得原图片的大小 */
        list($width, $height, $type, $attr) = getimagesize($srcImageFile);
        /* 将原图片读入内存中 */
        $imageType = image_type_to_extension($type, false);
        $readFunc = "imagecreatefrom{$imageType}";
        $srcImage = $readFunc($srcImageFile);

        $saveFunc = "image{$imageType}";

        /* 根据获得的图片的大小，计算得到标准图片的大小和缩放比例 */
        $newHeight = MyConstant::IMAGE_HEIGHT;
        $newScale = $newHeight / $height;
        $bigWidth = $width * $newScale;
        /* 新建新图片 */
        $newImage = imagecreatetruecolor($bigWidth, $newHeight);
        /* 将原图片缩放并且存在新建图片上 */
        $isSuccess = imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $bigWidth, $newHeight, $width, $height);
        if (!$isSuccess) {
            imagedestroy($newImage);
            imagedestroy($srcImage);
            return false;
        }
        /* 将缩放好的图片保存到磁盘上 */
        $dstImageFile = MyConstant::IMAGE_PATH . $dstImageFileName;
        $isSuccess = $saveFunc($newImage, $dstImageFile);
        /* 将内存中的图片回收 */
        imagedestroy($newImage);
        imagedestroy($srcImage);
        if (!$isSuccess) {
            return false;
        }
        return true;
    }
}