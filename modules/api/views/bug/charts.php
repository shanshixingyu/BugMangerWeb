<?php
/**
 * 手机端图表
 * Created by GuLang on 2015-06-06.
 */
use yii\helpers\Html;
use app\tools\MyConstant;
use yii\helpers\Json;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?php echo CSS_PATH; ?>charts.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo ASSETS_PATH; ?>10b978a4/jquery.js" type="text/javascript"></script>
    <script src="<?php echo JS_PATH; ?>echarts/echarts.js" type="text/javascript"></script>
    <style type="text/css">
        #bugChart0,
        #bugChart1,
        #bugChart2,
        #bugChart3,
        #bugChart4,
        #bugChart5,
        #bugChart6 {
            width: 100%;
            height: 300px;
            margin-top: 20px;
            margin-bottom: 50px;
            /*background-color: #ff0000;*/
        }
    </style>
</head>
<body>
<div id="bugChart0"></div>
<div id="bugChart1"></div>
<div id="bugChart2"></div>
<div id="bugChart3"></div>
<div id="bugChart4"></div>
<div id="bugChart5"></div>
<div id="bugChart6"></div>

<script type="text/javascript">
    $(document).ready(function () {
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
            drawCharts
        )
        ;

        function drawCharts(ec) {
            <?php if(isset($data)&&is_array($data)):?>
            <?php foreach($data as $index=>$one):?>
            var myChartElement = ec.init(document.getElementById('bugChart<?php echo $index;?>'));
            <?php
                $isPie=false;
                $chartTitle='';
                $seriesName='';
                switch($one['type']){
                    case MyConstant::ECHART_TYPE_SUBMIT_DAY:
                        $isPie=false;
                        $chartTitle='项目Bug提交情况（天）';
                        break;
                    case MyConstant::ECHART_TYPE_SUBMIT_MONTH:
                        $isPie=false;
                        $chartTitle='项目Bug提交情况（月）';
                        break;
                    case MyConstant::ECHART_TYPE_TOTAL_DAY:
                        $isPie=false;
                        $chartTitle='项目Bug总量走势（天）';
                        break;
                    case MyConstant::ECHART_TYPE_TOTAL_MONTH:
                        $isPie=false;
                        $chartTitle='项目Bug总量走势（月）';
                        break;
                    case MyConstant::ECHART_TYPE_MODULE:
                        $isPie=true;
                        $chartTitle='项目各模块Bug比例';
                        $seriesName='项目模块';
                        break;
                    case MyConstant::ECHART_TYPE_PRIORITY:
                        $isPie=true;
                        $chartTitle='项目Bug优先级比例';
                        $seriesName='优先级';
                        break;
                    case MyConstant::ECHART_TYPE_STATUS:
                        $isPie=true;
                        $chartTitle='项目Bug状态比例';
                        $seriesName='Bug状态';
                        break;
                }?>
            <?php if($isPie):?>
            myChartElement.setOption(getPieChartOption(
                '<?php echo $chartTitle;?>',
                <?php echo Json::encode(array_keys(['data']));?>, '<?php echo $seriesName;?>',
                [<?php foreach($one['data'] as $key=>$value):?>
                    {value:<?php echo $value;?>, name: '<?php echo $key;?>'},
                    <?php endforeach;?>]
            ));
            <?php else:?>
            myChartElement.setOption(getCommonChartOption(
                '<?php echo $chartTitle;?>', ['提交Bug数量'],
                <?php echo Json::encode(array_keys($one['data']));?>, '提交Bug数量',
                <?php echo Json::encode(array_values($one['data']))?>
            ));
            <?php endif;?>
            <?php endforeach;?>
            <?php endif;?>
        }


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
    })
    ;
</script>
</body>
</html>
