<?php
/**
 * 测试页面
 * Created by GuLang on 2015-04-23.
 */
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
$this->title = '产品管理';
$this->params['breadcrumbs'][] = ['label' => '后台管理', 'url' => 'index.php?r=site/manager'];
$this->params['breadcrumbs'][] = $this->title;

?>
    <script type="text/javascript">
        $('#modalButton').click(function () {
            $('#showModal').get($('#modalButton').getVal(), function (data) {
                alert(data);
            });
        });
        //        $("#productModal").modal();
    </script>


    <!--    <a href="index.php?r=site/test1" data-toggle="modal" data-target="#productModal">点击测试</a>-->
    <button value="index.php?r=site/test" data-toggle="modal" data-target="#productModal" id="modalButton">
        点击测试
    </button>


    <!--    <!--添加产品模态框-->-->
<?php Modal::begin([
    'header' => '添加产品',
    'id' => 'productModal',
    'size' => 'modal-lg',
]); ?>
    <div id="showModal">
        <div>
<?php Modal::end(); ?>