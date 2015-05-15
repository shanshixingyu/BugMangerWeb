<?php
/**
 *
 * Created by GuLang on 2015-05-12.
 */

namespace app\modules\api\controllers;

use app\models\Project;
use app\modules\api\models\HttpResult;
use app\tools\MyConstant;
use yii\data\Pagination;

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
}