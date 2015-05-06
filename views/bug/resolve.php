<?php
/**
 * Bug解决模态框
 * Created by GuLang on 2015-05-06.
 */
/* @var $this \yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div style="text-align: center;">
    <?php
    $form = ActiveForm::begin([
        'action' => ['bug/resolve', 'bugId' => $bug->id],
    ]);
    echo $form->field($resolveForm, 'type', [
        'template' => '<div style="display: block;overflow: hidden;">
                            <div style="float: left;margin-top: 6px;margin-right: 10px;">{label}</div>
                            <div style="float: left">{input}</div>
                            <div style="float: left">{error}</div>
                        </div>',
    ])->radioList(['0' => '解决', '1' => '其它'], [
        'style' => 'padding-top:7px;',
        'item' => function ($index, $label, $name, $checked, $value) {
            return '<span style="margin-right: 15px;">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</span>';
        }
    ]);
    echo $form->field($resolveForm, 'introduce', [
        'template' => '<div style="display: block;overflow: hidden;">
                            <div style="float: left;margin-top: 6px;margin-right: 10px;">{label}</div>
                            {input}{error}
                        </div>',
    ])->textarea(['rows' => 5, 'style' => 'width:100%;resize:none;']);
    echo Html::submitButton('解决', ['class' => 'btn btn-primary', 'id' => 'submitModalBtn']);
    echo Html::button('取消', ['class' => 'btn btn-danger', 'id' => 'cancelModalBtn', 'style' => 'margin-left:50px;']);
    ActiveForm::end();
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
//        $('#submitModalBtn').click(function () {
//            $('#resolveBugModal').modal('hide');
//        });
        $('#cancelModalBtn').click(function () {
            $('#resolveBugModal').modal('hide');
        });
    });
</script>
