<?php
/**
 * 用户相关控制器
 * Created by GuLang on 2015-04-29.
 */

namespace app\controllers;

use app\models\UserGroup;
use Yii;
use app\models\Role;
use app\models\User;
use app\models\UserForm;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\base\Exception;

class UserController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->joinWith(['role', 'createUser']),
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdd()
    {
        $userForm = new UserForm();

        if (isset($_POST['UserForm']) && $userForm->loadData() && $userForm->validate()) {
            $result = $userForm->addUserToDb();
            if (isset($result) && $result) {
                /* 数据插入成功 */
                return $this->render('opt_result', ['message' => '用户信息保存成功！']);
            } else {
                /* 数据插入失败 */
                return $this->render('opt_result', ['message' => '用户信息保存失败！']);
            }
        }

        $roles = Role::find()->where('id>0')->all();
        return $this->render('edit', [
            'userForm' => $userForm,
            'roles' => $roles,
            'isAdd' => true,
        ]);
    }

    public function actionModify($id)
    {
        $userForm = new UserForm();
        $userForm->isModify = true;
        $userForm->id = $id;

        if (isset($_POST['UserForm']) && $userForm->loadData()) {
            if ($userForm->validate()) {
                $result = $userForm->modifyUserOfDb();
                if (isset($result) && $result) {
                    /* 数据插入成功 */
                    return $this->render('opt_result', ['message' => '用户信息修改成功！']);
                } else {
                    /* 数据插入失败 */
                    return $this->render('opt_result', ['message' => '用户信息修改失败！']);
                }
            }
        } else {
            $user = User::findOne(['id' => $id]);
            $userForm->name = $user->name;
            $userForm->roleId = $user->role_id;
            $userForm->email = $user->email;
        }

        $roles = Role::find()->where('id>0')->all();
        return $this->render('edit', [
            'userForm' => $userForm,
            'roles' => $roles,
            'isAdd' => false,
        ]);
    }

    public function actionDelete($userId)
    {
        /* 删除用户的同时，也需要其从用户组（团队）中删除 */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            UserGroup::deleteAll(['user_id' => $userId]);
            User::deleteAll(['id' => $userId]);
            $transaction->commit();
            echo 'success';
        } catch (Exception $e) {
            $transaction->rollBack();
            echo 'failure';
        }
    }

}