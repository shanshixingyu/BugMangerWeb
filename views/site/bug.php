<?php
/**
 * bug详情页面
 */
/* @var $this yii\web\View */
$this->title = 'bug列表';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(CSS_PATH . 'bug.css');
?>

<div id="bugQueryCondition">
    bug信息查询条件表单
</div>
<div id="bugTable">
    <table>
        <tr>
            <th style="width:30px;">ID</th>
            <th style="width:325px;">Bug名称</th>
            <th style="width:100px;">产品名称</th>
            <th style="width:100px;">当前状态</th>
            <th style="width:75px;">严重程度</th>
            <th style="width:50px;">指派给</th>
            <th style="width:50px;">提交者</th>
            <th style="width:50px;">提交时间</th>
        </tr>
        <tr>
            <td>1232</td>
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