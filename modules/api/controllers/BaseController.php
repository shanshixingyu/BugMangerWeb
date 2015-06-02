<?php
/**
 *
 * Created by GuLang on 2015-05-12.
 */

namespace app\modules\api\controllers;


use yii\web\Controller;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        date_default_timezone_set('Asia/Shanghai');
        parent::init();
    }
}