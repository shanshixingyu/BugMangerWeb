<?php
/**
 * 显示bug详情
 * Created by GuLang on 2015-05-06.
 */
use app\models\Module;
use yii\helpers\Json;
use app\tools\MyConstant;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = '项目缺陷详情';
$this->params['breadcrumbs'] = [
    ['label' => '项目缺陷概况', 'url' => 'index.php?r=bug/index'],
    $this->title,
];
$bugStatus = Json::decode(BUG_STATUS);
$bugPriority = Json::decode(BUG_PRIORITY);
$bugSerious = Json::decode(BUG_SERIOUS);

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
            <!--     对于解决操作按钮，只有当Bug状态为“未解决”或者“重新激活”时才显示       -->
            <?php if ($bug->status == array_search(BUG_STATUS_UNSOLVED, $bugStatus) || $bug->status == array_search(BUG_STATUS_ACTIVE, $bugStatus)): ?>
                <a href="#" id="resolveBug"><img src="<?php echo IMG_PATH; ?>ic_resolve.png">&nbsp;解决</a>
            <?php endif; ?>
            <!--    以下几个按钮只有在登录用户为bug创建用户时才显示 -->
            <?php if ($bug->creator_id == Yii::$app->user->identity->getId()): ?>
                <!-- 关闭前都可以修改-->
                <?php if ($bug->status != array_search(BUG_STATUS_CLOSED, $bugStatus)): ?>
                    <a href="index.php?r=bug/modify&bugId=<?php echo $bug->id; ?>">
                        <img src="<?php echo IMG_PATH; ?>ic_pan.png">&nbsp;修改
                    </a>
                <?php endif; ?>
                <?php if ($bug->status == array_search(BUG_STATUS_SOLVED, $bugStatus)
                    || $bug->status == array_search(BUG_STATUS_CLOSED, $bugStatus)
                    || $bug->status == array_search(BUG_STATUS_OTHER, $bugStatus)
                ): ?>
                    <a href="#" id="activeBug"><img src="<?php echo IMG_PATH; ?>ic_active.png">&nbsp;激活</a>
                <?php endif; ?>
                <?php if ($bug->status == array_search(BUG_STATUS_SOLVED, $bugStatus)
                    || $bug->status == array_search(BUG_STATUS_OTHER, $bugStatus)): ?>
                    <a href="#" id="closeBug"><img src="<?php echo IMG_PATH; ?>ic_close.png">&nbsp;关闭</a>
                <?php endif; ?>
                <a href="index.php?r=bug/delete&bugId=<?php echo $bug->id; ?>" data-confirm="删除后将不能够恢复，确定删除？"><img
                        src="<?php echo IMG_PATH; ?>ic_delete.png">&nbsp;删除</a>
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
            <div class="itemTitle">影响程度：</div>
            <div class="itemContent"><?php echo $bugSerious[$bug->serious_id]; ?></div>
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
                                    <img src="<?php echo MyConstant::IMAGE_PATH, $imagesName; ?>" alt="缺陷截图">
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
            <div class="itemArea" style="border: 1px solid #C1C8D2;padding:5px;">
                <?php $introduces = Json::decode($bug->introduce); ?>
                <?php foreach ($introduces as $introduce): ?>
                    <div class="introduceTitle">
                        <?php echo $introduce['time']; ?>&nbsp;&nbsp;<?php echo $introduce['type']; ?>
                        &nbsp;by&nbsp;<?php echo $introduce['name']; ?></div>
                    <div class="introduceContent">
                        <?php echo $introduce['content'], '<br/>'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bugInfoItem">
            <div class="itemTitle">重现步骤：</div>
            <?php if (isset($bug->reappear) && trim($bug->reappear) != ''): ?>
                <div class="itemArea" style="padding:0;">
                    <?php echo Html::textarea('reappear', $bug->reappear, [
                        'readonly' => true,
                        'rows' => 5,
                        'style' => 'resize:none;width:100%;height:100%;float:left;padding:5px;'
                    ]); ?>
                </div>
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

<?php Modal::begin([
    'id' => 'activeBugModal',
    'header' => '<div id="showModalHeader">激活Bug</div>',
    'size' => Modal::SIZE_DEFAULT,
]);
$activeForm = new \app\models\ActiveBugForm();
echo $this->render('active', ['bug' => $bug, 'activeForm' => $activeForm]);
Modal::end(); ?>

<?php Modal::begin([
    'id' => 'closeBugModal',
    'header' => '<div id="showModalHeader">关闭Bug</div>',
    'size' => Modal::SIZE_DEFAULT,
]);
$closeForm = new \app\models\CloseBugForm();
echo $this->render('close', ['bug' => $bug, 'closeForm' => $closeForm]);
Modal::end(); ?>


<script>
    $(document).ready(function () {
        $('#resolveBug').click(function () {
            $('#resolveBugModal').modal('toggle');
        });
        $('#activeBug').click(function () {
            $('#activeBugModal').modal('toggle');
        });
        $('#closeBug').click(function () {
            $('#closeBugModal').modal('toggle');
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