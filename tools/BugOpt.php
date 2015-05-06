<?php
/**
 *
 * Created by GuLang on 2015-05-06.
 */

namespace app\tools;


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
                'title' => $datetime . ' 新建 by ' . \Yii::$app->user->identity->name,
                'content' => $introduce
            ]
        ];
        return Json::encode($newIntroduce);
    }

    /**
     * 给bug添加introduce信息
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
        $tempIntroduce = Json::decode($oldIntroduce);
        $tempIntroduce[] = [
            'title' => $dateTime . ' ' . $type . ' by ' . \Yii::$app->user->identity->name,
            'content' => $introduce
        ];
        return Json::encode($tempIntroduce);
    }
}