<?php
/**
 *
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\base\Exception;

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

    /**
     * 获得模块的负责人信息
     * @param null $selectColumn
     * @return array|\yii\db\ActiveRecord[]
     * @throws Exception
     */
    public function getFzrMember($selectColumn = null)
    {
        /* 防止没有查询出该项的情况 */
        if (isset($this->fuzeren)) {
            $memberId = Json::decode($this->fuzeren);
            if ($selectColumn === null)
                return User::find()->where(['id' => $memberId])->all();
            else
                return User::find()->select($selectColumn)->where(['id' => $memberId])->all();
        } else {
            throw new Exception('没有查询出负责人信息字段');
        }
    }

}