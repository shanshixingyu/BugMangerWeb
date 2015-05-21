<?php
/**
 *
 * Created by GuLang on 2015-05-19.
 */

namespace app\modules\api\controllers;

use Yii;
use app\modules\api\models\HttpResult;
use app\tools\MyConstant;
use yii\data\Pagination;
use app\models\User;
use app\tools\PasswordUtils;
use yii\base\Exception;

class UserController extends BaseController
{
    public function actionIndex()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $query = User::find()->select(['id', 'name', 'role_id', 'email', 'creator']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $users = $query->limit($pagination->limit)->offset($pagination->offset)->all();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = '获得用户信息成功';
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $users
        ];
        return $result->parseJson();
    }

    public function actionAdd()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            /* 操作流程：1、取数据；2、验证用户名的唯一性；3、插入数据库 */

            //取数据
            $name = $_POST['name'];
            $roleId = $_POST['roleId'];
            $email = $_POST['email'];

            //验证数据的唯一性
            $user = User::findOne(['name' => $name]);
            if ($user !== null) {
                $result->code = MyConstant::VISIT_CODE_ADD_EXIST;
                $result->message = '用户已经存在';
                return $result->parseJson();
            }

            //插入数据库
            $user = new User();
            $user->name = $name;
            $user->password = PasswordUtils::getEncryptedPassword();
            $user->role_id = $roleId;
            $user->email = $email;
            $user->creator = Yii::$app->user->identity->getId();
            date_default_timezone_set('Asia/Shanghai');
            $user->create_time = date('Y-m-d H:i:s', time());
            $user->setIsNewRecord(true);
            $success = false;
            try {
                $success = $user->save();
            } catch (Exception $e) {
                $success = false;
            }
            if ($success) {
                //插入成功
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '添加用户成功';
            } else {
                //插入成功
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '添加用户失败';
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionDelete($userId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $user = User::find()->where(['id' => $userId])->one();
        if ($user === null) {
            $result->code = MyConstant::VISIT_CODE_OUT_DATE;
            $result->message = '用户信息过期';
            return $result->parseJson();
        }
        if ($user->creator != Yii::$app->user->identity->getId()) {
            $result->code = MyConstant::VISIT_CODE_NOT_PERMISSION;
            $result->message = '无删除权限';
            return $result->parseJson();
        }
        if ($user->delete()) {
            $result->code = MyConstant::VISIT_CODE_SUCCESS;
            $result->message = '删除用户成功';
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_FAILURE;
            $result->message = '删除用户失败';
            return $result->parseJson();
        }
    }


}