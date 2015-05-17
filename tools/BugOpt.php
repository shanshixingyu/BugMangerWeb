<?php
/**
 *
 * Created by GuLang on 2015-05-06.
 */

namespace app\tools;

use Yii;
use app\models\Bug;
use app\models\Module;
use yii\helpers\Json;

class BugOpt
{
    /**
     * 处理新建bug的消息
     * @param $introduce
     * @param $datetime
     * @return string
     */
    public static function bugIntroduce($introduce, $datetime)
    {
        $newIntroduce = [
            [
                'type' => '新建',
                'name' => Yii::$app->user->identity->name,
                'time' => $datetime,
                'content' => $introduce
            ]
        ];
        return Json::encode($newIntroduce);
    }

    /**
     * 给bug添加introduce信息,新加入的元素放在最前面
     * @param $oldIntroduce
     * @param $introduce
     * @param string $type
     * @param null $dateTime
     * @return string
     */
    public static function addBugIntroduce($oldIntroduce, $introduce, $type = '解决', $dateTime = null)
    {
        if ($dateTime === null) {
            date_default_timezone_set('Asia/Shanghai');
            $dateTime = date('Y-m-d H:i:s', time());
        }
        $tempOldIntroduce = Json::decode($oldIntroduce);
        $tempIntroduce = [
            'type' => $type,
            'name' => Yii::$app->user->identity->name,
            'time' => $dateTime,
            'content' => $introduce
        ];
        array_unshift($tempOldIntroduce, $tempIntroduce);

        return Json::encode($tempOldIntroduce);
    }

    public static function getEchartFunction($choiceId)
    {
        $func = 'getProjectDayBugCounts';
        switch ($choiceId) {
            case 0:
                $func = 'getProjectDayBugCounts';
                break;
            case 1:
                $func = 'getProjectMonthBugCounts';
                break;
            case 2:
                $func = 'getProjectTotalBugDayCounts';
                break;
            case 3:
                $func = 'getProjectTotalBugMonthCounts';
                break;
            case 4:
                $func = 'getProjectModuleBugCounts';
                break;
            case 5:
                $func = 'getProjectPriorityBugCounts';
                break;
            case 6:
                $func = 'getProjectStatusBugCounts';
                break;
            default:
                $func = 'getProjectDayBugCounts';
        }
        return $func;
    }

