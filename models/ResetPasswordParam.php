<?php
/**
 *
 * Created by GuLang on 2015-05-27.
 */

namespace app\models;


use yii\db\ActiveRecord;

class ResetPasswordParam extends ActiveRecord
{
    public static function tableName()
    {
        return 'reset';
    }


}