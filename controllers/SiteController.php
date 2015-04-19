<?php
/**
 * 用户登录控制器
 * Created by GuLang on 2015-04-16.
 */
namespace app\controllers;

use Yii;
use app\models\LoginForm;
use yii\web\Controller;

class SiteController extends Controller
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

    public function actionLogin()
    {
        $loginForm = new LoginForm();
        if (isset($_POST['LoginForm'])) {
            /* 表示按了登录过后过来的 */
            if ($loginForm->load(Yii::$app->request->post("LoginForm")) && $loginForm->login()) {
                $this->redirect("./index.php?r=site/bug");
            }
        }
        $loginForm->verifyCode = "";
        return $this->renderPartial("login", ['loginForm' => $loginForm]);
    }

    public function actionBug()
    {
        $productDataList = ['孤狼软件', '毕设'];
        return $this->render('bug', ["productDataList" => $productDataList]);
    }

    public function actionPim()
    {
        return $this->render("pim");
    }


    public function actionTest1()
    {

    }


}