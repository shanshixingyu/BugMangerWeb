<?php
/**
 * 重置密码邮箱
 * Created by GuLang on 2015-05-27.
 */
use app\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody() ?>
<p>亲爱的<span style="color:red;font-weight: bold;"><?php echo $userName; ?></span>:</p>

<p>请点击下面的链接重置您在软件缺陷管理系统中的用户的密码:</p>

<p><a href="<?php echo $emailUrl; ?>" target="_blank"><?php echo $emailUrl; ?></a></p>

<p>请注意：</p>

<p>1、您必须在24小时之内点击链接才能够重置密码，否则此邮件的重置链接将无效作废！</p>

<p>2、重置成功后，系统会自动将您的密码将重置为：123456。</p>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
