<?php
/**
 * 操作结果提醒界面
 * Created by GuLang on 2015-04-28.
 */
/* @var $this \yii\web\View */
$this->title = '用户管理'; //给人一种在加载产品管理却还没加载出来的错觉

$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    $this->title,
];
?>
<script type="text/javascript">
    window.onload = function () {
        var msg = '<?php echo (isset($message)?$message:'');?>';
        alert(msg);
        window.location.href = 'index.php?r=user/index';
    };
</script>



