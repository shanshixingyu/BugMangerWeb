<?php
/**
 * 用户信息编辑界面
 * Created by GuLang on 2015-04-29.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
$isAddUserView = true;
if (isset($isAdd) && $isAdd) {
    $isAddUserView = true;
    $this->title = '添加用户';
} else {
    $isAddUserView = false;
    $this->title = '修改用户';
}

$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '用户管理', 'url' => 'index.php?r=user/index'],
    $this->title,
];

if (Yii::$app->session->hasFlash(OPT_RESULT)) {
    $this->registerJs('alert("' . Yii::$app->session->getFlash(OPT_RESULT) . '");');
    Yii::$app->session->removeFlash(OPT_RESULT);
}

$this->registerCssFile(CSS_PATH . 'edit.css');
?>
<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        <?php
        if ($isAddUserView) {
            echo '添加用户';
        } else {
            echo '修改用户信息';
        }
        ?>

    </div>
    <div class="editInfoForm">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
            ],
        ]);
        echo $form->field($userForm, 'name')->textInput();
        echo $form->field($userForm, 'roleId')->dropDownList(ArrayHelper::map($roles, 'id', 'name'));
        echo $form->field($userForm, 'email')->textInput();

        echo Html::submitButton($isAddUserView ? '添加' : '修改', ['class' => 'btn btn-primary', 'id' => 'submitBtn']);

        ActiveForm::end(); ?>

    </div>
</div>