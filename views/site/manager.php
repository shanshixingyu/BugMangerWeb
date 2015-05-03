<?php
/**
 * 后台管理界面
 * Created by GuLang on 2015-04-22.
 */
/* @var $this yii\web\View */
$this->title = '后台管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(CSS_PATH . 'manager.css');
?>

<div id="managerWrap">
    <a href="index.php?r=user/index" class="managerItem">
        <div id="userManagerIcon"></div>
        用户管理
    </a>
    <a href="index.php?r=group/index" class="managerItem">
        <div id="groupManagerIcon"></div>
        团队管理
    </a>
    <a href="index.php?r=project/index" class="managerItem">
        <div id="productManagerIcon"></div>
        项目管理
    </a>
    <a href="#" class="managerItem">
        <div id="settingManagerIcon"></div>
        系统设置
    </a>
</div>