<?php
/**
 * 添加产品界面
 * Created by GuLang on 2015-04-23.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
$this->title = '添加产品';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '产品管理', 'url' => 'index.php?r=product/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'addProductOrModule.css');

if (isset($this->params[OPT_RESULT])) {
    if (is_string($this->params[OPT_RESULT])) {
        /* 传递过来的错误信息 */
        $message = '';
        if ($this->params[OPT_RESULT] == PRODUCT_HAS_EXIST_ERROR)
            $message = '已经存在相同名称的产品信息';
        else
            $message = '添加产品失败';
        $this->registerJs('window.onload=function(){alert("' . $message . '!");}');
    } else if ($this->params[OPT_RESULT]) {
        $this->registerJs('window.onload=function(){alert("添加产品成功!");}');
        unset($this->params[JS_AFFECT_ROW]);
    }
}
?>

<div class="addProductOrModuleInfo">
    <div class="addInfoTitle">
        <div class="addInfoTitleIcon"></div>
        添加产品
    </div>
    <div class="addInfoForm">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
            ],
        ]); ?>
        <?php echo $form->field($productForm, 'name')->textInput(); ?>
        <?php echo $form->field($productForm, 'groupId')->dropDownList(ArrayHelper::map($groupDetails, 'id', 'name')); ?>
        <?php echo $form->field($productForm, 'introduce', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInputArea">{input}</div></div>',
        ])->textarea(['rows' => 6]) ?>
        <?php echo Html::submitButton('添加产品', [
            'class' => 'btn btn-primary submitBtn',
        ]) ?>
        <? ActiveForm::end(); ?>
    </div>
</div>


