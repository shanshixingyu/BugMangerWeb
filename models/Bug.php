<?php
/**
 *
 * Created by GuLang on 2015-05-04.
 */

namespace app\models;


use yii\db\ActiveRecord;

class Bug extends ActiveRecord
{
    public static function tableName()
    {
        return 'bug';
    }

    public function getAssign()
    {
        return $this->hasOne(User::className(), ['id' => 'assign_id'])
            ->from(['assign' => User::tableName()]);
    }

    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id'])
            ->from(['creator' => User::tableName()]);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

}