    /**
     * 获得项目的Bug提交日期分布
     * @param $projectId
     * @param $startDay
     * @param $endDay
     * @return array
     */
    public static function getProjectDayBugCounts($projectId, $startDay, $endDay)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_SUBMIT_DAY, 'data' => []];
        $startTime = strtotime($startDay);
        $tempEndTime = strtotime($endDay);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的时间以及最迟提交bug的时间 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始时间
        if ($startTime < $earliestTime) {
            $startDay = date('Y-m-d', $earliestTime);
            $startTime = strtotime($startDay);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endDay = date('Y-m-d', $tempEndTime);
        $endTime = strtotime($endDay);
        unset($tempEndTime);

        /* between and 的结束时间必须增加一天 */
        $query = $query->andFilterWhere(['between', 'create_time', $startDay, date('Y-m-d', strtotime('+1day', $endTime))]);

        for ($i = 0; true; ++$i) {
            //计算此次循环的日期
            $tempDay = strtotime('+' . $i . 'day', $startTime);
            $key = date('Y-m-d', $tempDay);
            $tempQuery = clone $query;
            $result['data'][$key] = $tempQuery->andFilterWhere(['like', 'create_time', $key])->count();
            if ($tempDay >= $endTime) {
                break;
            }
        }
        return $result;
    }

    /**
     * 获得bug的提交日期分布（月），从最早提交日期到最迟的提交日期
     * @param $projectId
     * @param $startMonth
     * @param $endMonth
     * @return array
     */
    public static function getProjectMonthBugCounts($projectId, $startMonth, $endMonth)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_SUBMIT_MONTH, 'data' => []];
        $startTime = strtotime($startMonth);
        $tempEndTime = strtotime($endMonth);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的月份以及最迟提交bug的月份 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始月份
        if ($startTime < $earliestTime) {
            $startMonth = date('Y-m', $earliestTime);
            $startTime = strtotime($startMonth);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endMonth = date('Y-m', $tempEndTime);
        $endTime = strtotime($endMonth);
        unset($tempEndTime);

        /* between and 的结束时间必须增加一个月份 */
        $query = $query->andFilterWhere(['between', 'create_time', date('Y-m-d', strtotime($startMonth)), date('Y-m-d', strtotime(date('Y-m', strtotime('+1month', $endTime))))]);

        for ($i = 0; true; ++$i) {
            //计算此次循环的月份
            $tempMonth = strtotime('+' . $i . 'month', $startTime);
            $key = date('Y-m', $tempMonth);
            $tempQuery = clone $query;
            $result['data'][$key] = $tempQuery->andFilterWhere(['like', 'create_time', $key])->count();
            if ($tempMonth >= $endTime) {
                break;
            }
        }
        return $result;
    }

    /**
     * 获得bug的提交Bug总量走势(天)
     * @param $projectId
     * @param $startDay
     * @param $endDay
     * @return array
     */
    public static function getProjectTotalBugDayCounts($projectId, $startDay, $endDay)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_TOTAL_DAY, 'data' => []];
        $startTime = strtotime($startDay);
        $tempEndTime = strtotime($endDay);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的时间以及最迟提交bug的时间 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始时间
        if ($startTime < $earliestTime) {
            $startDay = date('Y-m-d', $earliestTime);
            $startTime = strtotime($startDay);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endDay = date('Y-m-d', $tempEndTime);
        $endTime = strtotime($endDay);
        unset($tempEndTime);

        /* between and 的结束时间必须增加一天 */
        $query = $query->andFilterWhere(['between', 'create_time', $startDay, date('Y-m-d', strtotime('+1day', $endTime))]);
        $countSum = 0;
        for ($i = 0; true; ++$i) {
            //计算此次循环的日期
            $tempDay = strtotime('+' . $i . 'day', $startTime);
            $key = date('Y-m-d', $tempDay);
            $tempQuery = clone $query;
            $countSum += $tempQuery->andFilterWhere(['like', 'create_time', $key])->count();
            $result['data'][$key] = $countSum;
            if ($tempDay >= $endTime) {
                break;
            }
        }
        return $result;
    }

    /**
     * 获得bug的提交Bug总量走势(月)
     * @param $projectId
     * @param $startMonth
     * @param $endMonth
     * @return array
     */
    public static function getProjectTotalBugMonthCounts($projectId, $startMonth, $endMonth)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_TOTAL_MONTH, 'data' => []];
        $startTime = strtotime($startMonth);
        $tempEndTime = strtotime($endMonth);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的月份以及最迟提交bug的月份 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始月份
        if ($startTime < $earliestTime) {
            $startMonth = date('Y-m', $earliestTime);
            $startTime = strtotime($startMonth);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endMonth = date('Y-m', $tempEndTime);
        $endTime = strtotime($endMonth);
        unset($tempEndTime);

        /* between and 的结束时间必须增加一个月份 */
        $query = $query->andFilterWhere(['between', 'create_time', date('Y-m-d', strtotime($startMonth)), date('Y-m-d', strtotime(date('Y-m', strtotime('+1month', $endTime))))]);
        $countSum = 0;
        for ($i = 0; true; ++$i) {
            //计算此次循环的月份
            $tempMonth = strtotime('+' . $i . 'month', $startTime);
            $key = date('Y-m', $tempMonth);
            $tempQuery = clone $query;
            $countSum += $tempQuery->andFilterWhere(['like', 'create_time', $key])->count();
            $result['data'][$key] = $countSum;
            if ($tempMonth >= $endTime) {
                break;
            }
        }
        return $result;
    }

    /**
     * 获得项目中各个模块的bug数目以及总bug数目
     * @param $projectId
     * @param $startDay
     * @param $endDay
     * @return array
     */
    public static function getProjectModuleBugCounts($projectId, $startDay, $endDay)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_MODULE, 'data' => []];

        $startTime = strtotime($startDay);
        $tempEndTime = strtotime($endDay);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的时间以及最迟提交bug的时间 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始时间
        if ($startTime < $earliestTime) {
            $startDay = date('Y-m-d', $earliestTime);
            $startTime = strtotime($startDay);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endDay = date('Y-m-d', $tempEndTime);
        $endTime = strtotime($endDay);
        unset($tempEndTime);

        $query = Bug::find()->where(['project_id' => $projectId])->andFilterWhere([
            'between', 'create_time', $startDay, date('Y-m-d', strtotime('+1day', $endTime))
        ]);
        //获取bug总数
        $result['totalCount'] = $query->count();
        if ($result['totalCount'] <= 0)
            return $result;

        $modules = Module::find()->select(['id', 'name'])->where(['project_id' => $projectId])->all();
        if (count($modules) <= 0) {
            $result['data']['other'] = $result['totalCount'];
            return $result;
        }

        //暂存项目中剩余还没获取的bug数目
        $leaveCount = $result['totalCount'];
        foreach ($modules as $module) {
            $tempQuery = clone $query;
            $count = $tempQuery->andWhere(['module_id' => $module->id])->count();
            $result['data'][$module->name] = $count;
            $leaveCount -= $count;
        }
        if ($leaveCount < 0)
            $leaveCount = 0;
        $result['data']['other'] = $leaveCount;

        return $result;
    }

    /**
     * 获得项目中各个优先级的bug数目以及总bug数目
     * @param $projectId
     * @param $startDay
     * @param $endDay
     * @return array
     */
    public static function getProjectPriorityBugCounts($projectId, $startDay, $endDay)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_PRIORITY, 'data' => []];

        $startTime = strtotime($startDay);
        $tempEndTime = strtotime($endDay);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的时间以及最迟提交bug的时间 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始时间
        if ($startTime < $earliestTime) {
            $startDay = date('Y-m-d', $earliestTime);
            $startTime = strtotime($startDay);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endDay = date('Y-m-d', $tempEndTime);
        $endTime = strtotime($endDay);
        unset($tempEndTime);

        $query = Bug::find()->where(['project_id' => $projectId])->andFilterWhere([
            'between', 'create_time', $startDay, date('Y-m-d', strtotime('+1day', $endTime))
        ]);
        //获取bug总数
        $result['totalCount'] = $query->count();
        if ($result['totalCount'] <= 0)
            return $result;

        $priorityArr = Json::decode(BUG_PRIORITY);

        //暂存项目中剩余还没获取的bug数目
        foreach ($priorityArr as $key => $priorityName) {
            $tempQuery = clone $query;
            $count = $tempQuery->andWhere(['priority' => $key])->count();
            $result['data'][$priorityName] = $count;
        }

        return $result;
    }

    /**
     * 获得项目中各个状态的bug数目以及总bug数目
     * @param $projectId
     * @param $startDay
     * @param $endDay
     * @return array
     */
    public static function getProjectStatusBugCounts($projectId, $startDay, $endDay)
    {
        $result = ['type' => MyConstant::ECHART_TYPE_STATUS, 'data' => []];

        $startTime = strtotime($startDay);
        $tempEndTime = strtotime($endDay);
        if ($startTime > $tempEndTime)
            return $result;

        $query = Bug::find()->where(['project_id' => $projectId]);
        if (!$query->exists())
            return $result;

        /* 获得项目最早提交的bug的时间以及最迟提交bug的时间 */
        $earliestTime = strtotime($query->min('create_time'));
        $lastTime = strtotime($query->max('create_time'));
        //判断查询开始时间是否在最早之前,如果是，将最早时间格式化成正确的开始时间
        if ($startTime < $earliestTime) {
            $startDay = date('Y-m-d', $earliestTime);
            $startTime = strtotime($startDay);
        }
        //判断查询结束时间是否在最迟之后，如果是，将最迟时间格式化成正确结束时间
        if ($lastTime < $tempEndTime) {
            $tempEndTime = $lastTime;
        }
        $endDay = date('Y-m-d', $tempEndTime);
        $endTime = strtotime($endDay);
        unset($tempEndTime);

        $query = Bug::find()->where(['project_id' => $projectId])->andFilterWhere([
            'between', 'create_time', $startDay, date('Y-m-d', strtotime('+1day', $endTime))
        ]);
        //获取bug总数
        $result['totalCount'] = $query->count();
        if ($result['totalCount'] <= 0)
            return $result;

        $statusArr = Json::decode(BUG_STATUS);

        //暂存项目中剩余还没获取的bug数目
        foreach ($statusArr as $key => $statusName) {
            $tempQuery = clone $query;
            $count = $tempQuery->andWhere(['status' => $key])->count();
            $result['data'][$statusName] = $count;
        }

        return $result;
    }


}