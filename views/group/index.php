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

$this->registerCssFile(CSS_PATH . 'show_manager.css');
?>

<div id="showInfoWrap">
    <div id="aboveShowInfTable">
        <div id="showInfoTableName">团队信息表</div>
        <?php
        $myRole = Yii::$app->user->identity->role_id;
        if ($myRole == 0 || $myRole == 1):?>
            <a href="index.php?r=group/add" class="optBtn">新建团队</a>
        <?php endif; ?>
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
        <?php if (isset($groups) && count($groups) > 0): ?>
            <?php foreach ($groups as $group): ?>
                <tr data-key="<?php echo $group->id; ?>">
                    <td><?php echo $group->name; ?></td>
                    <td>
                        <?php
                        if (isset($group->member) && is_array($group->member)) {
                            foreach ($group->member as $one) {
                                echo '<div class="showCellInfoItem">', $one->name, '</div>';
                            }
                        }
                        ?>
                    </td>
                    <td><?php echo $group->introduce; ?></td>
                    <td>
                        <?php echo (isset($group->createUser) && isset($group->createUser->name)) ? $group->createUser->name : ''; ?>
                    </td>
                    <td><?php echo $group->create_time; ?></td>
                    <td>
                        <?php if ($group->creator == Yii::$app->user->identity->getId() || Yii::$app->user->identity->role_id == 0): ?>
                            <a href="index.php?r=group/modify&id=<?php echo $group->id; ?>" title="编辑">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                            &nbsp;&nbsp;
                            <a class="deleteGroup" href="javascript:void(0)" title="删除">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        <?php else: ?>
                            无权限
                        <?php endif; ?>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.deleteGroup').click(function () {
            if (confirm("删除后将无法恢复，是否确认删除？")) {
                var row = $(this).parent().parent();
                $.get('index.php?r=group/delete', {id: row.data('key')}, function (data) {
                    var result = $.parseJSON(data);
                    if (result !== null) {
                        alert(result.message);
                    } else {
                        alert('删除失败！');
                    }
                    window.location.reload();//主要为了用户关闭了提示框后刷新界面
                });
            }
        });
    });
</script>
