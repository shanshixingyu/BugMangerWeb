<?php
/**
 * 团队模型
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;

class Group extends ActiveRecord
{
    public static function tableName()
    {
        return 'group';
    }

    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator']);
    }
}