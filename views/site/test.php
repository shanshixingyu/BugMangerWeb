<?php
/**
 * 测试页面
 * Created by GuLang on 2015-04-23.
 */
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
$this->title = '项目管理';
$this->params['breadcrumbs'][] = ['label' => '后台管理', 'url' => 'index.php?r=site/manager'];
$this->params['breadcrumbs'][] = $this->title;

?>
    <script type="text/javascript">
        $('#modalButton').click(function () {
            $('#showModal').get($('#modalButton').getVal(), function (data) {
                alert(data);
            });
        });
        //        $("#projectModal").modal();
    </script>


    <!--    <a href="index.php?r=site/test1" data-toggle="modal" data-target="#projectModal">点击测试</a>-->
    <button value="index.php?r=site/test" data-toggle="modal" data-target="#projectModal" id="modalButton">
        点击测试
    </button>


    <!--    <!--添加项目模态框-->-->
<?php Modal::begin([
    'header' => '添加项目',
    'id' => 'projectModal',
    'size' => 'modal-lg',
]); ?>
    <div id="showModal">
    </div>
<?php Modal::end(); ?>


<?php if (true): ?>
    sdafnsda osd no
<?php elseif (true): ?>
    sdfmsop mo
<?php endif; ?>

<?php if (true): ?>
<?php else: ?>
<?php endif; ?>