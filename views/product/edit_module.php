<?php
/**
 * 添加/修改产品模块界面
 * Created by GuLang on 2015-04-25.
 */

/* @var $this \yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;


$isAddModuleView = false;
if (isset($isAdd) && $isAdd) {
    //是添加产品信息界面
    $this->title = '添加产品模块信息';
    $isAddModuleView = true;
} else {
    //是修改产品信息界面
    $this->title = '修改产品模块信息';
    $isAddModuleView = false;
}
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '产品管理', 'url' => 'index.php?r=product/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'edit.css');
$this->registerJsFile(JS_PATH . 'edit_module.js', [
    'depends' => [\app\assets\AppAsset::className()]
]);
?>

<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        <?php
        if ($isAddModuleView)
            echo '添加产品模块信息';
        else
            echo '修改产品模块信息';
        ?>
    </div>
    <div class="editInfoForm">
        <?php if ($isAddModuleView && count($products) <= 0): ?>
        不好意思，暂时还没有任何产品信息，请先<a href="index.php?r=product/add-product">添加产品信息！
            <?php elseif (!$isAddModuleView && count($modules) <= 0): ?>
                不好意思，暂时还没有产品
                <span style="margin: 0 5px;font-weight: bold;color: #FF0000;">
                “<?php echo $moduleForm->productName; ?>”
            </span>
                的任何模块信息，请先<a href="index.php?r=product/add-module">添加模块</a>信息！
            <?php else: ?>
                <?php $form = ActiveForm::begin(['fieldConfig' => ['template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',],]); ?>
                <?php if (!$isAddModuleView): ?>
                    <?php echo $form->field($moduleForm, 'productName')->textInput(['readonly' => 'true']); ?>
                    <?php echo $form->field($moduleForm, 'id')->dropDownList(ArrayHelper::map($modules, 'id', 'name')); ?>
                    <div
                        style="background-color: #717171;width:100%;height: 1px;margin-top: -5px;margin-bottom: 10px;"></div>
                <?php else: ?>
                    <?php echo $form->field($moduleForm, 'productId')->dropDownList(ArrayHelper::map($products, 'id', 'name')); ?>
                <?php endif; ?>

                <?php echo $form->field($moduleForm, 'name')->textInput(); ?>
                <?php echo $form->field($moduleForm, 'fuzeren')->dropDownList(ArrayHelper::map($groupMembers, 'user_id', 'user.name'), ['multiple' => 'multiple']) ?>
                <?php echo $form->field($moduleForm, 'introduce', ['template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInputArea">{input}</div></div>',])
                    ->textarea(['rows' => 6, 'style' => 'resize: none;']) ?>

                <?php echo Html::submitButton($isAddModuleView ? '添加' : '修改', ['class' => 'btn btn-primary', 'id' => 'submitBtn',]) ?>

                <?php ActiveForm::end(); ?>
            <?php endif; ?>
    </div>
</div>



