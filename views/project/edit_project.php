<?php
/**
 * 添加/修改项目信息界面
 * Created by GuLang on 2015-04-23.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
$isAddProjectView = false;
if (isset($isAdd) && $isAdd) {
    //是添加项目信息界面
    $this->title = '添加项目信息';
    $isAddProjectView = true;
} else {
    //是修改项目信息界面
    $this->title = '修改项目信息';
    $isAddProjectView = false;
}

$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '项目管理', 'url' => 'index.php?r=project/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'edit.css');
?>

<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        <?php
        if ($isAddProjectView)
            echo '添加项目信息';
        else
            echo '修改项目信息';
        ?>
    </div>
    <div class="editInfoForm">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
            ],
        ]); ?>
        <?php echo $form->field($projectForm, 'name')->textInput(); ?>
        <?php echo $form->field($projectForm, 'groupId')->dropDownList(ArrayHelper::map($groups, 'id', 'name')); ?>
        <?php echo $form->field($projectForm, 'introduce', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInputArea">{input}</div></div>',
        ])->textarea(['rows' => 6, 'style' => 'resize: none;']) ?>
        <?php echo Html::submitButton($isAddProjectView ? '添加' : '修改', [
            'class' => 'btn btn-primary',
            'id' => 'submitBtn'
        ]) ?>
        <? ActiveForm::end(); ?>
    </div>
</div>


