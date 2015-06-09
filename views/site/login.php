<?php
/**
 * 登录页面
 * Created by GuLang on 2015-04-16.
 */
/* @var $this \yii\web\View */
/* @var $content string */
use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\captcha\Captcha;
use yii\bootstrap\Modal;
use app\models\ResetPasswordParamForm;
use app\tools\MyConstant;

AppAsset::register($this);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统登录</title>
    <link href="<?php echo ASSETS_PATH; ?>61ba6892/css/bootstrap.css" rel="stylesheet"/>
    <link href="<?php echo CSS_PATH; ?>login.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo ASSETS_PATH; ?>10b978a4/jquery.js" type="text/javascript"></script>
    <script src="<?php echo ASSETS_PATH; ?>ceb33bbe/js/bootstrap.js"></script>
    <!--    <script src="--><?php //echo ASSETS_PATH; ?><!--9768bb07/yii.js"></script>-->
    <!--    <script src="--><?php //echo ASSETS_PATH; ?><!--9768bb07/yii.validation.js"></script>-->
    <!--    <script src="--><?php //echo ASSETS_PATH; ?><!--9768bb07/yii.activeForm.js"></script>-->
</head>
<body>
<div id="wrap">
    <div id="content">
        <div id="login">
            <h3><span style="font-size: 25px;margin-left: 5px;">登录</span>&nbsp;&nbsp;LOGIN</h3>
            <hr style="width:305px; margin-left: -5px;"/>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class=\"login_row\"><div class=\"login_title\">{label}</div>{input}<div class=\"login_error\">{error}</div></div>",
                ]
            ]); ?>
            <?php echo $form->field($loginForm, 'username')->textInput(['class' => 'login_input_normal']); ?>
            <?php echo $form->field($loginForm, 'password')->passwordInput(['class' => 'login_input_normal']); ?>
            <!--            --><?php //echo $form->field($loginForm, 'verifyCode')->textInput(['class' => 'login_input_verify']); ?>
            <?php echo $form->field($loginForm, 'verifyCode')->widget(Captcha::className(), [
                'template' => "{input}{image} ",
                'imageOptions' => ['alt' => '验证码图片', 'class' => 'verifyCodeImage'],
                'captchaAction' => 'site/captcha',
                'options' => ['class' => 'verifyCodeInput', 'maxlength' => 4],
            ]) ?>
            <div style="margin-left: 70px;margin-top:-10px;">
                <?php echo $form->field($loginForm, 'rememberMe')->checkbox(); ?>
            </div>
            <div style="margin-top: 25px;margin-left: 95px;">
                <!--                <input type="submit" id="login_button" value="登录">&nbsp;&nbsp;&nbsp;-->
                <?= Html::submitButton('登录', ['class' => 'btn btn-primary']) ?>
                &nbsp;&nbsp;&nbsp;
                <a id="forgetPassword" data-toggle="modal" href="#resetPasswordModal"
                   style="font-size: 15px;font-weight:bold;color: #0608ff;margin-left: 10px">找回密码？</a>
            </div>
            <?php $form = ActiveForm::end(); ?>
        </div>

        <div id="left_content">
            <img src="<?php echo IMG_PATH; ?>ic_title.png" alt="标题图片">
        </div>
    </div>
    <div id="foot">
        <p style="font-size: 15px;">版权所有&nbsp;&nbsp;2014-2015&nbsp;&nbsp;孤狼软件&nbsp;&nbsp;版权所有&nbsp;&nbsp;盗版必究</p>

        <p style="font-size: 13px;margin-top: -10px;">Copyright&nbsp;©&nbsp;GuLangSoftware,All rights reserved.</p>
    </div>
</div>
<?php Modal::begin([
    'id' => 'resetPasswordModal',
    'header' => '<div style="text-align: center;font-size:20px;font-weight: bold">密码重置</div>',
    'size' => Modal::SIZE_SMALL,
]);
$resetPasswordForm = new ResetPasswordParamForm();
echo $this->render('reset_password', ['resetPasswordForm' => $resetPasswordForm]);
Modal::end(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if (Yii::$app->session->hasFlash(OPT_RESULT)):?>
        alert('<?php echo Yii::$app->session->getFlash(OPT_RESULT);?>');
        <?php Yii::$app ->session ->removeFlash(OPT_RESULT);?>
        <?php endif;?>

        <?php if (Yii::$app->session->hasFlash(MyConstant::PASSWORD_OPT_RESULT)):?>
        alert('<?php echo Yii::$app->session->getFlash(MyConstant::PASSWORD_OPT_RESULT);?>');
        <?php Yii::$app ->session ->removeFlash(MyConstant::PASSWORD_OPT_RESULT);?>

        <?php if (Yii::$app->session->hasFlash(MyConstant::RESET_PASSWORD_SUCCESS)){
            Yii::$app ->session ->removeFlash(MyConstant::RESET_PASSWORD_SUCCESS);
        }else{
            echo '$("#resetPasswordModal").modal("toggle")';
        }?>
        <?php endif;?>
    });
</script>
</body>
</html>