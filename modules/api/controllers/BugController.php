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
use yii\base\Exception;
use yii\helpers\Json;
use app\tools\BugOpt;

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

                    $query = Bug::find()->select([
                        Bug::tableName() . '.id',
                        Bug::tableName() . '.name',
                        'priority',
                        'serious_id',
                        'status',
                        Bug::tableName() . '.create_time'
                    ])->joinWith(['assign'])->where($where)->addOrderBy(Bug::tableName() . '.create_time DESC');
                    $countQuery = clone $query;
                    $pagination = new Pagination([
                        'totalCount' => $countQuery->count(),
                        'pageSize' => 10,
                    ]);
                    $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

                    $result->code = MyConstant::VISIT_CODE_SUCCESS;
                    $result->message = "获取成功";
                    $result->result = [
                        'pageCount' => $pagination->getPageCount(),
                        'currentPage' => $pagination->getPage() + 1,
                        'data' => $bugs
                    ];;
                    return $result->parseJson();
                }
            }

        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = "不是post请求";
            return $result->parseJson();
        }
    }

    public function actionDetail($bugId)
    {
        $result = new HttpResult();
        $bug = Bug::find()->joinWith(['assign', 'creator', 'project'])->where([Bug::tableName() . '.id' => $bugId])->one();
//        var_dump($bug);
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取数据成功";
        $module = Module::find()->where(['id' => $bug->module_id])->one();
        $result->result = [
            'bug' => $bug,
            'assignName' => isset($bug->assign->name) ? $bug->assign->name : '',
            'creatorName' => isset($bug->creator->name) ? $bug->creator->name : '',
            'projectName' => isset($bug->project->name) ? $bug->project->name : '',
            'moduleName' => isset($module->name) ? $module->name : ''
        ];
        return $result->parseJson();
    }

    public function actionDownload($fileName)
    {
        $file = MyConstant::ATTACHMENT_PATH . $fileName;
        if (is_file($fileName)) {
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . basename($file));
            readfile($file);
            exit;
        } else {
            header("Content-Disposition: attachment; filename=" . basename($file));
            readfile($file);
            exit;
        }
    }

    public function actionSolve($bugId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            /* 步骤：1、取数据  2、验证bug是否存在  3、更新数据库信息 */
            //取数据
            $type = $_POST['type'];
            $introduce = $_POST['introduce'];

            //验证是否存在
            $bug = Bug::find()->where(['id' => $bugId])->one();
            if ($bug === null) {
                $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
                $result->message = '指定缺陷不存在';
                return $result->parseJson();
            }
            $bugStatus = Json::decode(BUG_STATUS);
            $bug->resolve_id = Yii::$app->user->identity->getId();
            date_default_timezone_set('Asia/Shanghai');
            $dateTime = date('Y-m-d H:i:s', time());
            $bug->resolve_time = $dateTime;
            if ($type == 0) {
                $bug->status = array_search(BUG_STATUS_SOLVED, $bugStatus);
                $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $introduce, '解决', $dateTime);
            } else {
                $bug->status = array_search(BUG_STATUS_OTHER, $bugStatus);
                $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $introduce, '改成其他状态', $dateTime);
            }
            $success = false;
            try {
                $success = $bug->update();
            } catch (Exception $e) {
                $success = false;
            }

            if ($success) {
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '成功解决';
            } else {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '解决失败';
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionActive($bugId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            /* 步骤：1、取数据  2、验证bug是否存在  3、更新数据库信息 */
            //取数据
            $reason = $_POST['reason'];

            //验证是否存在
            $bug = Bug::find()->where(['id' => $bugId])->one();
            if ($bug === null) {
                $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
                $result->message = '指定缺陷不存在';
                return $result->parseJson();
            }

            $bugStatus = Json::decode(BUG_STATUS);
            $bug->status = array_search(BUG_STATUS_ACTIVE, $bugStatus);
            date_default_timezone_set('Asia/Shanghai');
            $tempTime = date('Y-m-d H:i:s', time());
            $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $reason, '激活', $tempTime);
            ++$bug->active_num;
            $success = false;
            try {
                $success = $bug->update();
            } catch (Exception $e) {
                $success = false;
            }

            if ($success) {
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '成功激活';
            } else {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '激活失败';
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionClose($bugId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        if (Yii::$app->request->isPost) {
            /* 步骤：1、取数据  2、验证bug是否存在  3、更新数据库信息 */
            //取数据
            $reason = $_POST['reason'];

            //验证是否存在
            $bug = Bug::find()->where(['id' => $bugId])->one();
            if ($bug === null) {
                $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
                $result->message = '指定缺陷不存在';
                return $result->parseJson();
            }

            $bugStatus = Json::decode(BUG_STATUS);
            $bug->status = array_search(BUG_STATUS_CLOSED, $bugStatus);
            date_default_timezone_set('Asia/Shanghai');
            $tempTime = date('Y-m-d H:i:s', time());
            $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $reason, '关闭', $tempTime);
            $bug->close_time = $tempTime;
            $success = false;
            try {
                $success = $bug->update();
            } catch (Exception $e) {
                $success = false;
            }

            if ($success) {
                $result->code = MyConstant::VISIT_CODE_SUCCESS;
                $result->message = '关闭成功';
            } else {
                $result->code = MyConstant::VISIT_CODE_FAILURE;
                $result->message = '关闭失败';
            }
            return $result->parseJson();
        } else {
            $result->code = MyConstant::VISIT_CODE_NO_POST;
            $result->message = '不是POST请求';
            return $result->parseJson();
        }
    }

    public function actionDelete($bugId)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $bug = Bug::find()->where(['id' => $bugId])->one();
        if ($bug === null) {
            $result->code = MyConstant::VISIT_CODE_NOT_EXIST;
            $result->message = '指定缺陷不存在';
            return $result->parseJson();
        }

        $success = false;
        try {
            $success = $bug->delete();
        } catch (Exception $e) {
            $success = false;
        }
        if ($success) {
            $result->code = MyConstant::VISIT_CODE_SUCCESS;
            $result->message = '删除缺陷成功';
        } else {
            $result->code = MyConstant::VISIT_CODE_FAILURE;
            $result->message = '删除缺陷失败';
        }
        return $result->parseJson();
    }

    public function actionGetAllProject()
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $projects = Project::find()->select(['id', 'name'])->all();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = '项目信息获取成功';
        $result->result = $projects;
        return $result->parseJson();
    }

