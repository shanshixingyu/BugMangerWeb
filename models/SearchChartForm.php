<?php
/**
 * Bug情况表格的搜索的各种搜索条件
 * Created by GuLang on 2015-05-08.
 */

namespace app\models;


class SearchChartForm extends BaseForm
{
    public $type;
    public $startDate;
    public $endDate;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $now = time();
        $today = date('Y-m-d', $now);
        $defaultDay = date('Y-m-d', strtotime('-7day', $now));
        $this->startDate = $defaultDay;
        $this->endDate = $today;
    }


    public function rules()
    {
        return [
            ['type', 'required', 'message' => '图表类型必选'],
            ['startDate', 'required', 'message' => '开始时间必填'],
            ['endDate', 'required', 'message' => '结束时间必填'],
            ['startDate', 'date', 'format' => 'YY-mm-dd', 'message' => '开始时间格式不合法'],
            ['endDate', 'date', 'format' => 'YY-mm-dd', 'message' => '结束时间格式不合法'],
            ['endDate', 'validateTime', 'message' => '结束时间格式不合法'],
        ];
    }

    public function validateTime($attribute, $param)
    {
        if (strtotime($this->startDate) > strtotime($this->endDate))
            $this->addError($attribute, '查询开始时间比结束时间大');
    }

    public function attributeLabels()
    {
        return [
            'type' => '图表类型',
            'startDate' => '日期',
            'endDate' => '-',
        ];
    }


}