<?php
/**
 * 重置密码界面弹窗
 * Created by GuLang on 2015-05-27.
 */
/**@var $this \yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<div style="text-align: center;">
    <p style="width: 100%;text-align: left;font-weight: bold;font-size: 15px;">请注意：</p>

    <p style="width: 100%;text-align: left;text-indent: 2em;font-size: 13px;">
        当您点击重置按钮后，所指定的用户绑定的邮箱会收到系统发送的重置密码的邮件，
        请在24小时内进入邮箱点击邮件内的链接进行密码重置，否则此次重置无效！
    </p>
    <?php $form = ActiveForm::begin([
        'fieldConfig' => ['template' => '{input}{error}'],
        'action' => 'index.php?r=site/reset-password'
    ]); ?>
    <?php echo $form->field($resetPasswordForm, 'userName')->textInput(['style' => 'margin-top:-20px;', 'placeholder' => "重置用户名"]); ?>
    <div style="text-align: center;">
        <?php echo Html::submitButton('重置', ['id' => 'submitResetBtn', 'class' => 'btn btn-primary', 'style' => '']); ?>
        <?php echo Html::button('取消', ['id' => 'cancelResetBtn', 'class' => 'btn btn-danger', 'style' => 'margin-left:10px']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#cancelResetBtn').click(function () {
            $('#resetPasswordModal').modal('toggle');
        });
    });
</script>

