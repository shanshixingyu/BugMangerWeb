<?php
/**
 *
 * Created by GuLang on 2015-04-21.
 */

namespace app\models;


use yii\db\ActiveRecord;

class UserGroup extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_group';
    }

    public function getGroupDetail()
    {
        return $this->hasOne(GroupDetail::className(), ['id' => 'group_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}