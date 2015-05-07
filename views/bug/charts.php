<?php
/**
 * 生成报表的界面
 * Created by GuLang on 2015-05-07.
 */
/* @var $this yii\web\View */


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
        <label>测试
            <input type="text" name="test_input" id="testInput1" placeholder="开始时间" class="laydate-icon"/>
            <input type="text" name="test_input" id="testInput2" placeholder="结束时间" class="laydate-icon"/>
        </label>
    </div>

    <div id="chartContent">
        <div style="height: 400px;" id="testChart"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#testInput1').click(function () {
            laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'});
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
            ],
            function (ec) {
                //--- 折柱 ---
                var myChart = ec.init(document.getElementById('testChart'));
                myChart.setOption({
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['蒸发量', '降水量']
                    },
                    toolbox: {
                        show: true,
                        feature: {
//                            mark: {show: true},
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
                            data: ['2015年1月', '2015年2月', '2015年3月', '2015年4月', '2015年5月', '2015年6月', '2015年7月', '2015年8月', '2015年9月', '10月', '11月', '12月']
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
                            name: '蒸发量',
                            type: 'bar',
                            data: [2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3]
                        },
                        {
                            name: '降水量',
                            type: 'bar',
                            data: [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
                        }
                    ]
                });
            }
        );
    });
</script>
