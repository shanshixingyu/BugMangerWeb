<?php
/**
 * 项目管理界面
 * Created by GuLang on 2015-04-22.
 */
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
$this->title = '项目管理';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'show_manager.css');
$this->registerJsFile(JS_PATH . 'project_manager.js', [
    'depends' => [\app\assets\AppAsset::className()]
]);

?>

<div id="showInfoWrap">
    <div id="aboveShowInfTable">
        <div id="showInfoTableName">项目信息表</div>
        <?php
        $myRole = Yii::$app->user->identity->role_id;
        if ($myRole == 0 || $myRole == 1) {
            echo Nav::widget([
                'options' => ['class' => 'dropDownBtn'],
                'items' => [
                    [
                        'label' => '添加项目/模块',
//                    'label' => '添加操作',
                        'linkOptions' => ['id' => 'dropDownOpt'],
                        'items' => [
                            [
                                'label' => '添加项目',
                                'url' => ['project/add-project'],
                            ],
                            [
                                'label' => '添加模块',
                                'url' => ['project/add-module'],
                            ],
                        ],
                    ],
                ],
            ]);
        }
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
                'label' => '项目名称',
                'contentOptions' => ['class' => 'projectName'],
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '11%'],
            ],
            [
                'label' => '负责团队',
                'value' => function ($data) {
                    return isset($data->group->name) ? $data->group->name : '';
                },
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '12%'],
            ],
            [
                'label' => '项目介绍',
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
                'template' => '{seeModule}&nbsp;{modify-project}&nbsp;{modify-module}&nbsp;{deleteProject}',
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
                    'modify-project' => function ($url, $model, $key) {
                        if ($model->creator == Yii::$app->user->identity->getId() || Yii::$app->user->identity->role_id == 0) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => '修改项目信息',
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'modify-module' => function ($url, $model, $key) {
                        if ($model->creator == Yii::$app->user->identity->getId() || Yii::$app->user->identity->role_id == 0) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => '修改模块信息',
                            ]);
                        } else {
                            return '';
                        }
                    },
                    'deleteProject' => function ($url, $model, $key) {
                        if ($model->creator == Yii::$app->user->identity->getId() || Yii::$app->user->identity->getId() == 0) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0);', [
                                'title' => '删除',
                                'class' => 'deleteProject',
                            ]);
                        } else {
                            return '';
                        }
                    },

                ],
            ],
        ],
    ]);
    ?>
</div>

<?php Modal::begin([
    'id' => 'showModuleModal',
    'header' => '<div id="showModalHeader">项目模块详情</div>',
    'size' => Modal::SIZE_LARGE,
]); ?>
<?php echo $this->render('show_module') ?>
<?php Modal::end(); ?>

