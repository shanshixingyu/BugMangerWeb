<?php
/**
 * 生成报表的界面
 * Created by GuLang on 2015-05-07.
 */
/* @var $this yii\web\View */
use yii\helpers\Json;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\tools\MyConstant;


$this->title = '报表';
$this->params['breadcrumbs'] = [
    ['label' => '项目缺陷概况', 'url' => 'index.php?r=bug/index'],
    $this->title,
];
$this->registerJsFile('laydate/laydate.js');
$this->registerCssFile('laydate/need/laydate.css');
$this->registerJsFile('js/echarts/echarts.js');
$this->registerCssFile('css/charts.css');
?>

<div id="chartsWrap">
    <div id="chartChoice">
        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => '<div class="choiceItemRow">
                                    <div class="choiceItemLabel">{label}</div>
                                    <div class="choiceItemInput">{input}</div>
                                    <div class="choiceItemError">{error}</div>
                                </div>'
            ]
        ]);
        echo $form->field($searchCharts, 'type')->dropDownList(Json::decode(CHART_TYPE), ['class' => 'inputStyle form-control']);
        echo $form->field($searchCharts, 'startDate')->textInput(['id' => 'startDate', 'placeholder' => '开始时间', 'class' => 'laydate-icon inputStyle']);
        echo $form->field($searchCharts, 'endDate')->textInput(['id' => 'endDate', 'placeholder' => '结束时间', 'class' => 'laydate-icon inputStyle']);
        echo Html::submitButton('查询', ['class' => 'btn btn-primary', 'style' => "margin-top:8px;float:left;margin-left:10px;"]);
        ActiveForm::end();
        ?>
    </div>
    <div id="chartContent">
        <div style="height: 400px;" id="testChart"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#startDate').click(function () {
            laydate({istime: false, format: 'YYYY-MM-DD'});
        });
        $('#endDate').click(function () {
            laydate({istime: false, format: 'YYYY-MM-DD'});
        });

        require.config({
            paths: {
                echarts: 'js/echarts'
            }
        });

        require(
            [
                'echarts',
                'echarts/chart/bar',
                'echarts/chart/line',
                'echarts/chart/pie',
            ],
            function drawBarChart(ec) {
                //--- 折柱 ---
                var myChartElement = ec.init(document.getElementById('testChart'));

                <?php
                    $isPie=false;
                    $chartTitle='';
                    $seriesName='';
                    switch($data['type']){
                        case MyConstant::ECHART_TYPE_SUBMIT_DAY:
                            $isPie=false;
                            $chartTitle='项目缺陷提交情况（天）';
                            break;
                        case MyConstant::ECHART_TYPE_SUBMIT_MONTH:
                            $isPie=false;
                            $chartTitle='项目缺陷提交情况（月）';
                            break;
                        case MyConstant::ECHART_TYPE_TOTAL_DAY:
                            $isPie=false;
                            $chartTitle='项目缺陷总量走势（天）';
                            break;
                        case MyConstant::ECHART_TYPE_TOTAL_MONTH:
                            $isPie=false;
                            $chartTitle='项目缺陷总量走势（月）';
                            break;
                        case MyConstant::ECHART_TYPE_MODULE:
                            $isPie=true;
                            $chartTitle='项目各模块缺陷比例';
                            $seriesName='项目模块';
                            break;
                        case MyConstant::ECHART_TYPE_PRIORITY:
                            $isPie=true;
                            $chartTitle='项目缺陷优先级比例';
                            $seriesName='优先级';
                            break;
                        case MyConstant::ECHART_TYPE_STATUS:
                            $isPie=true;
                            $chartTitle='项目缺陷状态比例';
                            $seriesName='缺陷状态';
                            break;
                    }
                ?>

                <?php if($isPie):?>
                myChartElement.setOption(getPieChartOption(
                    '<?php echo $chartTitle;?>',
                    <?php echo Json::encode(array_keys($data['data']));?>, '<?php echo $seriesName;?>',
                    [<?php foreach($data['data'] as $key=>$value):?>
                        {value:<?php echo $value;?>, name: '<?php echo $key;?>'},
                        <?php endforeach;?>]
                ));
                <?php else:?>
                myChartElement.setOption(getCommonChartOption(
                    '<?php echo $chartTitle;?>', ['提交Bug数量'],
                    <?php echo Json::encode(array_keys($data['data']));?>, '提交Bug数量',
                    <?php echo Json::encode(array_values($data['data']))?>
                ));
                <?php endif;?>
            }
        );
        function getCommonChartOption(titleText, legendData, xAxisData, seriesName, seriesData) {
            return {
                title: {
                    show: true,
                    text: titleText
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: legendData
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {show: true},
                        dataView: {show: true, readOnly: true},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                calculable: true,
                xAxis: [
                    {
                        type: 'category',
                        data: xAxisData
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        splitArea: {show: true}
                    }
                ],
                series: [
                    {
                        name: seriesName,
                        type: 'bar',
                        data: seriesData
                    }
                ]
            };
        }

        function getPieChartOption(titleText, legendData, seriesName, seriesData) {
            return {
                title: {
                    show: true,
                    text: titleText
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    data: legendData,
                    orient: 'horizontal',
                    x: 'center',
                    y: 'bottom'
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {show: true},
                        dataView: {show: true, readOnly: true},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                series: [
                    {
                        name: seriesName,
                        type: 'pie',
                        radius: '75%',
                        center: ['50%', '50%'],
                        data: seriesData
                    }
                ]
            };
        }
    });
</script>
