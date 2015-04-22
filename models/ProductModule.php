<?php
/**
 *
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;

class ProductModule extends ActiveRecord
{
    public static function tableName()
    {
        return 'product_module';
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

}