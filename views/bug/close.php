<?php
/**
 * Bug关闭模态框
 * Created by GuLang on 2015-05-06.
 */
/* @var $this \yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div style="text-align: center;">
    <?php
    $form = ActiveForm::begin([
        'action' => ['bug/close', 'bugId' => $bug->id],
    ]);
    echo $form->field($closeForm, 'reason', [
        'template' => '<div style="display: block;overflow: hidden;">
                            <div style="float: left;margin-top: 6px;margin-right: 10px;">{label}</div>
                            {input}{error}
                        </div>',
    ])->textarea(['rows' => 5, 'style' => 'width:100%;resize:none;']);
    echo Html::submitButton('关闭', ['class' => 'btn btn-primary']);
    echo Html::button('取消', ['class' => 'btn btn-danger', 'id' => 'cancelCloseModalBtn', 'style' => 'margin-left:50px;']);
    ActiveForm::end();
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#cancelCloseModalBtn').click(function () {
            $('#closeBugModal').modal('hide');
        });
    });
</script>
