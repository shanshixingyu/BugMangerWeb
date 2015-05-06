<?php
/**
 * 用户个人信息管理界面
 * Created by GuLang on 2015-04-19.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = '个人信息管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(CSS_PATH . 'pim.css');

?>
<div id="userInfo" class="infoArea">
    <div class="infoTitle">
        <div class="infoTitleIcon"></div>
        个人资料
    </div>
    <div id="userInfoShow" class="infoShow">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="userInfoRow"><div class="userInfoLabel">{label}</div><div class="userInfoInput">{input}</div><div class="userInfoError">{error}</div></div>',
            ],
        ]); ?>
        <?php $myRowTemplate = '<div class="userInfoMyRow"><div class="userInfoLabel">{label}</div><div class="userInfoInput">{input}</div></div>'; ?>

        <?php echo $form->field($userModifyForm, 'userId', [
            'template' => $myRowTemplate,
        ])->textInput(['size' => 15, 'class' => 'cannotReadableInput', 'readonly' => 'true']); ?>
        <?php echo $form->field($userModifyForm, 'userName', [
            'template' => $myRowTemplate,
        ])->textInput(['size' => 15, 'class' => 'cannotReadableInput', 'readonly' => 'true']); ?>
        <?php echo $form->field($userModifyForm, 'roleName', [
            'template' => $myRowTemplate,
        ])->textInput(['size' => 15, 'class' => 'cannotReadableInput', 'readonly' => 'true']); ?>

        <div style="background-color: #717171;width:350px;height: 1px;margin-top: -5px;margin-bottom: 10px;"></div>
        <?php echo $form->field($userModifyForm, 'oldPassword')->passwordInput(['size' => 15, 'placeHolder' => '不修改时留空']); ?>
        <?php echo $form->field($userModifyForm, 'password')->passwordInput(['size' => 15, 'placeHolder' => '不修改时留空']); ?>
        <?php echo $form->field($userModifyForm, 'password2')->passwordInput(['size' => 15, 'placeHolder' => '不修改时留空']); ?>
        <?php echo $form->field($userModifyForm, 'email')->textInput(['size' => 15]); ?>

        <?php echo Html::submitButton('修改', ['class' => 'btn btn-primary', 'style' => 'margin-left:100px;margin-top:15px;']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div id="userGroup" class="infoArea">
    <div class="infoTitle">
        <div class="infoTitleIcon"></div>
        所在分组
    </div>
    <div id="userGroupShow" class="infoShow">
        <?php if (!isset($groupNames) || !is_array($groupNames) || count($groupNames) <= 0) {
            echo '暂无';
        } else {
            foreach ($groupNames as $groupName) {
                echo '<div class="userGroupItem">', $groupName, '</div>';
            }
        }
        ?>
    </div>
</div>
<div id="userProject" class="infoArea">
    <div class="infoTitle">
        <div class="infoTitleIcon"></div>
        参与项目与模块
    </div>
    <div id="userProjectShow" class="infoShow">
        <?php if (!isset($projectModuleData) || !is_array($projectModuleData) || count($projectModuleData) <= 0) {
            echo '暂无';
        } else {
            foreach ($projectModuleData as $data) {
                if (isset($data) && is_array($data))
                    echo '<div class="userProjectItem">【', $data['project'], '】', $data['module'], '</div>';
            }
        }
        ?>
    </div>
</div>
