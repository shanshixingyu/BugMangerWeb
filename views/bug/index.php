<?php
/**
 * bug概况页面
 */
/* @var $this yii\web\View */
/* @var $this yii\web\View */
$this->title = '项目缺陷概况';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(CSS_PATH . 'bug_overview.css');
?>

<div id="projectBugTable">
    <div id="aboveOfTable">
        <div id="pageTitle">项目缺陷概况表</div>
        <a href="index.php?r=bug/add" class="optBtn">提交新BUG</a>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th style="width:40%;">项目名称</th>
            <th style="width:12%;">Bug总数</th>
            <th style="width:12%;">未解决Bug数</th>
            <th style="width:12%;">当前激活Bug数</th>
            <th style="width:12%;">已解决Bug数</th>
            <th style="width:12%;">关闭Bug数</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($projects) && count($projects) > 0): ?>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <a href="index.php?r=bug/bug&projectId=<?php echo $project->id; ?>"><?php echo $project->name; ?></a>
                    </td>
                    <td><?php echo $project->getBugCount(); ?></td>
                    <td><?php echo $project->getStatusBugCount(BUG_STATUS_UNSOLVED); ?></td>
                    <td><?php echo $project->getStatusBugCount(BUG_STATUS_ACTIVE); ?></td>
                    <td><?php echo $project->getStatusBugCount(BUG_STATUS_SOLVED); ?></td>
                    <td><?php echo $project->getStatusBugCount(BUG_STATUS_CLOSED); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="padding-left: 30px;">sorry，暂时还没有任何项目信息!</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div style="width: 100%">
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
