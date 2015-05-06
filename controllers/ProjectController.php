<?php
/**
 *
 * Created by GuLang on 2015-04-26.
 */

namespace app\controllers;

use app\models\Module;
use app\models\Project;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use app\models\Group;
use app\models\ModuleForm;
use app\models\User;
use app\models\ProjectForm;
use Yii;


class ProjectController extends BaseController
{
    public function init()
    {
        parent::init();
        $myRole = Yii::$app->user->identity->role_id;
        if ($myRole != 0 && $myRole != 1) {
            $this->redirect('index.php?r=bug/index');
        }
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'add-project', 'modify-project', 'add-module', 'modify-module'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'verbs' => ['?']
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->auth();

        $dataProvider = new ActiveDataProvider([
            'query' => Project::find()->joinWith(['createUser', 'group']),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider,]);
    }

    public function actionSeeModule($projectId)
    {
        $this->auth();

        $projectModules = Module::find()->joinWith(['createUser'])->where(['project_id' => $projectId])->all();
        foreach ($projectModules as $projectModule) {
            /* 对模块创建者信息进行二次处理 */
            if (isset($projectModule->createUser) && isset($projectModule->createUser->name)) {
                $projectModule->creator = $projectModule->createUser->name;
            } else {
                $projectModule->creator = "";
            }
            /* 对负责人信息进行二次处理 */
            if (isset($projectModule->fuzeren)) {
                $fzrs = Json::decode($projectModule->fuzeren);
                if (is_array($fzrs)) {
                    $fzrUsers = User::find()->select('name')->where(['id' => $fzrs])->all();
                    $tempFzr = '';
                    foreach ($fzrUsers as $fzrUser) {
                        $tempFzr .= $fzrUser->name . ' , ';
                    }
                    $tempFzr = substr($tempFzr, 0, strlen($tempFzr) - 3);/*去掉最后的' , ' */
                    if ($tempFzr === false)/* 当数据库中出现‘[]’或者‘[""]’的时候截取字符串会返回false */
                        $tempFzr = '';
                    $projectModule->fuzeren = $tempFzr;
                } else {
                    $projectModule->fuzeren .= '';
                }
            } else {
                $projectModule->fuzeren = '';
            }
        }

        return Json::encode($projectModules);
    }

    public function actionAddProject()
    {
        $this->auth();

        $projectForm = new ProjectForm();

        if (isset($_POST['ProjectForm']) && $projectForm->loadData() && $projectForm->validate()) {
            $result = $projectForm->addProjectToDb();
            if ($result) {
                /* 数据修改成功 */
                Yii::$app->session->setFlash(OPT_RESULT, '项目信息添加成功！');
                return $this->refresh();
            } else {
                /* 数据修改失败 */
                Yii::$app->session->setFlash(OPT_RESULT, '项目信息添加失败！');
            }
        }

        $groups = Group::find()->select(['id', 'name'])->all();
        return $this->render('edit_project', [
            'projectForm' => $projectForm,
            'groups' => $groups,
            'isAdd' => true
        ]);
    }

    public function actionModifyProject($id)
    {
        $this->auth();

        $projectForm = new ProjectForm();

        if (isset($_POST['ProjectForm']) && $projectForm->loadData()) {
            $projectForm->id = $id;
            $projectForm->isModify = true;
            if ($projectForm->validate()) {
                $result = $projectForm->modifyProjectOfDb($id);
                if ($result) {
                    /* 数据修改成功 */
                    Yii::$app->session->setFlash(OPT_RESULT, '项目信息修改成功！');
                    return $this->refresh();
                } else {
                    /* 数据修改失败 */
                    Yii::$app->session->setFlash(OPT_RESULT, '项目信息修改失败！');
                }
            }
        } else {
            $project = Project::findOne(['id' => $id]);
            $projectForm->name = $project->name;
            $projectForm->groupId = $project->group_id;
            $projectForm->introduce = $project->introduce;
        }

        $groups = Group::find()->select(['id', 'name'])->all();
        return $this->render('edit_project', [
            'projectForm' => $projectForm,
            'groups' => $groups,
            'isAdd' => false
        ]);
    }

