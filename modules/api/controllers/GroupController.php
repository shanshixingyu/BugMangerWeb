<?php
/**
 *
 * Created by GuLang on 2015-05-20.
 */

namespace app\modules\api\controllers;

use app\models\User;
use Yii;
use app\modules\api\models\HttpResult;
use app\tools\MyConstant;
use app\models\Group;
use yii\data\Pagination;
use app\models\Project;
use yii\base\Exception;
use yii\helpers\Json;

class GroupController extends BaseController
{

    public function actionIndex()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $query = Group::find()->select(['id', 'name', 'introduce', 'creator']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $groups = $query->limit($pagination->limit)->offset($pagination->offset)->all();

        $result = new HttpResult();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获得团队信息成功";
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $groups
        ];
        return $result->parseJson();
    }
//    public function actionIndex()
//    {
//        $query = Group::find();
//        $countQuery = clone $query;
//        $pagination = new Pagination([
//            'totalCount' => $countQuery->count(),
//            'pageSize' => 10,
//        ]);
//        $groups = $query->limit($pagination->limit)->offset($pagination->offset)->all();
//        foreach ($groups as $group) {
//            $group->creator = User::find()->select(['id', 'name'])->where(['id' => $group->creator])->one();
//            $group->member = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
//        }
//
//        $result = new HttpResult();
//        $result->code = MyConstant::VISIT_CODE_SUCCESS;
//        $result->message = "获得团队信息成功";
//        $result->result = [
//            'pageCount' => $pagination->getPageCount(),
//            'currentPage' => $pagination->getPage() + 1,
//            'data' => $groups
//        ];
//        return $result->parseJson();
//    }

    public function actionGetAllUser()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $users = User::find()->select(['id', 'name'])->all();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取用户信息成功";
        $result->result = $users;

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
            /* 处理流程：1、取数据；2、验证团队名称的唯一性；3、插入数据库 */
            //取数据
            $name = $_POST['name'];
            $memberIds = $_POST['memberIds'];
            $introduce = $_POST['introduce'];

            //验证团队名称的唯一性
            $group = Group::findOne(['name' => $name]);
            if ($group !== null) {
                $result->code = MyConstant::VISIT_CODE_HAS_EXIST;
                $result->message = '已存在同名团队';
                return $result->parseJson();
            }

            //插入数据库
            $newGroup = new Group();
            $newGroup->name = $name;
            $newGroup->member = $memberIds;
            $newGroup->introduce = $introduce;
            $newGroup->creator = Yii::$app->user->identity->getId();
            $newGroup->create_time = date('Y-m-d H:i:s', time());
            $success = false;
            try {
                $success = $newGroup->insert();
            } catch (Exception $e) {
                $success = false;
            }
            if ($success) {
                //插入成功
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '添加团队成功';
            } else {
                //插入成功
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '添加团队失败';
            }
            return $result->parseJson();

        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionGetInfo($groupId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $group = Group::find()->joinWith(['createUser'])->where([Group::tableName() . '.id' => $groupId])->one();
        if ($group === null) {
            $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
            $result->message = '团队ID信息过期，请刷新重试';
            return $result->parseJson();
        }

        $allUser = User::find()->select(['id', 'name'])->all();

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = '获取团队信息成功';
        $result->result = [
            'group' => $group,
            'creatorName' => isset($group->createUser->name) ? $group->createUser->name : '',
            'allUser' => $allUser
        ];
        return $result->parseJson();
    }

    public function actionModify($groupId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            /* 处理流程：1、取数据；2、验证团队名称的唯一性和指定团队是否存在；3、更新数据库 */
            //取数据
            $name = $_POST['name'];
            $memberIds = $_POST['memberIds'];
            $introduce = $_POST['introduce'];

            //看要修改的团队信息是否存在
            $modifyGroup = Group::find()->where(['id' => $groupId])->one();
            if ($modifyGroup === null) {
                $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
                $result->message = '数据过期，请刷新重试';
                return $result->parseJson();
            }

            if ($modifyGroup->name != $name) {//说明团队名称发生了改变
                //当名称发生改变时，需要验证团队名称的唯一性
                $group = Group::findOne(['name' => $name]);
                if ($group !== null) {
                    $result->code = MyConstant::VISIT_CODE_HAS_EXIST;
                    $result->message = '已存在同名团队';
                    return $result->parseJson();
                }
            }

            //更新数据库
            $modifyGroup->name = $name;
            $modifyGroup->member = $memberIds;
            $modifyGroup->introduce = $introduce;
            $success = false;
            try {
                $success = $modifyGroup->update();
            } catch (Exception $e) {
                $success = false;
            }
            if ($success) {
                //插入成功
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '修改团队信息成功';
            } else {
                //插入成功
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '修改团队信息失败';
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionDelete($groupId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }
        $group = Group::find()->where(['id' => $groupId])->one();
        if ($group === null) {
            $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
            $result->message = '团队信息过期，请刷新重试';
            return $result->parseJson();
        }

        $project = Project::find()->where(['group_id' => $groupId])->one();
        if ($project !== null) {
            $result->code = MyConstant::VISIT_CODE_HAS_EXIST;
            $result->message = '该团队还负责着一些项目，无法删除！';
            return $result->parseJson();
        }

        $success = false;
        try {
            $success = $group->delete();
        } catch (Exception $e) {
            $success = false;
        }
        if ($success) {
            $result->code = MyConstant::VISIT_CODE_SUCCESS;
            $result->message = '删除成功';
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_FAILURE;
            $result->message = '删除失败';
            return $result->parseJson();
        }
    }
}