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
use yii\data\ActiveDataProvider;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;

class UserController extends BaseController
{
    public function init()
    {
        parent::init();
        if (isset(Yii::$app->user->identity->role_id)) {
            $myRole = Yii::$app->user->identity->role_id;
            if ($myRole != 0 && $myRole != 1) {
                $this->redirect('index.php?r=bug/index');
            }
        }
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'add', 'modify'],
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
        $this->auth();

        $userForm = new UserForm();
        if (isset($_POST['UserForm']) && $userForm->loadData() && $userForm->validate()) {
            $result = $userForm->addUserToDb();
            if (isset($result) && $result) {
                /* 数据插入成功 */
                Yii::$app->session->setFlash(OPT_RESULT, '用户信息保存成功！');
                return $this->refresh();
            } else {
                /* 数据插入失败 */
                Yii::$app->session->setFlash(OPT_RESULT, '用户信息保存失败！');
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
        $this->auth();

        $userForm = new UserForm();
        $userForm->isModify = true;
        $userForm->id = $id;

        if (isset($_POST['UserForm']) && $userForm->loadData()) {
            if ($userForm->validate()) {
                $result = $userForm->modifyUserOfDb();
                if (isset($result) && $result) {
                    /* 数据插入成功 */
                    Yii::$app->session->setFlash(OPT_RESULT, '用户信息修改成功！');
                    return $this->refresh();
                } else {
                    /* 数据插入失败 */
                    Yii::$app->session->setFlash(OPT_RESULT, '用户信息修改失败！');
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
        /* 删除用户的同时，也需要其从用户组（团队）中删除,这个暂时还没做,不过这个问题在界面上是不会出现问题,后期完善 */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            User::deleteAll(['id' => $userId]);
            $transaction->commit();
            echo 'success';
        } catch (Exception $e) {
            $transaction->rollBack();
            echo 'failure';
        }
    }

}