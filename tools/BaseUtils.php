<?php
/**
 *
 * Created by GuLang on 2015-05-07.
 */

namespace app\tools;

use Yii;
use app\models\Bug;
use app\models\Project;
use app\models\Module;

class BaseUtils
{
    /**
     * 模板布局中左边的项目和模块的初始内容
     * @return array
     */
    public static function getProjectModuleInfo()
    {
        $projects = Project::find()->select(['id', 'name'])->all();
        $modules = [];
        if (count($projects) > 0) {
            $modules = Module::find()->select(['id', 'name'])->where(['project_id' => $projects[0]->id])->all();
        } else {
            $modules = [];
        }
        return ['projects' => $projects, 'modules' => $modules];
    }

    /**
     * 获得我提交的bug的数目
     * @return int|string
     */
    public static function getMySubmitBugCount()
    {
        return Bug::find()->where(['creator_id' => Yii::$app->user->identity->getId()])->count();
    }

    /**
     * 获得指派给我的bug的数目
     * @return int|string
     */
    public static function getAssignToMeBugCount()
    {
        return Bug::find()->where(['assign_id' => Yii::$app->user->identity->getId()])->count();
    }

    /**
     * 获得我操作过的bug数目
     * @return int|string
     */
    public static function getMyOptBugCount()
    {
//        return Bug::find()->where('introduce regexp "by *' . Yii::$app->user->identity->name . '\""')->count();
        return Bug::find()->andFilterWhere(['like', 'introduce', '"name":"' . Yii::$app->user->identity->name . '"'])->count();
    }


}