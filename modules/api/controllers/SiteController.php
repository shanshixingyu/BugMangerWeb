<?php

namespace app\modules\api\controllers;

use app\models\Project;
use app\tools\MyConstant;
use Yii;
use app\models\User;
use app\modules\api\models\PhoneLoginUser;
use app\modules\api\models\HttpResult;
use app\tools\PasswordUtils;
use app\models\Group;

class SiteController extends BaseController
{
    public function actionIndex()
    {
        return $this->redirect('index.php?r=site/login');
    }

    public function actionLogin()
    {
        $result = new HttpResult();
        if (!Yii::$app->user->isGuest) {
            //已经登录过了
            $user = Yii::$app->user->identity;
            $phoneLoginUser = new PhoneLoginUser();
            $phoneLoginUser->userId = $user->id;
            $phoneLoginUser->userName = $user->name;
            $phoneLoginUser->password = $user->password;
            $phoneLoginUser->roleId = $user->role_id;
            $phoneLoginUser->roleName = $user->role->name;
            $result->code = MyConstant::VISIT_CODE_SUCCESS;
            $result->message = '登录成功';
            $result->result = $phoneLoginUser;
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
//            var_dump($_POST);
            //获得用户名、密码
            $userName = $_POST['name'];
            $password = $_POST['password'];
            $rememberMe = isset($_POST['rememberMe']) ? $_POST['rememberMe'] : false;
            //先将密码转换成加密密码
            $encryptedPassword = PasswordUtils::getEncryptedPassword($password);
            //验证用户名密码是否正确
            $user = User::find()->joinWith(['role'])->where([User::tableName() . '.name' => $userName, 'password' => $encryptedPassword])->one();
            //如果$user为null的话说明没有登录成功
            if ($user === null) {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '登录失败';
            } else {
                //系统登录
                $loginSuccess = Yii::$app->user->login($user, $rememberMe ? 3600 * 24 * 30 : 0);
                if (!$loginSuccess) {
                    //登录不成功
                    $result->code = 1;
                    $result->message = '登录失败';
                } else {
                    //登录成功
                    $phoneLoginUser = new PhoneLoginUser();
                    $phoneLoginUser->userId = $user->id;
                    $phoneLoginUser->userName = $user->name;
//                    $phoneLoginUser->password = $password;
                    $phoneLoginUser->password = $user->password;
                    $phoneLoginUser->roleId = $user->role_id;
                    $phoneLoginUser->roleName = $user->role->name;

                    $result->code = 0;
                    $result->message = '登录成功';
                    $result->result = $phoneLoginUser;
                }
            }
        } else {
            $result->code = -1;
            $result->message = '没有传输用户名和密码';
        }

        return $result->parseJson();
    }

    public function actionPim()
    {
        $userId = Yii::$app->user->identity->getId();
        //查找用户个人信息
        $user = User::find()->joinWith(['role', 'createUser'])->where([User::tableName() . '.id' => $userId])->one();
        //查找用户所在分组
        $groups = Group::find()->select(['id', 'name'])->where('member like "%' . $userId . '%"')->all();
        $groupIds = [];
        $groupNames = [];  //参与的所有团队
        foreach ($groups as $group) {
            $groupIds[] = $group->id;
            $groupNames[] = $group->name;
        }

        //通过参与的所有团队id组，找到所有参与的项目
        $projects = Project::find()->select([Project::tableName() . '.id', Project::tableName() . '.name'])->joinWith(['projectModules'])->where(['group_id' => $groupIds])->all();
        $projectModuleData = [];
        foreach ($projects as $project) {
            if (isset($project->projectModules) && is_array($project->projectModules) && count($project->projectModules) > 0) {
                $hasFlag = false;
                foreach ($project->projectModules as $module) {
                    if (strpos($module->fuzeren, '"' . $userId . '"')) {
                        $projectModuleData[] = ['projectName' => $project->name, 'moduleName' => $module->name];
                        $hasFlag = true;
                    }
                }
                if (!$hasFlag)
                    $projectModuleData[] = ['projectName' => $project->name, 'moduleName' => ''];
            } else {
                $projectModuleData[] = ['projectName' => $project->name, 'moduleName' => ""];
            }
        }

        $result = new HttpResult();
        $result->code = 0;
        $result->message = 'get message success';
        $result->result['user'] = $user;
        $result->result['roleName'] = $user->role->name;
        $result->result['creatorName'] = isset($user->createUser->name) ? $user->createUser->name : '';
        $result->result['groupNames'] = $groupNames;
        $result->result['projectModuleData'] = $projectModuleData;

        echo $result->parseJson();
    }

    public function actionLogout()
    {
        $result = new HttpResult();
        if (Yii::$app->user->logout()) {
            $result->code = MyConstant::VISIT_CODE_SUCCESS;
            $result->message = 'logout success';
        } else {
            $result->code = MyConstant::VISIT_CODE_FAILURE;
            $result->message = 'logout failure';
        }
        return $result->parseJson();
    }

    public function actionModifyEmail()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'no login';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            $user = Yii::$app->user->identity;
            if ($user == null) {
                $result->code = MyConstant::VISIT_CODE_NO_USER;
                $result->message = 'on user';
                return $result->parseJson();
            }
            //获得新邮箱,并且更新
            $tempEmail = $user->email;
            $user->email = $_POST['email'];
            if ($user->update()) {
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = 'modify email success';
            } else {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = 'modify email failure';
                //还原
                $user->email = $tempEmail;
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = 'not post';
            return $result->parseJson();
        }
    }

    public function actionModifyPassword()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'no login';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            $user = Yii::$app->user->identity;
            if ($user == null) {
                $result->code = MyConstant::VISIT_CODE_NO_USER;
                $result->message = 'on user';
                return $result->parseJson();
            }
            //获得传过来的原密码和新密码
            $oldPassword = $_POST["oldPassword"];
            $newPassword = $_POST["newPassword"];
            $encryptedPassword = PasswordUtils::getEncryptedPassword($oldPassword);
            if ($encryptedPassword != $user->password) {
                $result->code = MyConstant::VISIT_CODE_PASSWORD_WRONG;
                $result->message = "原密码错误";
                return $result->parseJson();
            }
            //赋值
            $user->password = PasswordUtils::getEncryptedPassword($newPassword);
//            $updateResult = User::updateAll(['password' => $encryptedPassword], ['id' => $user->getId()]);
            if ($user->update()) {
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = 'modify email success';
            } else {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = 'modify email failure';
                //恢复
                $user->password = $encryptedPassword;
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = 'not post';
            return $result->parseJson();
        }
    }

}
