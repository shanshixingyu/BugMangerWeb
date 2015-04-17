<?php
/**
 * 用户登录控制器
 * Created by GuLang on 2015-04-16.
 */
namespace app\controllers;

use Yii;
use app\models\LoginForm;
use yii\web\Controller;

class LoginController extends Controller
{

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'foreColor' => 0x00FF00,
                'backColor' => 0xD0F2FB,
                'width' => 75,
                'height' => 25,
                'padding' => 1,
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 0,
            ],
        ];
    }

    public function actionIndex()
    {
        $loginForm = new LoginForm();
        if (isset($_POST['LoginForm'])) {
            /* 表示按了登录过后过来的 */
            echo "POST<br />";
            var_dump($_POST);
            echo "Session<br />";
            var_dump(Yii::$app->getSession());

            if ($loginForm->load(Yii::$app->request->post("LoginForm")) && $loginForm->login()) {
//                var_dump($loginForm);
                print_r($loginForm);
            }
//            return $this->refresh();
        }
        return $this->renderPartial("index", ['loginForm' => $loginForm]);
    }

    public function actionTest1()
    {
        echo __FILE__, "<br />";
        echo __DIR__, "<br />";
        echo $_SERVER['PHP_SELF'], "<br />";
        echo $_SERVER['DOCUMENT_ROOT'], "<br />";
        echo $_SERVER['HTTP_HOST'], "<br />";

    }


}