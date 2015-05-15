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
use yii\helpers\Json;
use yii\web\Controller;

class ProjectController extends Controller
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
}