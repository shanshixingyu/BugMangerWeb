<?php
/**
 * 主页面模板
 * Created by GuLang on 2015-04-18.
 */
use app\assets\AppAsset;
use yii\helpers\Html;
use \yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */
AppAsset::register($this);
Yii::$app->homeUrl = 'index.php?r=site/bug';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!--    <meta name="viewport" content="width=device-width, initial-scale=1">-->
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?php echo CSS_PATH; ?>main.css" rel="stylesheet" type="text/css"/>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody() ?>
<div id="header">
    <div id="titleLogo"></div>
    <ul id="navigation">
        <li class="navigationItem">欢迎你，<span style="color: #FF0000;font-weight:bold;">admin</span></li>
        <li class="navigationItem"><span class="navigationDivider">|</span><a href="index.php?r=site/pim">个人信息管理</a>
        </li>
        <li class="navigationItem"><span class="navigationDivider">|</span><a href="index.php?r=site/manager">后台管理</a></li>
        <li class="navigationItem"><span class="navigationDivider">|</span><a href="#">退出系统</a></li>
    </ul>
</div>
<div id="content">
    <div id="leftContent">
        <div id="productList">
            <?php echo Html::dropDownList("productSelector", null, ['孤狼软件', '毕设'], ["id" => 'productSelector']); ?>
            <div id="productContent">
                <p>神农放松是神农放松是神农放松是神农放松是</p>

                <p>神农放松是神农放松是神农放松是</p>

                <p>神农放松是神农放松是</p>

                <p>神农放松是</p>

            </div>
        </div>
        <div id="aboutMe">
            <div>个人相关信息</div>
            <p><a href="#">我提交的bug</a></p>

            <p><a href="#">指派给我的bug</a></p>

            <p><a href="#">抄送给我的bug</a></p>
        </div>
    </div>
    <div id="rightContent">
        <?php echo Breadcrumbs::widget([
            'homeLink' => [
                'label' => Yii::t('yii', '首页'),
                'url' => Yii::$app->homeUrl,
            ],
            'options' => ['id' => "breadcrumbs"],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
        <div id="rightContentInternal">
            <?php echo $content; ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
