<?php
/**
 * 用户管理界面
 * Created by GuLang on 2015-04-29.
 */

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'] = [
    ['label' => '后台管理', 'url' => 'index.php?r=site/manager'],
    $this->title,
];

$this->registerCssFile(CSS_PATH . 'show_manager.css');
?>

<div id="showInfoWrap">
    <div id="aboveShowInfTable">
        <div id="showInfoTableName">用户信息表</div>
        <?php
        $myRole = Yii::$app->user->identity->role_id;
        if ($myRole == 0 || $myRole == 1):?>
            <a href="index.php?r=user/add" class="optBtn">添加用户</a>
        <?php endif; ?>
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
                'label' => '用户',
                'contentOptions' => ['class' => 'projectName'],
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '19%'],
            ],
            [
                'attribute' => 'role_id',
                'label' => '用户角色',
                'enableSorting' => false,
                'value' => function ($data) {
                    return isset($data->role) && isset($data->role->name) ? $data->role->name : $data->role_id;
                },
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '15%'],
            ],
            [
                'label' => '用户邮箱',
                'value' => function ($data) {
                    return isset($data->email) ? $data->email : '';
                },
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '25%'],
            ],
            [
//            'attribute' => 'creator',
                'label' => '创建者',
                'value' => function ($data) {
                    return isset($data->createUser->name) ? $data->createUser->name : '';
                },
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '12%'],
            ],
            [
                'attribute' => 'create_time',
                'label' => '创建时间',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'enableSorting' => false,
                'headerOptions' => ['class' => 'showInfoTableTh', 'width' => '18%'],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{modify}&nbsp;&nbsp;&nbsp;{delete}',
                'header' => '操作',
                'headerOptions' => [
                    'class' => 'showInfoTableTh',
                    'width' => '10%',
                ],
                'buttons' => [
                    'modify' => function ($url, $model, $key) {
                        if ($model->creator == Yii::$app->user->identity->getId() || Yii::$app->user->identity->role_id == 0) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => '修改',
                            ]);
                        } else {
                            return '无权限';
                        }
                    },
                    'delete' => function ($url, $model, $key) {
                        if ($model->creator == Yii::$app->user->identity->getId() || Yii::$app->user->identity->role_id == 0) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0)', [
                                'title' => '删除',
                                'class' => 'deleteUser',
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.deleteUser').click(function () {
            if (confirm("删除用户的同时将删除其所在用户组中的信息，并且无法恢复，是否确认删除用户？")) {
                var row = $(this).parent().parent();
                $.get('index.php?r=user/delete', {userId: row.data('key')}, function (result) {
                    if (result !== null && result.toUpperCase() == "SUCCESS") {
                        alert('用户删除成功！');
                        window.location.reload();//主要为了用户关闭了提示框后刷新界面
                    } else {
                        alert('用户删除失败！');
                    }

                });
            }
        });
    });
</script>