<?php
/**
 *
 * Created by GuLang on 2015-05-14.
 */

namespace app\modules\api\controllers;


use app\models\Bug;
use app\models\Group;
use Yii;
use app\models\Module;
use app\models\Project;
use app\modules\api\models\HttpResult;
use app\tools\MyConstant;
use yii\data\Pagination;
use yii\base\Exception;


class ProjectController extends BaseController
{
    public function actionProjectModule()
    {
        $result = new HttpResult();
        $projects = Project::find()->select(['id', 'name'])->all();
        $projectModuleDatas = [];
        foreach ($projects as $project) {
            $projectModules = Module::find()->select(['id', 'name'])->where(['project_id' => $project->id])->all();
            $projectModuleDatas[] = ['project' => $project, 'modules' => $projectModules];
        }

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "成功获取";
        $result->result = $projectModuleDatas;
        return $result->parseJson();
    }

    public function actionIndex()
    {
        $query = Project::find()->select(['id', 'name', 'introduce', 'creator']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $groups = $query->limit($pagination->limit)->offset($pagination->offset)->all();

        $result = new HttpResult();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获得项目信息成功";
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $groups
        ];
        return $result->parseJson();
    }

    public function actionGetAllGroup()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $groups = Group::find()->select(['id', 'name'])->all();

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = '获取团队信息成功';
        $result->result = $groups;
        return $result->parseJson();
    }

    public function actionAddProject()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            /* 处理流程：1、取数据；2、验证项目名称的唯一性以及团队是否存在；3、插入数据库 */
            //取数据
            $name = $_POST['name'];
            $groupId = $_POST['groupId'];
            $introduce = $_POST['introduce'];

            //验证项目名称的唯一性
            $project = Project::find()->where(['name' => $name])->one();
            if ($project !== null) {
                $result->code = MyConstant::VISIT_CODE_HAS_EXIST;
                $result->message = '已存在同名项目';
                return $result->parseJson();
            }

            $group = Group::find()->where(['id' => $groupId])->one();
            if ($group === null) {
                $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
                $result->message = '项目信息过期，请刷新重试';
                return $result->parseJson();
            }

            $newProject = new Project();
            $newProject->name = $name;
            $newProject->group_id = $groupId;
            $newProject->creator = Yii::$app->user->identity->getId();
            $newProject->create_time = date('Y-m-d H:i:s', time());
            $newProject->introduce = $introduce;
            $success = false;
            try {
                $success = $newProject->insert();
            } catch (Exception $e) {
                $success = false;
            }
            if ($success) {
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '添加项目成功';
            } else {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '添加项目信息失败';
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionGetProject($projectId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $project = Project::find()->joinWith(['createUser'])->where([Project::tableName() . '.id' => $projectId])->one();
        if ($project === null) {
            $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
            $result->message = '指定项目不存在';
            return $result->parseJson();
        }

        $allGroup = Group::find()->select(['id', 'name'])->all();

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = '获取团队信息成功';
        $result->result = [
            'project' => $project,
            'creatorName' => isset($project->createUser->name) ? $project->createUser->name : '',
            'allGroup' => $allGroup
        ];
        return $result->parseJson();
    }

    public function actionDeleteProject($projectId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $project = Project::find()->where(['id' => $projectId])->one();
        if ($project === null) {
            $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
            $result->message = '项目信息过期，请刷新重试';
            return $result->parseJson();
        }

        /* 只有创建者或者超级管理员才有权限去操作 */
        $me = Yii::$app->user->identity;
        if ($me->role_id != 0 && $me->id != $project->creator) {
            $result->code = MyConstant::VISIT_CODE_NOT_PERMISSION;
            $result->message = '您没有删除权限';
            return $result->parseJson();
        }

        $transition = Yii::$app->db->beginTransaction();
        $success = false;
        try {
            $success = $project->delete();
            if (!$success) {
                $transition->rollBack();
            } else {
                Module::deleteAll(['project_id' => $projectId]);
                Bug::deleteAll(['project_id' => $projectId]);
                $transition->commit();
            }
        } catch (Exception $e) {
            $success = false;
            $transition->rollBack();
        }
        if ($success) {
            $result->code = MyConstant::VISIT_CODE_SUCCESS;
            $result->message = '删除项目信息成功';
        } else {
            $result->code = MyConstant::VISIT_CODE_FAILURE;
            $result->message = '删除项目信息失败';
        }
        return $result->parseJson();
    }

}