<?php
/**
 * Bug控制器
 * Created by GuLang on 2015-05-02.
 */

namespace app\controllers;

use app\models\ResolveForm;
use Yii;
use app\models\Bug;
use app\models\BugForm;
use app\models\Module;
use app\models\Project;
use app\models\SearchBugForm;
use app\tools\ImageUtils;
use app\tools\MyConstant;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\UploadedFile;

class BugController extends BaseController
{
    private static $searchBugPost;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'bug', 'add', 'show'],
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
        if (isset(self::$searchBugPost)) {
            var_dump($this->searchBugPost);
        }

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

        $query = Bug::find()->joinWith(['assign'])->where($where);
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
            //处理模块信息
            if (!isset($bugForm->moduleId) || trim($bugForm->moduleId) == '')
                $bugForm->moduleId = MyConstant::OPTION_ALL;

            //处理上传的截图
            $imageNames = ImageUtils::uploadImageOpt($bugForm);
            $bugForm->images = Json::encode($imageNames);

            //处理上传的附件
            if (isset($bugForm->attachment) || trim($bugForm->attachment) != '') {
                $bugForm->attachment = UploadedFile::getInstance($bugForm, 'attachment');
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
        $projects = Project::find()->select(['id', 'name', 'group_id'])->all();
        $modules = [];
        if (count($projects) > 0) {
            $modules = Module::find()->select(['id', 'name'])->all();
        }

        return $this->render('add', ['bugForm' => $bugForm, 'projects' => $projects, 'modules' => $modules]);
    }

    public function actionShow($bugId)
    {
        $bug = Bug::find()->joinWith(['project', 'assign', 'creator'])->where(['bug.id' => $bugId])->one();

        return $this->render('show', ['bug' => $bug]);
    }

    public function actionDownload($fileName)
    {
        echo 'ssd s ' . $fileName;
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
        if (isset($_POST['ResolveForm']) && $resolveForm->loadData()) {
            $result = $resolveForm->modifyBugOfDb();
            if ($result) {
                Yii::$app->session->setFlash(OPT_RESULT, '解决操作保存成功！');
            } else {
                Yii::$app->session->setFlash(OPT_RESULT, '解决操作保存失败！');
            }
        }

        return $this->redirect($_SERVER['HTTP_REFERER']); //回到来的地方
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

    public function actionTest()
    {
//        return $this->renderPartial('resolve');
        var_dump($_SERVER);
//        var_dump(Yii::$app->request);
    }

    public function actionTest1()
    {
        return $this->renderPartial('test');
    }
}