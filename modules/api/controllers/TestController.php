<?php
/**
 * SiteController的测试控制器类
 */
namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    public function actionCharts()
    {
        return $this->renderPartial('charts');
    }

}
