<?php
/**
 *
 * Created by GuLang on 2015-05-02.
 */

namespace app\controllers;


use app\models\Module;
use app\models\Project;
use app\tools\BaseUtils;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;
use app\models\Group;
use app\models\User;

class BaseController extends Controller
{
    public function init()
    {
        date_default_timezone_set('Asia/Shanghai');
        parent::init();
    }


    /**
     * 用户身份控制,没登录的用户不允许访问
     * @return \yii\web\Response
     */
    protected function auth()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('index.php?r=site/login');
        }
    }

    protected function permission()
    {

    }

    /**
     * 获得指定项目的模块信息
     * @param $projectId
     * @param array $column
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getModuleInfo($projectId, $column = ['id', 'name'])
    {
        if (isset($projectId)) {
            return Module::find()->select($column)->where(['project_id' => $projectId])->all();
        } else {
            return [];
        }
    }

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