<?php
/**
 *
 * Created by GuLang on 2015-05-15.
 */

namespace app\tools;

use app\models\Project;
use app\models\Group;
use yii\helpers\Json;
use app\models\User;
use app\models\Module;


class MangerUtils
{
    /**
     * 获得项目成员
     * @param $projectId
     * @param array $memberColumn
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getGroupMember($projectId, $memberColumn = ['id', 'name'])
    {
        $project = Project::find()->select('group_id')->where(['id' => $projectId])->one();
        if ($project !== null) {
            $group = Group::find(['member'])->where(['id' => $project->group_id])->one();
            if ($group !== null) {
                $memberId = Json::decode($group->member);
                return User::find()->select($memberColumn)->where(['id' => $memberId])->all();
            }
        }
        return [];
    }

    /**
     * 获取模块负责人信息
     * @param $moduleId
     * @param array $fzrColumn
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getModuleFzr($moduleId, $fzrColumn = ['id', 'name'])
    {
        $module = Module::find()->select(['fuzeren'])->where(['id' => $moduleId])->one();
        if ($module !== null) {
            $fzrArr = Json::decode($module->fuzeren);
            return User::find()->select($fzrColumn)->where(['id' => $fzrArr])->all();
        }
        return [];
    }

}