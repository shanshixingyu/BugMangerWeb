<?php
/**
 * 登录页面
 * Created by GuLang on 2015-04-16.
 */
use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统登录</title>
    <link href="/yii-basic-app-2.0.3/web/assets/61ba6892/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo CSS_PATH; ?>login.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="wrap">
    <div id="content">
        <div id="login">
            <h3><span style="font-size: 25px;margin-left: 5px;">登录</span>&nbsp;&nbsp;LOGIN</h3>
            <hr style="width:325px; margin-left: -5px;"/>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class=\"login_row\"><div class=\"login_title\">{label}</div>{input}<div>{error}</div></div>",
                ]
            ]); ?>
            <?php echo $form->field($loginForm, 'username')->textInput(['class' => 'login_input_normal']); ?>
            <?php echo $form->field($loginForm, 'password')->passwordInput(['class' => 'login_input_normal']); ?>
            <?php echo $form->field($loginForm, 'verifyCode')->textInput(['class' => 'login_input_verify']); ?>
            <?php echo $form->field($loginForm, 'rememberMe')->checkbox(); ?>

            <!--            --><?php //echo $form->field($loginForm, 'username')->textInput(['class' => 'form-control']); ?>
            <!--            --><?php //echo $form->field($loginForm, 'password')->passwordInput(['class' => 'form-control']); ?>
            <!--            --><?php //echo $form->field($loginForm, 'verifyCode')->textInput(['class' => 'form-control']); ?>
            <!--            --><?php //echo $form->field($loginForm, 'rememberMe')->checkbox(['margin-left' => '20px']); ?>
            <div style="margin-top: 15px;margin-left: 95px;">
                <input type="submit" id="login_button" value="登录">&nbsp;&nbsp;&nbsp;
                <a href="#" style="font-size: 14px;">找回密码？</a>
            </div>
            <?php $form = ActiveForm::end(); ?>
        </div>

        <div id="left_content">
            <img src="<?php echo IMG_PATH; ?>ic_title.png" alt="标题图片">
        </div>

        <div style="background-color: #ffffff;padding: 20px">

            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <?= $form->field($loginForm, 'username')->textInput(['id' => 'ddd']) ?>
            <?= $form->field($loginForm, 'password')->textArea(['rows' => 6]) ?>
            <?= $form->field($loginForm, 'verifyCode')->widget(Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ]) ?>
            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

    </div>
    <div id="foot">
        <p style="font-size: 15px;">版权所有&nbsp;&nbsp;2014-2015&nbsp;&nbsp;孤狼软件&nbsp;&nbsp;版权所有&nbsp;&nbsp;盗版必究</p>

        <p style="font-size: 13px;margin-top: -10px;">Copyright&nbsp;©&nbsp;GuLangSoftware,All rights reserved.</p>
    </div>
</div>
</body>
</html>