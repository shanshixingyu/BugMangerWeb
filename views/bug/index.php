<?php
/**
 * bug详情页面
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $this yii\web\View */
$this->title = '产品缺陷概况';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(CSS_PATH . 'bug.css');
?>
<div id="bugTable">
    <table>
        <tr>
            <th style="width:40%;">项目名称</th>
            <th style="width:12px;">Bug总数</th>
            <th style="width:12px;">未解决Bug数</th>
            <th style="width:12px;">当前激活Bug数</th>
            <th style="width:12px;">已解决Bug数</th>
            <th style="width:12px;">关闭Bug数</th>
        </tr>
<!--        <tr>-->
        <!--            <td><a href="#">手动bug</a></td>-->
        <!--            <td>孤狼软件</td>-->
        <!--            <td>解决未审核</td>-->
        <!--            <td>非常严重</td>-->
        <!--            <td>hwz</td>-->
        <!--            <td>孤狼</td>-->
        <!--            <td>2014-12-05</td>-->
        <!--        </tr>-->
    </table>
</div>
