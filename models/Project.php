<?php
/**
 * 项目信息模型
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;


class Project extends ActiveRecord
{
    public static function tableName()
    {
        return 'project';
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator']);
    }

}