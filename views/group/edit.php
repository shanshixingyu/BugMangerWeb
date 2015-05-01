<?php
/**
 * 编辑团队界面
 * Created by GuLang on 2015-04-30.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */

$isAddGroupView = true;
//if (isset($isAdd) && $isAdd) {
//    $isAddGroupView = true;
//    $this->title = '添加团队';
//} else {
//    $isAddGroupView = false;
//    $this->title = '修改团队';
//}

$this->title = '添加团队';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    ['label' => '团队管理', 'url' => 'index.php?r=group/index'],
    $this->title,
];
$this->registerJsFile(ASSETS_PATH . '10b978a4/jquery.js', ['position' => View::POS_HEAD]);
$this->registerCssFile(CSS_PATH . 'edit.css');

?>
<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        <?php
        if ($isAddGroupView) {
            echo '添加团队';
        } else {
            echo '修改团队信息';
        }
        ?>

    </div>
    <div class="editInfoForm">
        <?php $form = ActiveForm::begin();
        /* 团队名称 */
        echo $form->field($groupEditForm, 'name', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
        ])->textInput();
        /* 团队成员 */
        echo $form->field($groupEditForm, 'member', [
            'template' =>
                '<div class="infoRow">
                    <div class="infoLabel">{label}</div>
                    <div style="float: left;width: 200px;margin-left: 10px;">'
                . Html::dropDownList('memberList', null, ArrayHelper::map($allUser, 'id', 'name'), [
                    'multiple' => true, 'id' => 'leftMember', 'class' => 'form-control', 'size' => 6])
                . ' </div>
                    <div style="margin:15px 5px 15px 15px;float: left;">
                        <input id="addMember" type="button" value="&gt;&gt;" class="btn">
                        <br/><br/>
                        <input id="deleteMember" type="button" value="&lt;&lt;" class="btn">
                    </div>
                    <div class="infoInput" style="width:200px;">{input}</div>
                    <div class="infoError">{error}</div>
                </div>',
        ])->dropDownList(ArrayHelper::map($selectedMember, 'id', 'name'), [
            'multiple' => true, 'class' => 'form-control', 'size' => 6, 'id' => 'rightMember',
        ]);
        /* 团队简介 */
        echo $form->field($groupEditForm, 'introduce', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInputArea">{input}</div></div>',
        ])->textarea(['rows' => 6, 'style' => 'resize:none;width:480px;',]);
        /* 提交按钮 */
        echo Html::submitButton($isAddGroupView ? '添加' : '修改', ['class' => 'btn btn-primary', 'id' => 'submitBtn']);

        ActiveForm::end(); ?>

    </div>
</div>

<script type="text/javascript">
    $('document').ready(function () {
        $('#addMember').click(function () {
            addMember($('#leftMember option:selected').clone());
        });
        $('#deleteMember').click(function () {
            $('#rightMember option:selected').remove();
        });
//        $('#submitBtn').
        $('form').submit(function () {
            $('#rightMember option').attr('selected', true);
        });
    });
    /**
     * 注意避免重复
     * @param selectedArr
     */
    function addMember(selectedArr) {
        var $selectedUserStr = ';';
        var $existedUserArr = $('#rightMember option');
        $existedUserArr.each(function () {
            $selectedUserStr += $(this).val() + ';';
        })
        selectedArr.each(function () {
            if ($selectedUserStr.indexOf(';' + $(this).val() + ';') < 0) {
                $selectedUserStr += $(this).val() + ';';
                $(this).appendTo('#rightMember');
            }
        })
    }

</script>
