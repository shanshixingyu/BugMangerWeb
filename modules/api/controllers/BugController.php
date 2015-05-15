<?php
/**
 *
 * Created by GuLang on 2015-05-12.
 */

namespace app\modules\api\controllers;

use Yii;
use app\models\Module;
use app\models\Project;
use app\modules\api\models\HttpResult;
use app\tools\MangerUtils;
use app\tools\MyConstant;
use yii\data\Pagination;
use app\models\Bug;

class BugController extends BaseController
{
    public function actionIndex()
    {
        $query = Project::find()->select(['id', 'name']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 8,
        ]);
        $projects = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        $bugData = [];
        foreach ($projects as $project) {
            $ProjectBug['projectId'] = $project->id;
            $ProjectBug['projectName'] = $project->name;
            $ProjectBug['projectBugCount'] = $project->getBugCount();
            $ProjectBug['bugStatusUnsolved'] = $project->getStatusBugCount(BUG_STATUS_UNSOLVED);
            $ProjectBug['bugStatusActive'] = $project->getStatusBugCount(BUG_STATUS_ACTIVE);
            $ProjectBug['bugStatusSolved'] = $project->getStatusBugCount(BUG_STATUS_SOLVED);
            $ProjectBug['bugStatusClosed'] = $project->getStatusBugCount(BUG_STATUS_CLOSED);
            $bugData[] = $ProjectBug;
        }

        $result = new HttpResult();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = 'get page bug info success';
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $bugData
        ];
        return $result->parseJson();

    }

    public function actionGetModuleMember($projectId)
    {
        $result = new HttpResult();
        $modules = Module::find()->select(['id', 'name'])->where(['project_id' => $projectId])->all();
        $users = MangerUtils::getGroupMember($projectId);
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取模块成功";
        $result->result = ["modules" => $modules, "users" => $users];
        return $result->parseJson();
    }

    public function actionGetAssign($projectId, $moduleId)
    {
        if ($moduleId <= 0)
            $assign = MangerUtils::getGroupMember($projectId);
        else
            $assign = MangerUtils::getModuleFzr($moduleId);
        $result = new HttpResult();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取指派人成功";
        $result->result = $assign;
        return $result->parseJson();
    }

    public function actionSearchBug()
    {
        $result = new HttpResult();
        if (Yii::$app->request->isPost) {
            if (!isset($_POST['projectId'])) {
                $result->code = MyConstant::VISIT_CODE_NO_POST;
                $result->message = "没有传送查询条件";
                return $result->parseJson();
            } else {
                //判断项目存在否
                $projectId = $_POST['projectId'];
                $project = Project::find()->where(['id' => $projectId])->one();
                if ($project == null) {
                    $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
                    $result->message = "项目不存在";
                    return $result->parseJson();
                } else {
                    $where = ['project_id' => $projectId];
                    if (isset($_POST['moduleId'])) {
                        $where['module_id'] = $_POST['moduleId'];
                    }
                    if (isset($_POST['priority'])) {
                        $where['priority'] = $_POST['priority'];
                    }
                    if (isset($_POST['serious'])) {
                        $where['serious_id'] = $_POST['serious'];
                    }
                    if (isset($_POST['assign'])) {
                        $where['assign_id'] = $_POST['assign'];
                    }
                    if (isset($_POST['submit'])) {
                        $where['creator_id'] = $_POST['submit'];
                    }
                    if (isset($_POST['status'])) {
                        $where['status'] = $_POST['status'];
                    }

                    $query = Bug::find()->joinWith(['assign'])->where($where)->addOrderBy(Bug::tableName() . '.create_time DESC');
                    $countQuery = clone $query;
                    $pagination = new Pagination([
                        'totalCount' => $countQuery->count(),
                        'pageSize' => 8,
                    ]);
                    $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

                    $result->code = MyConstant::VISIT_CODE_SUCCESS;
                    $result->message = "获取成功";
                    $result->result = $bugs;
                    return $result->parseJson();
                }
            }

        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = "不是post请求";
            return $result->parseJson();
        }
    }


}