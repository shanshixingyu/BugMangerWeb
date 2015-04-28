<?php
/**
 *
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;

class ProductModule extends ActiveRecord
{
    public $tempCreateUser;/*备用*/
    public $tempProduct;/*备用*/


    public static function tableName()
    {
        return 'product_module';
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator']);
    }

}