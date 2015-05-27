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
<div style="text-align: center;margin-top: -40px;">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => ['template' => '{input}{error}'],
        'action' => 'index.php?r=site/reset-password'
    ]); ?>
    <?php echo $form->field($resetPasswordForm, 'userName')->textInput(['style' => '', 'placeholder' => "重置用户名"]); ?>
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