    public function actionAddModule()
    {
        $this->auth();

        $moduleForm = new ModuleForm();
        if (isset($_POST['ModuleForm']) && $moduleForm->loadData() && $moduleForm->validate()) {
            $result = $moduleForm->addModuleToDb();
            if ($result) {
                /* 数据修改成功 */
                Yii::$app->session->setFlash(OPT_RESULT, '项目模块信息添加成功！');
                return $this->refresh();
            } else {
                /* 数据修改失败 */
                Yii::$app->session->setFlash(OPT_RESULT, '项目模块信息添加失败！');
            }
        }

        /* 获得所有项目信息 */
        $projects = Project::find()->select(['id', 'name', 'group_id'])->all();
        if (count($projects) > 0) {
            $group = Group::find()->select(['member'])->where(['id' => $projects[0]->group_id])->one();
            if ($group != null) {
                $memberIds = Json::decode($group->member);
                $groupMembers = User::find()->select(['id', 'name'])->where(['id' => $memberIds])->all();
            } else {
                $groupMembers = [];
            }
        } else {
            $groupMembers = [];
        }

        return $this->render('edit_module', [
            'moduleForm' => $moduleForm,
            'projects' => $projects,
            'groupMembers' => $groupMembers,
            'isAdd' => true,
        ]);
    }

    public function actionModifyModule($id)
    {
        $this->auth();

        $moduleForm = new ModuleForm();

        if (isset($_POST['ModuleForm']) && $moduleForm->loadData()) {
            $moduleForm->isModify = true;
            $moduleForm->projectId = $id;
            if ($moduleForm->validate()) {
                $result = $moduleForm->modifyModuleOfDb();
                if ($result) {
                    /* 数据修改成功 */
                    Yii::$app->session->setFlash(OPT_RESULT, '项目模块信息修改成功！');
                    return $this->refresh();
                } else {
                    /* 数据修改失败 */
                    Yii::$app->session->setFlash(OPT_RESULT, '项目模块信息修改失败！');
                }
            }
        }

        /* 获得所有项目信息 */
        $project = Project::find()->select(['name', 'group_id'])->where(['id' => $id])->one();
        $modules = [];
        $groupMembers = [];
        if ($project != null) {
            $moduleForm->projectName = $project->name;
//            $modules = ProjectModule::findAll(['project_id' => $id]);
            $modules = Module::find()->select(['id', 'name', 'fuzeren', 'introduce'])->where(['project_id' => $id])->all();
            if (count($modules) > 0) {
                $moduleForm->id = $modules[0]->id;
                $moduleForm->name = $modules[0]->name;
                $moduleForm->fuzeren = Json::decode($modules[0]->fuzeren);
                $moduleForm->introduce = $modules[0]->introduce;
            }

//            $groupMembers = UserGroup::find()->joinWith('user')->where(['group_id' => $project->group_id])->all();
            $group = Group::find()->where(['id' => $project->group_id])->one();
            if ($group != null)
                $groupMembers = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
            else
                $groupMembers = [];
        }

        return $this->render('edit_module', [
            'moduleForm' => $moduleForm,
            'modules' => $modules,
            'groupMembers' => $groupMembers,
            'isAdd' => false,
        ]);
    }

    public function actionGetModule($moduleId)
    {
        $this->auth();

        $module = Module::find()->select(['name', 'fuzeren', 'introduce'])->where(['id' => $moduleId])->one();
        echo Json::encode($module);
    }

    public function actionGetGroupMember($projectId)
    {
//        $this->auth();

        //由项目id找到groupId，然后由groupId找到userId,然后从userId找到useName
        $chooseProject = Project::find()->select(['group_id'])->where(['id' => $projectId])->one();
        if ($chooseProject == null)
            return '';
        $group = Group::find()->select(['member'])->where(['id' => $chooseProject->group_id])->one();
        if ($group != null)
            $allMembers = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
        else
            $allMembers = [];

        return Json::encode($allMembers);
    }

    public function actionDeleteProject($projectId)
    {
//        $this->auth();

        /* 删除项目的同时，也需要将项目的模块删除 */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Module::deleteAll(['project_id' => $projectId]);
            Project::deleteAll(['id' => $projectId]);
            $transaction->commit();
            echo 'success';
        } catch (Exception $e) {
            $transaction->rollBack();
            echo 'failure';
        }
    }

}