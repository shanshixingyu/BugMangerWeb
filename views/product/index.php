<?php
/**
 * 产品管理界面
 * Created by GuLang on 2015-04-22.
 */
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = '产品管理';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    $this->title,
];
$this->registerCssFile(CSS_PATH . 'productManager.css');
$this->registerCssFile(CSS_PATH . 'addProduct.css');


?>

<div id="productWrap">
    <div id="aboveProductTable">
        <div id="productTableName">产品信息表</div>
        <a class="addBtn" href="index.php?r=site/addModule"><span>添加模块</span></a>
        <a class="addBtn" href="index.php?r=site/addProduct"><span>添加产品</span></a>
    </div>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}\n{summary}",
        'summary' => '<div class="productSummary">当前页&nbsp;{begin}-{end}&nbsp;&nbsp;&nbsp;总记录数&nbsp;{totalCount}<br/></div>',
        'pager' => ['options' => ['id' => 'paginationWidget', 'class' => 'pagination',]],
        'dataColumnClass' => \yii\grid\DataColumn::className(),
        'columns' => [
            [
                'attribute' => 'id',
                'label' => 'ID',
                'enableSorting' => false,
                'headerOptions' => ['class' => 'productTableTh', 'width' => '4%'],
            ],
            [
                'attribute' => 'name',
                'label' => '产品名称',
                'enableSorting' => false,
                'headerOptions' => ['class' => 'productTableTh', 'width' => '10%'],
            ],
            [
                'label' => '负责用户组',
                'value' => function ($data) {
                    return isset($data->groupDetail->name) ? $data->groupDetail->name : '';
                },
                'headerOptions' => ['class' => 'productTableTh', 'width' => '12%'],
            ],
            [
                'label' => '产品介绍',
                'enableSorting' => false,
                'value' => function ($data) {
                    return isset($data->introduce) ? $data->introduce : '';
                },
                'headerOptions' => ['class' => 'productTableTh', 'width' => '50%'],
            ],
            [
                'label' => '创建者',
                'value' => function ($data) {
                    return isset($data->createUser->name) ? $data->createUser->name : '';
                },
                'enableSorting' => false,
                'headerOptions' => ['class' => 'productTableTh', 'width' => '8%'],
            ],
            [
                'attribute' => 'create_time',
                'label' => '创建时间',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'enableSorting' => false,
                'headerOptions' => ['class' => 'productTableTh', 'width' => '8%'],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{seeModule}&nbsp;{update}&nbsp;{delete}&nbsp;{sb}',
                'header' => '操作',
                'buttons' => [
                    'seeModule' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => '查看模块',
                            'data-pjax' => '0',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => '编辑',
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => '删除',
                            'data-confirm' => '确定删除该项产品信息?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },

                ],
                'headerOptions' => [
                    'class' => 'productTableTh',
                    'width' => '8%',
                ],
            ],
        ],
    ]);
    ?>

</div>

