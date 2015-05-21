<?php
/**
 *
 * Created by GuLang on 2015-05-14.
 */

namespace app\modules\api\controllers;


use app\models\Module;
use app\models\Project;
use app\modules\api\models\HttpResult;
use app\tools\MyConstant;
use yii\data\Pagination;


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
}