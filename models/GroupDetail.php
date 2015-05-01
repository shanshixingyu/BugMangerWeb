<?php
/**
 *
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;

class GroupDetail extends ActiveRecord
{
    public $member;


    public static function tableName()
    {
        return 'group_detail';
    }

    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator']);
    }

    public function getGroupIds()
    {
        return $this->hasMany(UserGroup::className(), ['group_id' => 'id']);
    }

}