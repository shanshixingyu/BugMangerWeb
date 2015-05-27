<?php
/**
 * 产品模块模态框界面
 * Created by GuLang on 2015-04-27.
 */
use yii\helpers\Html;

/* @var $this \yii\web\View */
$this->registerCssFile(CSS_PATH . "show_module.css");
?>
<table id="showModuleTable">
    <tr>
        <th style="width:17.65%;">模块名称</th>
        <th style="width:11.76%;">负责人</th>
        <th style="width:47.06%;">模块简介</th>
        <th style="width:9.41%;">创建者</th>
        <th style="width:14.12%;">创建时间</th>
    </tr>
</table>
<div style="width:100%;text-align:center;">
    <?php echo Html::button('关闭', [
        'id' => "closeModalBtn",
        'class' => 'btn btn-primary',
    ]) ?>
</div>
