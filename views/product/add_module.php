<?php
/**
 * 添加产品模块界面
 * Created by GuLang on 2015-04-25.
 */

/* @var $this \yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = '添加产品模块';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '产品管理', 'url' => 'index.php?r=product/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'addProductOrModule.css');

if (isset($this->params[OPT_RESULT]) && $this->params[OPT_RESULT]) {
    /* 传递过来的错误信息 */
    $this->registerJs('window.onload=function(){alert("添加产品失败");}');
    unset($this->params[OPT_RESULT]);
}

$changedProductJs = <<<JS
$('#moduleform-productid').change(function(data){
    var productId=$('#moduleform-productid').find('option:selected').val();
    //alert('改变选项'+productId);
    $.get('index.php?r=product/getGroupMember',{productId:productId},function(data){
        var members=jQuery.parseJSON(data);
        $('#moduleform-fuzeren').empty();
        $.each(members,function(idx,member){
            $('#moduleform-fuzeren').append('<option value="'+member.userId+'">'+member.userName+'</option>');
        });
    });
});
JS;
$this->registerJs($changedProductJs);

?>

<div class="addProductOrModuleInfo">
    <div class="addInfoTitle">
        <div class="addInfoTitleIcon"></div>
        添加产品模块
    </div>
    <div class="addInfoForm">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
            ],
        ]); ?>
        <?php echo $form->field($moduleForm, 'productId')->dropDownList(ArrayHelper::map($products, 'id', 'name')); ?>
        <?php echo $form->field($moduleForm, 'name')->textInput(); ?>
        <?php echo $form->field($moduleForm, 'fuzeren')->dropDownList(ArrayHelper::map($groupMembers, 'user_id', 'user.name'), ['multiple' => 'multiple']) ?>
        <?php echo $form->field($moduleForm, 'introduce', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInputArea">{input}</div></div>',
        ])->textarea(['rows' => 6]) ?>

        <?php echo Html::submitButton('添加模块', [
            'class' => 'btn btn-primary submitBtn',
        ]) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

