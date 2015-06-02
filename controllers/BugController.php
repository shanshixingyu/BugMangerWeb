<?php
/**
 * Bug控制器
 * Created by GuLang on 2015-05-02.
 */

namespace app\controllers;

use app\models\ActiveBugForm;
use app\models\ResolveForm;
use app\models\SearchChartForm;
use app\tools\BugOpt;
use Yii;
use app\models\Bug;
use app\models\BugForm;
use app\models\Module;
use app\models\Project;
use app\models\SearchBugForm;
use app\tools\ImageUtils;
use app\tools\MyConstant;
use yii\base\Exception;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\UploadedFile;
use app\models\CloseBugForm;

class BugController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'bug', 'add', 'show', 'modify', 'delete',
                    'download', 'resolve', 'active', 'close', 'charts'],
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

        $query = Project::find()->select(['id', 'name']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 8,
        ]);
        $projects = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('index', ['projects' => $projects, 'pagination' => $pagination]);
    }

    public function actionBug($projectId)
    {
        $this->auth();
        /* 主要目的是为了确保获得的projectId是正确的 */
        $project = Project::find()->where(['id' => $projectId])->one();
        if ($project === null) {
            \Yii::$app->session->setFlash(OPT_RESULT, '指定的项目不存在，请重试!');
            return $this->redirect('index.php?r=bug/index');
        }

        $searchBugForm = new SearchBugForm();
        //查询条件
        $where = ['project_id' => $projectId];
//        if (isset(self::$searchBugPost)) {
//            var_dump($this->searchBugPost);
//        }

        if (isset($_POST['SearchBugForm']) && $searchBugForm->loadData()) {
            if (isset($searchBugForm->moduleId) && trim($searchBugForm->moduleId) != '') {
                $where['module_id'] = $searchBugForm->moduleId;
            }
            if (isset($searchBugForm->priority) && trim($searchBugForm->priority) != '') {
                $where['priority'] = $searchBugForm->priority;
            }
            if (isset($searchBugForm->seriousId) && trim($searchBugForm->seriousId) != '') {
                $where['serious_id'] = $searchBugForm->seriousId;
            }
            if (isset($searchBugForm->assignId) && trim($searchBugForm->assignId) != '') {
                $where['assign_id'] = $searchBugForm->assignId;
            }
            if (isset($searchBugForm->creatorId) && trim($searchBugForm->creatorId) != '') {
                $where['creator_id'] = $searchBugForm->assignId;
            }
            if (isset($searchBugForm->statusId) && trim($searchBugForm->statusId) != '') {
                $where['status'] = $searchBugForm->statusId;
            }
        }

        $query = Bug::find()->joinWith(['assign'])->where($where)->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 8,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('bug', [
            'searchBugForm' => $searchBugForm,
            'project' => $project,
            'bugs' => $bugs,
            'pagination' => $pagination,
        ]);
    }

    public function actionAdd()
    {
        $bugForm = new BugForm();
        if (isset($_POST['BugForm']) && $bugForm->loadData()) {
            //在验证之前，先将文件什么的读进来
            $bugForm->images = UploadedFile::getInstances($bugForm, 'images');
            $bugForm->attachment = UploadedFile::getInstance($bugForm, 'attachment');

            if ($bugForm->validate()) {
                //处理模块信息
                if (!isset($bugForm->moduleId) || trim($bugForm->moduleId) == '')
                    $bugForm->moduleId = MyConstant::OPTION_ALL;

                //处理上传的截图
                $imageNames = ImageUtils::uploadImageOpt($bugForm);
                $bugForm->images = Json::encode($imageNames);

                //处理上传的附件
                if (isset($bugForm->attachment) || trim($bugForm->attachment) != '') {
                    if ($bugForm->attachment !== null) {
                        $attachmentName = time() . $bugForm->attachment->name;
                        $bugForm->attachment->saveAs(MyConstant::ATTACHMENT_PATH . $attachmentName);
                        $bugForm->attachment = $attachmentName;
                    }
                }
                $result = $bugForm->addBugToDb();
                if ($result) {
                    //插入数据库成功
                    Yii::$app->session->setFlash(OPT_RESULT, 'Bug提交成功！');
                    return $this->redirect('index.php?r=bug/index');
                } else {
                    //插入数据库失败
                    Yii::$app->session->setFlash(OPT_RESULT, 'Bug提交失败！');
                }
            }
        }
        $projects = Project::find()->select(['id', 'name', 'group_id'])->all();
        $modules = [];
        if (count($projects) > 0) {
            $modules = Module::find()->select(['id', 'name'])->where(['project_id' => $projects[0]->id])->all();
        }

        return $this->render('edit', ['bugForm' => $bugForm, 'projects' => $projects, 'modules' => $modules]);
    }

    public function actionModify($bugId)
    {
        $bugForm = new BugForm();
        $bugForm->isModify = true;
        $bugForm->bugId = $bugId;

        $bug = Bug::find()->where(['id' => $bugId])->one();
        if ($bug === null) {
            Yii::$app->session->setFlash(OPT_RESULT, 'Bug信息过期，请刷新重试！');
            return $this->redirect('index.php?r=bug/index');
        }

        if (isset($_POST['BugForm']) && $bugForm->loadData()) {
            //在验证之前，先将文件什么的读进来
            $bugForm->images = UploadedFile::getInstances($bugForm, 'images');
            $bugForm->attachment = UploadedFile::getInstance($bugForm, 'attachment');
            if ($bugForm->validate()) {
                //处理模块信息
                if (!isset($bugForm->moduleId) || trim($bugForm->moduleId) == '')
                    $bugForm->moduleId = MyConstant::OPTION_ALL;

                //处理上传的截图
                $imageNames = ImageUtils::uploadImageOpt($bugForm);
                $bugForm->images = Json::encode($imageNames);

                //处理上传的附件
                if (isset($bugForm->attachment) || trim($bugForm->attachment) != '') {
                    if ($bugForm->attachment !== null) {
                        $attachmentName = time() . $bugForm->attachment->name;
                        $bugForm->attachment->saveAs(MyConstant::ATTACHMENT_PATH . $attachmentName);
                        $bugForm->attachment = $attachmentName;
                    }
                }
                var_dump($bugForm);
                $result = $bugForm->modifyBugOfDb($bug);
                if ($result) {
                    //插入数据库成功
                    Yii::$app->session->setFlash(OPT_RESULT, 'Bug修改成功！');
                    return $this->redirect('index.php?r=bug/index');
                } else {
                    //插入数据库失败
                    Yii::$app->session->setFlash(OPT_RESULT, 'Bug修改失败！');
                }
            }
        } else {
            $bugForm->name = $bug->name;
            $bugForm->projectId = $bug->project_id;
            $bugForm->moduleId = $bug->module_id;
            $bugForm->assignId = $bug->assign_id;
            $bugForm->priority = $bug->priority;
            $bugForm->seriousId = $bug->serious_id;
            $bugForm->reappear = $bug->reappear;
        }
        $projects = Project::find()->select(['id', 'name', 'group_id'])->all();
        $modules = [];
        if (count($projects) > 0) {
            $modules = Module::find()->select(['id', 'name'])->where(['project_id' => $bugForm->projectId])->all();
        }
        return $this->render('edit', ['bugForm' => $bugForm, 'projects' => $projects, 'modules' => $modules]);
    }

    public function actionDelete($bugId)
    {
        $bug = Bug::find()->where(['id' => $bugId])->one();
        if ($bug === null) {
            Yii::$app->session->setFlash(OPT_RESULT, 'Bug信息过期，请刷新重试！');
            return $this->redirect('index.php?r=bug/index');
        } else {
            $result = false;
            try {
                $result = $bug->delete();
            } catch (Exception $e) {
                $result = false;
            }
            if ($result) {
                Yii::$app->session->setFlash(OPT_RESULT, 'Bug删除成功!');
                return $this->redirect('index.php?r=bug/index');
            } else {
                Yii::$app->session->setFlash(OPT_RESULT, 'Bug删除成功失败!');
                if (isset($_SERVER['HTTP_REFERER']))
                    return $this->redirect($_SERVER['HTTP_REFERENCE']);
                else
                    return $this->redirect('index.php?r=bug/index');
            }
        }
    }

    public function actionShow($bugId)
    {
        $bug = Bug::find()->joinWith(['project', 'assign', 'creator'])->where(['bug.id' => $bugId])->one();
        if ($bug === null) {
            Yii::$app->session->setFlash(OPT_RESULT, '指定Bug不存在！');
            return $this->redirect('index.php?r=bug/index');
        }
        return $this->render('show', ['bug' => $bug]);
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

    public function actionResolve($bugId)
    {
        $resolveForm = new ResolveForm();
        $resolveForm->bugId = $bugId;
        if (isset($_POST['ResolveForm']) && $resolveForm->loadData() && $resolveForm->validate()) {
            $result = $resolveForm->modifyBugOfDb();
            if ($result) {
                Yii::$app->session->setFlash(OPT_RESULT, '操作保存成功！');

            } else {
                Yii::$app->session->setFlash(OPT_RESULT, '操作保存失败！');
            }
        }
        if (isset($_SERVER['HTTP_REFERER']))
            return $this->redirect($_SERVER['HTTP_REFERER']); //回到来的地方
        else
            return $this->redirect('index.php?r=bug/index');
    }

    public function actionActive($bugId)
    {
        $activeForm = new ActiveBugForm();
        $activeForm->bugId = $bugId;
        if (isset($_POST['ActiveBugForm']) && $activeForm->loadData() && $activeForm->validate()) {
            $result = $activeForm->modifyBugOfDb();
            if ($result) {
                Yii::$app->session->setFlash(OPT_RESULT, '激活成功！');
            } else {
                Yii::$app->session->setFlash(OPT_RESULT, '激活失败！');
            }
        }
        if (isset($_SERVER['HTTP_REFERER']))
            return $this->redirect($_SERVER['HTTP_REFERER']); //回到来的地方
        else
            return $this->redirect('index.php?r=bug/index');
    }

    public function actionClose($bugId)
    {
        $closeForm = new CloseBugForm();
        $closeForm->bugId = $bugId;
        if (isset($_POST['CloseBugForm']) && $closeForm->loadData() && $closeForm->validate()) {
            $result = $closeForm->modifyBugOfDb();
            if ($result) {
                Yii::$app->session->setFlash(OPT_RESULT, '关闭Bug成功！');
            } else {
                Yii::$app->session->setFlash(OPT_RESULT, '关闭Bug失败！');
            }
        }
        if (isset($_SERVER['HTTP_REFERER']))
            return $this->redirect($_SERVER['HTTP_REFERER']); //回到来的地方
        else
            return $this->redirect('index.php?r=bug/index');
    }

    public function actionGetModuleAndMembers($projectId)
    {
        $result = [];
        $result['modules'] = $this->getModuleInfo($projectId);
        $result['members'] = $this->getGroupMember($projectId);
        return Json::encode($result);
    }

    public function actionGetFzr($projectId, $moduleId)
    {
        if (!isset($moduleId) || trim($moduleId) == '') {
            return Json::encode($this->getGroupMember($projectId));
        } else {
            return Json::encode($this->getModuleFzr($moduleId));
        }
    }

    public function actionCharts($projectId)
    {
        $searchCharts = new SearchChartForm();

        if (isset($_POST['SearchChartForm']) && $searchCharts->loadData() && $searchCharts->validate()) {
//            $data = BugOpt::getProjectRecentDayBugCounts($projectId, $searchCharts->startDate, $searchCharts->endDate);
        } else {
            $now = time();
            $today = date('Y-m-d', $now);
            $defaultDay = date('Y-m-d', strtotime('-7day', $now));
            $searchCharts->startDate = $defaultDay;
            $searchCharts->endDate = $today;
        }

//        $data = BugOpt::getProjectRecentDayBugCounts($projectId, $searchCharts->startDate, $searchCharts->endDate);
        $func = BugOpt::getEchartFunction($searchCharts->type);
        $data = BugOpt::$func($projectId, $searchCharts->startDate, $searchCharts->endDate);

//        var_dump($data);

//        $data = BugOpt::getProjectModuleBugCounts($projectId);
//        var_dump($data);
        return $this->render('charts', ['searchCharts' => $searchCharts, 'data' => $data]);
    }

    public function actionTest()
    {
        $bugs = Bug::find()->andFilterWhere(['between', 'create_time', '2015-02-01', '2015-08-01'])->all();
        var_dump($bugs);
        echo date('Y-m-d H:i:s', time());
        return $this->render('test');
    }

}