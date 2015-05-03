<?php
/**
 * bug详情页面
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $this yii\web\View */
$this->title = 'bug列表';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(CSS_PATH . 'bug.css');
$bugStatus = Json::decode(BUG_STATUS);
$bugSerious = Json::decode(BUG_SERIOUS);
?>

<div id="bugQueryCondition">
    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' =>
                '<div class="searchItem">
                    <div class="searchLabel">{label}</div>
                    <div class="searchInput">{input}</div>
                </div>',
            'inputOptions' => ['class' => 'searchInputOption form-control'],
        ]
    ]);
    echo $form->field($searchBugForm, 'productId')->dropDownList([], ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'moduleId')->dropDownList([], ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'seriousId')->dropDownList($bugSerious, ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'assignId')->dropDownList([], ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'creatorId')->textInput();
    echo $form->field($searchBugForm, 'statusId')->dropDownList($bugStatus, ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'submitStart')->textInput(['style' => 'width']);
    echo $form->field($searchBugForm, 'submitEnd')->textInput();
    echo $form->field($searchBugForm, 'keyword')->textInput();

    echo Html::submitButton('查询', ['class' => 'btn btn-primary', 'id' => 'submitBtn']);

    ActiveForm::end();
    ?>
</div>
<div id="bugTable">
    <table>
        <tr>
            <th style="width:325px;">Bug名称</th>
            <th style="width:100px;">产品名称</th>
            <th style="width:100px;">当前状态</th>
            <th style="width:75px;">严重程度</th>
            <th style="width:50px;">指派给</th>
            <th style="width:50px;">提交者</th>
            <th style="width:50px;">提交时间</th>
        </tr>
        <tr>
            <td><a href="#">手动bug</a></td>
            <td>孤狼软件</td>
            <td>解决未审核</td>
            <td>非常严重</td>
            <td>hwz</td>
            <td>孤狼</td>
            <td>2014-12-05</td>
        </tr>
    </table>
</div>
