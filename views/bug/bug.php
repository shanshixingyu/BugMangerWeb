<?php
/**
 * 项目bug详情页面
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
$this->title = '项目缺陷列表';
$this->params['breadcrumbs'] = [
    ['label' => '项目缺陷概况', 'url' => 'index.php?r=bug/index'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'bug.css');
$bugStatus = Json::decode(BUG_STATUS);
$bugPriority = Json::decode(BUG_PRIORITY);
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
    echo $form->field($searchBugForm, 'projectId')->dropDownList([$project->id => $project->name], ['readonly' => true]);
    echo $form->field($searchBugForm, 'moduleId')->dropDownList(ArrayHelper::map($project->getModules(), 'id', 'name'), ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'priority')->dropDownList($bugPriority, ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'seriousId')->dropDownList($bugSerious, ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'assignId')->dropDownList(ArrayHelper::map($project->getGroupMember(), 'id', 'name'), ['prompt' => '全部']);
    echo $form->field($searchBugForm, 'creatorId')->textInput();
    echo $form->field($searchBugForm, 'statusId')->dropDownList($bugStatus, ['prompt' => '全部']);
    //    echo $form->field($searchBugForm, 'submitStart')->textInput(['style' => 'width']);
    //    echo $form->field($searchBugForm, 'submitEnd')->textInput();
    echo $form->field($searchBugForm, 'keyword')->textInput();

    echo Html::submitButton('查询', ['class' => 'btn btn-primary', 'id' => 'submitBtn']);

    ActiveForm::end();
    ?>
</div>
<a href="index.php?r=bug/charts&projectId=<?php echo $project->id; ?>" id="createCharts">统计报表</a>
<div id="bugTable">
    <table>
        <thead>
        <tr>
            <th style="width:40%;">缺陷名称</th>
            <th style="width:8%;">优先级</th>
            <th style="width:11%;">当前状态</th>
            <th style="width:11%;">影响程度</th>
            <th style="width:8%;">指派给</th>
            <th style="width:8%;">提交者</th>
            <th style="width:17%;">提交时间</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($bugs) && count($bugs) > 0): ?>
            <?php foreach ($bugs as $bug): ?>
                <tr>
                    <td><a href="index.php?r=bug/show&bugId=<?php echo $bug->id; ?>"><?php echo $bug->name; ?></a></td>
                    <td>
                        <?php
                        switch ($bug->priority) {
                            case array_search(BUG_PRIORITY_LOW, $bugPriority):
                                echo '<span style="color:#0fff0a;">';
                                break;
                            case array_search(BUG_PRIORITY_MIDDLE, $bugPriority);
                                echo '<span style="color:#07e1ff;">';
                                break;
                            case array_search(BUG_PRIORITY_HIGH, $bugPriority):
                                echo '<span style="color:#ff18e7;">';
                                break;
                            case array_search(BUG_PRIORITY_URGENT, $bugPriority);
                                echo '<span style="color:#ff0000;">';
                                break;
                            default:
                                echo '<span>';
                        }
                        echo $bugPriority[$bug->priority];
                        echo '</span>';
                        ?>
                    </td>
                    <td style="color: #ffffff;">
                        <?php
                        switch ($bug->status) {
                            case array_search(BUG_STATUS_CLOSED, $bugStatus):
                                echo '<span style="background-color:#0fff0a;padding:3px 5px;">';
                                break;
                            case array_search(BUG_STATUS_UNSOLVED, $bugStatus);
                                echo '<span style="background-color:#ff0203;padding:3px 5px;">';
                                break;
                            case array_search(BUG_STATUS_SOLVED, $bugStatus):
                                echo '<span style="background-color:#ff18e7;padding:3px 5px;">';
                                break;
                            case array_search(BUG_STATUS_ACTIVE, $bugStatus);
                                echo '<span style="background-color:#ff0000;padding:3px 5px;">';
                                break;
                            case array_search(BUG_STATUS_OTHER, $bugStatus);
                                echo '<span style="background-color:#03f8ff;padding:3px 5px;">';
                                break;
                            default:
                                echo '<span>';
                        }
                        echo $bugStatus[$bug->status];
                        echo '</span>';
                        ?>
                    </td>
                    <td><?php echo $bugSerious[$bug->serious_id]; ?></td>
                    <td><?php echo $bug->assign->name; ?></td>
                    <td><?php echo $bug->creator->name; ?></td>
                    <td><?php echo $bug->create_time; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">sorry，该项目暂时没有满足条件的Bug信息!</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div style="width: 100%;margin-top: 10px;">
        <?php echo \yii\widgets\LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination', 'style' => 'margin:0;'],
        ]); ?>
        <div style="float: right;clear: right;font-size: 14px;padding: 5px;font-weight: bold;">
            当前页&nbsp;
            <?php
            if ($pagination->totalCount <= 0) {
                echo '0';
            } else {
                echo $pagination->offset + 1;
            }
            ?>
            -
            <?php
            /* 先判断当前页是否为最后一页（page+1==pageCount）,如果是最后一页，当前页的大小为 总数-offset，否则为pageSize */
            if ($pagination->totalCount <= 0) {
                echo '0';
            } else if ($pagination->page + 1 == $pagination->pageCount) {
                echo $pagination->totalCount;
            } else {
                echo $pagination->offset + $pagination->pageSize;
            }
            ?>
            &nbsp;&nbsp;&nbsp;总记录数&nbsp;<?php echo $pagination->totalCount; ?><br/>
        </div>
    </div>
</div>
