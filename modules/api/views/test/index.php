<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = '登录测试';
?>

<div style="margin: 20px;">

    <?php
    if (Yii::$app->user->isGuest)
        echo "没有登录";
    else if (isset(Yii::$app->user->identity) && isset(Yii::$app->user->identity->name))
        echo "已经登录，用户为：", Yii::$app->user->identity->name;
    else
        echo "其它情况";
    ?>

    <?php
    echo Html::beginForm('index.php?r=api/site/login', 'post', []), '<br/>';
    echo Html::textInput("name", null, ['style' => 'width:150px;padding:5px;margin:10px;', 'placeholder' => '账号']), '<br/>';
    echo Html::passwordInput("password", null, ['style' => 'width:150px;padding:5px;margin:10px;', 'placeholder' => '密码']), '<br/>';
    echo Html::checkbox("rememberMe", false, []), '<label>记住我</label>', '<br/>';
    echo Html::submitButton('登录', ['style' => 'margin:10px;']);
    echo Html::endForm();
    ?>
    <br/> <br/> <br/>
    <a href="index.php?r=api/site/logout" target="_blank">退出登录</a>

    <!--    <form action="index.php?r=api/site/login" method="post">-->
    <!--        <input type="text" name="name" style="width:150px;padding:5px;margin:10px;" placeholder="账号"/><br/>-->
    <!--        <input type="text" name="password" style="width:150px;padding:5px;margin:10px;" placeholder="密码"/><br/>-->
    <!--        <input type="submit" value="密码"/><br/>-->
    <!--    </form>-->


    <br/> <br/> <br/>
    <a href="index.php?r=api/site/pim" target="_blank">个人信息</a><br/><br/>
    <a href="index.php?r=api/bug/index" target="_blank">Bug概况</a><br/><br/>

    <?php
    echo Html::beginForm('index.php?r=api/site/modify-email', 'post', []), '<br/>';
    echo Html::textInput("email", null, ['style' => 'width:150px;padding:5px;margin:10px;', 'placeholder' => '修改邮箱']), '<br/>';
    echo Html::submitButton('修改邮箱', ['style' => 'margin:10px;']);
    echo Html::endForm();
    ?>
    <?php
    echo Html::beginForm('index.php?r=api/site/modify-password', 'post', []), '<br/>';
    echo Html::textInput("oldPassword", null, ['style' => 'width:150px;padding:5px;margin:10px;', 'placeholder' => '原密码']), '<br/>';
    echo Html::textInput("newPassword", null, ['style' => 'width:150px;padding:5px;margin:10px;', 'placeholder' => '新密码']), '<br/>';
    echo Html::submitButton('修改密码', ['style' => 'margin:10px;']);
    echo Html::endForm();
    ?>

    root对应的密码是：
    <?php
    echo \app\tools\PasswordUtils::getEncryptedPassword('root');
    ?>

    

</div>




