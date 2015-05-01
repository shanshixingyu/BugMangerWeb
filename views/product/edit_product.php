<?php
/**
 * 添加/修改产品信息界面
 * Created by GuLang on 2015-04-23.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
$isAddProductView = false;
if (isset($isAdd) && $isAdd) {
    //是添加产品信息界面
    $this->title = '添加产品信息';
    $isAddProductView = true;
} else {
    //是修改产品信息界面
    $this->title = '修改产品信息';
    $isAddProductView = false;
}

$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '产品管理', 'url' => 'index.php?r=product/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'edit.css');

?>

<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        <?php
        if ($isAddProductView)
            echo '添加产品信息';
        else
            echo '修改产品信息';
        ?>
    </div>
    <div class="editInfoForm">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
            ],
        ]); ?>
        <?php echo $form->field($productForm, 'name')->textInput(); ?>
        <?php echo $form->field($productForm, 'groupId')->dropDownList(ArrayHelper::map($groupDetails, 'id', 'name')); ?>
        <?php echo $form->field($productForm, 'introduce', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInputArea">{input}</div></div>',
        ])->textarea(['rows' => 6, 'style' => 'resize: none;']) ?>
        <?php echo Html::submitButton($isAddProductView ? '添加' : '修改', [
            'class' => 'btn btn-primary',
            'id' => 'submitBtn'
        ]) ?>
        <? ActiveForm::end(); ?>
    </div>
</div>


