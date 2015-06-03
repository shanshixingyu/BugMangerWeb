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

    <br/>
    <a href="index.php?r=api/bug/download&fileName=是的弄.txt">下载文件</a>

    <br/><br/><br/>
    添加用户：<br/>

    <form action="index.php?r=api/user/add" method="post">
        用户名<input type="text" name="name"/><br/>
        角色ID<input type="text" name="roleId"/><br/>
        邮箱<input type="email" name="email"/><br/>

        <input type="submit" value="提交"/><br/>
    </form>

    <br/><br/><br/>
    修改用户信息：<br/>

    <form action="index.php?r=api/user/modify&userId=38" method="post">
        用户名<input type="text" name="name"/><br/>
        角色ID<input type="text" name="roleId"/><br/>
        邮箱<input type="email" name="email"/><br/>

        <input type="submit" value="提交"/><br/>
    </form>

    <br/><br/><br/>
    添加团队信息：<br/>

    <form action="index.php?r=api/group/add" method="post">
        团队名称<input type="text" name="name"/><br/>
        团队成员<input type="text" name="memberIds" value="[]"/><br/>
        团队简介<input type="text" name="introduce"/><br/>

        <input type="submit" value="提交"/><br/>
    </form>

    <br/><br/><br/>
    <a href="index.php?r=api/project/get-all-group"> 获得所有的团队信息</a>

    <br/><br/><br/>
    添加项目信息：<br/>

    <form action="index.php?r=api/project/add-project" method="post">
        项目名称<input type="text" name="name"/><br/>
        负责团队<input type="text" name="groupId"/><br/>
        项目简介<input type="text" name="introduce"/><br/>
        <input type="submit" value="提交"/><br/>
    </form>

    <br/><br/><br/>
    修改项目信息：<br/>

    <form action="index.php?r=api/project/modify-project&projectId=149" method="post">
        项目名称<input type="text" name="name"/><br/>
        负责团队<input type="text" name="groupId"/><br/>
        项目简介<input type="text" name="introduce"/><br/>
        <input type="submit" value="提交"/><br/>
    </form>

</div>




