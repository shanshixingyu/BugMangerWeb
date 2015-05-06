<?php
/**
 *
 * Created by GuLang on 2015-05-05.
 */
namespace app\tools;

use yii\base\Exception;
use yii\web\UploadedFile;

class ImageUtils
{
    public static function uploadImageOpt($bugForm)
    {
        $bugForm->images = UploadedFile::getInstances($bugForm, 'images');
        $imageNames = [];
        $index = 0;
        foreach ($bugForm->images as $image) {
            $tempName = time() . $index . $image->getExtension();
            $tempFilePath = MyConstant::IMAGE_PATH . $tempName;
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
        $func = "imagecreatefrom{$imageType}";
        $srcImage = $func($srcImageFile);

        $saveFunc = "image{$imageType}";

        /**************************************大图****************************************/

        /* 根据获得的图片的大小，计算得到标准图片的大小和缩放比例 */
        $bigHeight = MyConstant::IMAGE_BIG_HEIGHT;
        $bigScale = $bigHeight / $height;
        $bigWidth = $width * $bigScale;
        /* 新建新图片 */
        $bigImage = imagecreatetruecolor($bigWidth, $bigHeight);
        /* 将原图片缩放并且存在新建图片上 */
        $isSuccess = imagecopyresampled($bigImage, $srcImage, 0, 0, 0, 0, $bigWidth, $bigHeight, $width, $height);
        if (!$isSuccess) {
            imagedestroy($bigImage);
            imagedestroy($srcImage);
            return false;
        }
        /* 将缩放好的图片保存到磁盘上 */
        $dstImageFile = MyConstant::BIG_IMAGE_PATH . $dstImageFileName;
        $isSuccess = $saveFunc($bigImage, $dstImageFile);
        /* 将内存中的大图片回收 */
        imagedestroy($bigImage);
        if (!$isSuccess) {
            imagedestroy($srcImage);
            return false;
        }


        /**************************************小图****************************************/

        /* 根据获得的图片的大小，计算得到标准图片的大小和缩放比例 */
        $smallHeight = MyConstant::IMAGE_SMALL_HEIGHT;
        $smallScale = $smallHeight / $height;
        $smallWidth = $width * $smallScale;
        /* 新建新图片 */
        $smallImage = imagecreatetruecolor($smallWidth, $smallHeight);
        /* 将原图片缩放并且存在新建图片上 */
        $isSuccess = imagecopyresampled($smallImage, $srcImage, 0, 0, 0, 0, $smallWidth, $smallHeight, $width, $height);
        if (!$isSuccess) {
            imagedestroy($smallImage);
            imagedestroy($srcImage);
            return false;
        }
        /* 将缩放好的图片保存到磁盘上 */
        $dstImageFile = MyConstant::SMALL_IMAGE_PATH . $dstImageFileName;
        $isSuccess = $saveFunc($smallImage, $dstImageFile);
        /* 将内存中的大图片回收 */
        imagedestroy($smallImage);
        imagedestroy($srcImage);
        if (!$isSuccess) {
            return false;
        }

        return true;
    }
}