<?php
/**
 * 显示bug详情
 * Created by GuLang on 2015-05-06.
 */
use app\models\Module;
use yii\helpers\Json;
use app\tools\MyConstant;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
$this->title = '项目缺陷详情';
$this->params['breadcrumbs'] = [
    ['label' => '项目缺陷概况', 'url' => 'index.php?r=bug/index'],
//    isset($_SERVER['HTTP_REFERER']) ? ['label' => '项目缺陷列表', 'url' => $_SERVER['HTTP_REFERER']] : null,
    $this->title,
];
$bugStatus = Json::decode(BUG_STATUS);
$bugPriority = Json::decode(BUG_PRIORITY);

$this->registerCssFile(CSS_PATH . 'show_bug.css');
$this->registerCssFile(CSS_PATH . 'edit.css');
$this->registerCssFile(CSS_PATH . 'idangerous.swiper.css');
$this->registerJsFile(JS_PATH . 'idangerous.swiper.min.js', ['position' => \yii\web\View::POS_HEAD]);
$bugStatus = Json::decode(BUG_STATUS);
?>
<div class="editInfo">
    <div class="editInfoTitle">
        <div class="editInfoTitleIcon"></div>
        Bug详细信息
    </div>
    <div class="editInfoForm">
        <div id="opt_area">
            <?php if ($bug->status == array_search(BUG_STATUS_UNSOLVED, $bugStatus) || $bug->status == array_search(BUG_STATUS_ACTIVE, $bugStatus)): ?>
                <a href="#" id="resolveBug"><img src="<?php echo IMG_PATH; ?>ic_resolve.png">&nbsp;解决</a>
            <?php endif; ?>
            <?php if ($bug->creator_id == Yii::$app->user->identity->getId()): ?>
                <?php if ($bug->status == array_search(BUG_STATUS_UNSOLVED, $bugStatus)): ?>
                    <a href="index.php?r=bug/test"><img src="<?php echo IMG_PATH; ?>ic_pan.png">&nbsp;修改</a>
                <?php endif; ?>
                <?php if ($bug->status == array_search(BUG_STATUS_SOLVED, $bugStatus)): ?>
                    <a href="#"><img src="<?php echo IMG_PATH; ?>ic_active.png">&nbsp;激活</a>
                    <a href="#"><img src="<?php echo IMG_PATH; ?>ic_close.png">&nbsp;关闭</a>
                <?php endif; ?>
                <a href="#"><img src="<?php echo IMG_PATH; ?>ic_delete.png">&nbsp;删除</a>
            <?php endif; ?>

        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">Bug名称：</div>
            <div class="itemContent"><?php echo $bug->name; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">Bug路径：</div>
            <div class="itemContent">
                <?php
                echo $bug->project->name;
                if ($bug->module_id > 0) {
                    $module = Module::find()->where(['id' => $bug->module_id])->one();
                    if ($module !== null) {
                        echo '&nbsp;&nbsp;-&nbsp;&nbsp;', $module->name;
                    }
                }
                ?>
            </div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">状态：</div>
            <div class="itemContent"><?php echo $bugStatus[$bug->status]; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">指派给：</div>
            <div class="itemContent"><?php echo $bug->assign->name; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">优先级：</div>
            <div class="itemContent"><?php echo $bugPriority[$bug->priority]; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">提交者：</div>
            <div class="itemContent"><?php echo $bug->creator->name; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">提交日期：</div>
            <div class="itemContent"><?php echo $bug->create_time; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">激活次数：</div>
            <div class="itemContent"><?php echo $bug->active_num; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">关闭日期：</div>
            <div class="itemContent"><?php echo $bug->close_time; ?></div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">Bug截图：</div>
            <div class="itemContent">
                <?php
                try {
                    $imagesNames = Json::decode($bug->img_path);
                } catch (\yii\base\Exception $e) {
                    $imagesNames = [];
                }
                if (count($imagesNames) > 0):?>
                    <div class="swiper-container" style="width: 100%">
                        <div class="swiper-wrapper">

                            <?php foreach ($imagesNames as $imagesName) : ?>
                                <div class="swiper-slide">
                                    <img src="<?php echo MyConstant::BIG_IMAGE_PATH, $imagesName; ?>" alt="缺陷截图">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="pagination"></div>
                    </div>
                <?php else: ?>
                    暂无
                <?php endif; ?>
            </div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">Bug注释：</div>
            <div class="itemArea">
                <?php $introduces = Json::decode($bug->introduce); ?>
                <?php foreach ($introduces as $introduce): ?>
                    <div class="introduceTitle"><?php echo $introduce['title'], '<br/>'; ?></div>
                    <div class="introduceContent">
                        <?php echo $introduce['content'], '<br/>'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">重现步骤：</div>
            <?php if (isset($bug->reappear) && trim($bug->reappear) != ''): ?>
                <div class="itemArea"><?php echo $bug->reappear, '<br/>'; ?></div>
            <?php else: ?>
                <div class="itemContent"></div>
            <?php endif; ?>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">附件：</div>
            <div class="itemContent">
                <?php
                if (isset($bug->file_path) && trim($bug->file_path) != '')
                    echo '<a href="index.php?r=bug/download&fileName=' . $bug->file_path . '" target="_blank">' . $bug->file_path . '</a>';
                else
                    echo '暂无';
                ?>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin([
    'id' => 'resolveBugModal',
    'header' => '<div id="showModalHeader">解决BUG</div>',
    'size' => Modal::SIZE_DEFAULT,
]);
$resolveForm = new \app\models\ResolveForm();
echo $this->render('resolve', ['bug' => $bug, 'resolveForm' => $resolveForm]);
Modal::end(); ?>
<script>
    $(document).ready(function () {
        $('#resolveBug').click(function () {
            $('#resolveBugModal').modal('toggle');
        });

    });
</script>

<script>
    var mySwiper = new Swiper('.swiper-container', {
        pagination: '.pagination',
        paginationClickable: true,
        slidesPerView: 'auto'
    })
</script>