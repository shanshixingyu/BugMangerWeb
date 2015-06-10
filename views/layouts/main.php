<?php
/**
 * 主页面模板
 * Created by GuLang on 2015-04-18.
 */
use app\assets\AppAsset;
use yii\helpers\Html;
use \yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\tools\BaseUtils;

/* @var $this \yii\web\View */
/* @var $content string */
AppAsset::register($this);
Yii::$app->homeUrl = 'index.php?r=bug/index';

if (Yii::$app->session->hasFlash(OPT_RESULT)) {
    $this->registerJs('window.onload=function(){alert("' . Yii::$app->session->getFlash(OPT_RESULT) . '");}');
    Yii::$app->session->removeFlash(OPT_RESULT);
}
$projectModuleInfo = BaseUtils::getProjectModuleInfo();
$this->registerJsFile(ASSETS_PATH . '10b978a4/jquery.js', ['position' => View::POS_HEAD]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?php echo CSS_PATH; ?>main.css" rel="stylesheet" type="text/css"/>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody() ?>
<div id="header">
    <div id="titleLogo"></div>
    <ul id="navigation">
        <li class="navigationItem">欢迎你，
            <span style="color: #FF0000;font-weight:bold;">
                <?php echo Yii::$app->user->identity->name; ?>
            </span>
            ( <?php echo Yii::$app->user->identity->role->name; ?>)
        </li>
        <li class="navigationItem"><span class="navigationDivider">|</span><a href="index.php?r=site/pim">个人信息管理</a>
        </li>
        <?php if (isset(Yii::$app->user->identity->role_id) && Yii::$app->user->identity->role_id < 2): //只有超级管理员和管理人员才有后台管理的权限?>
            <li class="navigationItem"><span class="navigationDivider">|</span>
                <a href="index.php?r=site/manager">后台管理</a>
            </li>
        <?php endif; ?>
        <li class="navigationItem"><span class="navigationDivider">|</span><a href="index.php?r=site/logout">退出系统</a>
        </li>
    </ul>
</div>
<div id="content">
    <div id="leftContent">
        <div id="projectList">
            <?php
            echo Html::dropDownList("projectSelector", null,
                ArrayHelper::map($projectModuleInfo['projects'], 'id', 'name'), ["id" => 'projectSelector']);
            ?>
            <ul id="moduleContent">
                <?php foreach ($projectModuleInfo['modules'] as $module): ?>
                    <li class="moduleItem"><?php echo $module->name; ?></li>
                <?php endforeach; ?>

            </ul>
        </div>
        <div id="aboutMe">
            <div>个人相关信息</div>
            <p>
                <a href="index.php?r=person/submit">我提交的缺陷&nbsp;
                    ( <span style="font-weight: bold;"><?php echo BaseUtils::getMySubmitBugCount(); ?></span> )
                </a>
            </p>

            <p>
                <a href="index.php?r=person/assign">指派给我的缺陷&nbsp;
                    ( <span style="font-weight: bold;"><?php echo BaseUtils::getAssignToMeBugCount(); ?></span> )
                </a>
            </p>

            <p>
                <a href="index.php?r=person/opt">我操作过的缺陷&nbsp;
                    ( <span style="font-weight: bold;"><?php echo BaseUtils::getMyOptBugCount(); ?></span> )
                </a>
            </p>

            <!--            <p><a href="#">抄送给我的缺陷</a></p>-->
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
<script type="text/javascript">
    $('document').ready(function () {
        $('#projectSelector').change(function () {
            $.get('index.php?r=site/get-module', {projectId: $(this).val()}, function (data) {
                $('#moduleContent').empty();
                var result = $.parseJSON(data);
                $.each(result, function (idx, module) {
                    $('#moduleContent').append('<li class="moduleItem">' + module.name + '</li>');
                });

            });
        });
    });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