//    public function actionCharts($projectId, $startTime, $endTime)
//    {
//        $startDate = date('Y-m-d', $startTime);
//        $endDate = date('Y-m-d', $endTime);
//        $data = [];
//        for ($i = 0; $i < 7; $i++) {
//            $func = BugOpt::getEchartFunction($i);
//            $data[] = BugOpt::$func($projectId, $startDate, $endDate);
//        }
//        return $this->renderPartial('charts', ['data' => $data]);
//    }

    public function actionCharts($projectId, $startTime, $endTime)
    {
        $result = new HttpResult();
        if (Yii::$app->user->isGuest) {
            $result->code = MyConstant::VISIT_CODE_NO_LOGIN;
            $result->message = 'sorry，您还没有登录';
            return $result->parseJson();
        }

        $startDate = date('Y-m-d', $startTime);
        $endDate = date('Y-m-d', $endTime);

        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $func = BugOpt::getEchartFunction($i);
            $data[] = BugOpt::$func($projectId, $startDate, $endDate);
        }

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = '获取成功';
        $result->result = $data;
        return $result->parseJson();
    }

    public function actionTest()
    {
        $query = Bug::find()->select([Bug::tableName() . '.id', Bug::tableName() . '.name', 'priority', 'serious_id', 'status', Bug::tableName() . '.create_time'])->joinWith(['assign'])->addOrderBy(Bug::tableName() . '.create_time DESC')->all();
        var_dump($query);
    }

}