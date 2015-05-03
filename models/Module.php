<?php
/**
 *
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;

class Module extends ActiveRecord
{
    public $tempCreateUser;/*备用*/
    public $tempProject;/*备用*/


    public static function tableName()
    {
        return 'module';
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator']);
    }

}