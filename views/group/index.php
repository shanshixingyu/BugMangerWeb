<?php
/**
 * 团队管理界面
 * Created by GuLang on 2015-04-30.
 */

/* @var $this yii\web\View */
/* @var $pagination yii\data\Pagination */

$this->title = '团队管理';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    $this->title,
];

$deleteJs = <<<JS
$('.deleteUser').click(function(){
    if (confirm("用户删除将同时删除其所在用户组中的信息，并且无法恢复，是否确认删除用户？")) {
        var row = $(this).parent().parent();
        $.get('index.php?r=user/delete', {userId: row.data('key')}, function (result) {
            if (result !== null && result.toUpperCase() == "SUCCESS") {
                alert('用户删除成功！');
                window.location.reload();
            } else {
                alert('用户删除失败！');
            }
        });
    }
});
JS;
$this->registerJs($deleteJs);
$this->registerCssFile(CSS_PATH . 'show_manager.css');
?>

<div id="showInfoWrap">
    <div id="aboveShowInfTable">
        <div id="showInfoTableName">团队信息表</div>
        <a href="index.php?r=group/add" class="optBtn">添加团队</a>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th class="showInfoTableTh" width="12%">用户组名</th>
            <th class="showInfoTableTh" width="23%">成员</th>
            <th class="showInfoTableTh" width="34%">团队简介</th>
            <th class="showInfoTableTh" width="8%">创建者</th>
            <th class="showInfoTableTh" width="15%">创建时间</th>
            <th class="showInfoTableTh" width="8%">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($groupDetails) && count($groupDetails) > 0): ?>
            <?php foreach ($groupDetails as $groupDetail): ?>
                <tr>
                    <td><?php echo $groupDetail->name; ?></td>
                    <td>
                        <?php
                        if (isset($groupDetail->member) && is_array($groupDetail->member)) {
                            foreach ($groupDetail->member as $one) {
                                echo '<div class="showCellInfoItem">', $one->name, '</div>';
                            }
                        }
                        ?>
                    </td>
                    <td><?php echo $groupDetail->introduce; ?></td>
                    <td>
                        <?php echo (isset($groupDetail->createUser) && isset($groupDetail->createUser->name)) ? $groupDetail->createUser->name : ''; ?>
                    </td>
                    <td><?php echo $groupDetail->create_time; ?></td>
                    <td>
                        <a href="#" title="编辑">
                            <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                        <!--                        <a href="index.php?r=group/xxx&id=-->
                        <?php //echo $groupDetail->id; ?><!--" title="编辑"><span-->
                        <!--                                class="glyphicon glyphicon-pencil"></span></a>-->
                        &nbsp;&nbsp;
                        <a class="deleteGroup" href="javascript:void(0)" title="删除">
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="padding-left: 30px;">sorry，暂时还没有任何团队信息!</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div style="width: 100%;background-color: red;">
        <!--        --><?php
        //            $startIndex = $pagination->page * $pagination->limit + 1;
        //            $endIndex=$startIndex+$pagination->
        //        ?>
        <div class="showInfoSummary">当前页&nbsp;<?php echo $pagination->offset + 1; ?>
            -
            <?php
            /* 先判断当前页是否为最后一页（page+1==pageCount）,如果是最后一页，当前页的大小为 总数-offset，否则为pageSize */
            if ($pagination->page + 1 == $pagination->pageCount) {
                echo $pagination->totalCount;
            } else {
                echo $pagination->offset + $pagination->pageSize;
            }
            ?>
            &nbsp;&nbsp;&nbsp;总记录数&nbsp;<?php echo $pagination->totalCount; ?><br/></div>
    </div>
    <?php echo \yii\widgets\LinkPager::widget([
        'pagination' => $pagination,
    ]); ?>
</div>
