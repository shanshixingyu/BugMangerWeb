<?php
/**
 * 用户角色（身份）类
 * Created by GuLang on 2015-04-20.
 */

namespace app\models;


use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    public static function tableName()
    {
        return 'role';
    }

}