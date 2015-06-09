<?php
/**
 * 编辑bug界面
 * Created by GuLang on 2015-05-04.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $bugForm app\models\BugForm */
$isModify = false;
if (isset($bugForm->isModify) && $bugForm->isModify) {
    $this->title = '修改BUG';
    $isModify = true;
} else {
    $this->title = '提交新BUG';
    $isModify = false;
}
$this->params['breadcrumbs'] = [
    ['label' => '项目缺陷概况', 'url' => 'index.php?r=bug/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'edit.css');
?>
<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        <?php echo $isModify ? '修改Bug' : '提交新bug'; ?>
    </div>
    <div class="editInfoForm">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'template' => '<div class="infoRow">
                                    <div class="infoLabel">{label}</div>
                                    <div class="infoInput" style="width: 350px">{input}</div>
                                    <div class="infoError">{error}</div>
                                </div>'
            ]
        ]);
        echo $form->field($bugForm, 'name')->textInput();
        echo $form->field($bugForm, 'projectId')->dropDownList(ArrayHelper::map($projects, 'id', 'name'));
        echo $form->field($bugForm, 'moduleId')->dropDownList(ArrayHelper::map($modules, 'id', 'name'), ['prompt' => '全部']);
        echo $form->field($bugForm, 'assignId')->dropDownList(ArrayHelper::map((count($projects) > 0) ? $projects[0]->getGroupMember(['id','name']) : [], 'id', 'name'));
        echo $form->field($bugForm, 'priority')->radioList(Json::decode(BUG_PRIORITY), [
            'style' => 'padding-top:7px;',
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<span style="margin-right: 15px;">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</span>';
            }
        ]);
        echo $form->field($bugForm, 'seriousId')->dropDownList(Json::decode(BUG_SERIOUS));
        echo $form->field($bugForm, 'images[]')->fileInput(['multiple' => true]);
        echo $form->field($bugForm, 'attachment')->fileInput();
        echo $form->field($bugForm, 'introduce')->textarea(['rows' => 6, 'style' => 'resize:none;width:480px;',]);
        echo $form->field($bugForm, 'reappear')->textarea(['rows' => 6, 'style' => 'resize:none;width:480px;',]);

        echo Html::submitButton($isModify ? '修改' : '提交', ['class' => 'btn btn-primary', 'id' => 'submitBtn']);
        ActiveForm::end(); ?>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#bugform-projectid').change(function () {
            var projectId = $(this).find('option:selected').val();
            $.get('index.php?r=bug/get-module-and-members', {projectId: projectId}, function (data) {
                var result = $.parseJSON(data);
                var moduleSelect = $('#bugform-moduleid');
                moduleSelect.find('option:gt(0)').remove();
                $.each(result.modules, function (idx, item) {
                    moduleSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
                var memberSelect = $('#bugform-assignid');
                memberSelect.empty();
                $.each(result.members, function (idx, item) {
                    memberSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
            });
        });
        $("#bugform-moduleid").change(function () {
            var projectId = $('#bugform-projectid').find('option:selected').val();
            var moduleId = $(this).find('option:selected').val();
            $.get('index.php?r=bug/get-fzr', {projectId: projectId, moduleId: moduleId}, function (data) {
                var result = $.parseJSON(data);
                var memberSelect = $('#bugform-assignid');
                memberSelect.empty();
                $.each(result, function (idx, item) {
                    memberSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
            });

        });
    });
</script>