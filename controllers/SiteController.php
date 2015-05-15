<?php
/**
 * 用户登录控制器
 * Created by GuLang on 2015-04-16.
 */
namespace app\controllers;

use app\models\Module;
use app\models\User;
use app\models\Group;
use app\models\UserModifyForm;
use Yii;
use app\models\LoginForm;
use yii\filters\AccessControl;
use yii\helpers\Json;
use app\models\Project;

class SiteController extends BaseController
{
    public $oldUserModifyForm = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['bug', 'pim', 'manager'],
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

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'foreColor' => 0x00FF00,
                'backColor' => 0xD0F2FB,
                'width' => 75,
                'height' => 25,
                'padding' => 1,
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 0,
            ],
        ];
    }

    public function actionIndex()
    {
        //该方法没什么作用，主要是用来重定向的
        return $this->redirect('index.php?r=site/login');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $this->redirect('index.php?r=bug/index');
        }

        $loginForm = new LoginForm();
        if (isset($_POST['LoginForm'])) {
            /* 表示按了登录过后过来的 */
            if ($loginForm->load(Yii::$app->request->post("LoginForm")) && $loginForm->login()) {
                $this->redirect("index.php?r=bug/index");
            }
        }

        $loginForm->verifyCode = "";
        return $this->renderPartial("login", ['loginForm' => $loginForm]);
    }

    public function actionPim()
    {
        $this->auth();

        $userModifyForm = new UserModifyForm();

        if (isset($_POST['UserModifyForm']) && $userModifyForm->loadData() && $userModifyForm->validate()) {
            /* 表示修改个人信息部分过来的 */
            $result = $userModifyForm->modifyPimOfDb();
            if ($result) {
                /* 个人信息修改成功 */
                Yii::$app->session->setFlash(OPT_RESULT, '个人信息修改成功！');
                //要将登录的用户信息更新
                Yii::$app->user->identity = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
            } else {
                /* 个人信息修改失败 */
                Yii::$app->session->setFlash(OPT_RESULT, '个人信息修改失败！');
            }
            return $this->refresh();
        } else {
            //到这里表示修改信息验证不成功或者还没修改信息
            /*从中获得信息，并且最好还是能够在进入界面的时候重新访问下数据库，
                              保证数据最新,不过数据也一般只有自己能够修改，也可不修改*/
            $user = User::find()->joinWith(['role'])->where([User::tableName() . '.id' => Yii::$app->user->identity->getId()])->one();
            if ($user != null) {
                $userModifyForm->userId = $user->id;
                $userModifyForm->userName = $user->name;
                $userModifyForm->roleName = isset($user->role) && isset($user->role->name) ? $user->role->name : '';
                $userModifyForm->email = $user->email;
            }

        }
        /* 查询指定用户id下的所有组名 */
        $groups = Group::find()->where('member like "%' . $userModifyForm->userId . '%"')->all();
        $groupNames = [];
        $groupIds = [];
        foreach ($groups as $group) {
            $groupNames[] = $group->name;
            $groupIds[] = $group->id;
        }
        unset($groups);

//        /* 参与项目与模块 */
//        $where = 'fuzeren like "%\"' . $userModifyForm->userId . '\"%"';
//        $projectModules = Module::find()->joinWith('project')->where($where)->all();
//        $projectModuleData = [];
//        foreach ($projectModules as $projectModule) {
//            $projectModuleData[] = ['project' => $projectModule->project->name, 'module' => $projectModule->name];
//        }
//        unset($projectModules);

        //通过参与的所有团队id组，找到所有参与的项目
        $projects = Project::find()->select([Project::tableName() . '.id', Project::tableName() . '.name'])->joinWith(['projectModules'])->where(['group_id' => $groupIds])->all();
        $projectModuleData = [];
        foreach ($projects as $project) {
            if (isset($project->projectModules) && is_array($project->projectModules) && count($project->projectModules) > 0) {
                $hasFlag = false;
                foreach ($project->projectModules as $module) {
                    if (strpos($module->fuzeren, '"' . $userModifyForm->userId . '"')) {
                        $projectModuleData[] = ['project' => $project->name, 'module' => $module->name];
                        $hasFlag = true;
                    }
                }
                if (!$hasFlag)
                    $projectModuleData[] = ['project' => $project->name, 'module' => ''];
            } else {
                $projectModuleData[] = ['project' => $project->name, 'module' => ""];
            }
        }

        return $this->render("pim", [
            'userModifyForm' => $userModifyForm,
            'groupNames' => $groupNames,
            'projectModuleData' => $projectModuleData,
        ]);
    }

    public function actionManager()
    {
        $this->auth();
        /* 除了管理员和超级管理员外，其它人都不允许进入 */
        if (isset(Yii::$app->user->identity->role_id) && Yii::$app->user->identity->role_id < 2) {
            return $this->render('manager');
        } else {
            return $this->redirect('index.php?r=site/bug');
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('index.php?r=site/login');
    }

    public function actionGetModule($projectId)
    {
        $modules = Module::find()->select(['name'])->where(['project_id' => $projectId])->all();
        echo Json::encode($modules);
    }

}