<?php
/**
 * 产品管理界面
 * Created by GuLang on 2015-04-22.
 */
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
$this->title = '产品管理';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'show_manager.css');
$this->registerJsFile(JS_PATH . 'product_manager.js', [
    'depends' => [\app\assets\AppAsset::className()]
]);

?>

<div id="showInfoWrap">
    <div id="aboveShowInfTable">
        <div id="showInfoTableName">产品信息表</div>
        <?php
        echo Nav::widget([
            'options' => ['class' => 'dropDownBtn'],
            'items' => [
                [
                    'label' => '添加产品/模块',
//                    'label' => '添加操作',
                    'linkOptions' => ['id' => 'dropDownOpt'],
                    'items' => [
                        [
                            'label' => '添加产品',
                            'url' => ['product/add-product'],
                        ],
                        [
                            'label' => '添加模块',
                            'url' => ['product/add-module'],
                        ],
                    ],
                ],
            ],
        ]);
        ?>
    </div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'summary' => '<div class="showInfoSummary">当前页&nbsp;{begin}-{end}&nbsp;&nbsp;&nbsp;总记录数&nbsp;{totalCount}<br/></div>',
        'pager' => ['options' => ['id' => 'paginationWidget', 'class' => 'pagination',]],
        'dataColumnClass' => \yii\grid\DataColumn::className(),
        'columns' => [
            [
                'attribute' => 'name',
                'label' => '产品名称',
                'contentOptions' => ['class' => 'productName'],
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '11%'],
            ],
            [
                'label' => '负责团队',
                'value' => function ($data) {
                    return isset($data->groupDetail->name) ? $data->groupDetail->name : '';
                },
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '12%'],
            ],
            [
                'label' => '产品介绍',
                'enableSorting' => false,
                'value' => function ($data) {
                    return isset($data->introduce) ? $data->introduce : '';
                },
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '50%'],
            ],
            [
                'label' => '创建者',
                'value' => function ($data) {
                    return isset($data->createUser->name) ? $data->createUser->name : '';
                },
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '8%'],
            ],
            [
                'attribute' => 'create_time',
                'label' => '创建时间',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '8%'],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{seeModule}&nbsp;{modify-product}&nbsp;{modify-module}&nbsp;{deleteProduct}',
                'header' => '操作',
                'headerOptions' => [
                    'class' => 'showInfoTableTh',
                    'width' => '10%',
                ],
                'buttons' => [
                    'seeModule' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0);', [
                            'title' => '查看模块',
                            'class' => 'seeModule',
                        ]);
                    },
                    'modify-product' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => '修改产品信息',
                        ]);
                    },
                    'modify-module' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => '修改模块信息',
                        ]);
                    },
                    'deleteProduct' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0);', [
                            'title' => '删除',
                            'class' => 'deleteProduct',
                        ]);
                    },

                ],
            ],
        ],
    ]);
    ?>
</div>

<?php Modal::begin([
    'id' => 'showModuleModal',
    'header' => '<div id="showModalHeader">产品模块详情</div>',
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php echo $this->render('show_module') ?>
<?php Modal::end(); ?>

