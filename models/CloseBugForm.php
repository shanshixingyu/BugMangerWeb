<?php
/**
 * 关闭Bug
 * Created by GuLang on 2015-05-07.
 */

namespace app\models;


use app\tools\BugOpt;
use yii\base\Exception;
use yii\helpers\Json;

class CloseBugForm extends BaseForm
{
    public $bugId;
    public $reason;

    public function rules()
    {
        return [
            ['reason', 'required', 'message' => '关闭原因必填'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'reason' => '关闭原因',
        ];
    }

    /**
     * 关闭Bug
     * @return bool|int
     * @throws Exception
     * @throws \Exception
     */
    public function modifyBugOfDb()
    {
        if (!isset($this->bugId))
            throw new Exception('关闭方法中必须将Bug的Id设置好');
        $bug = Bug::find()->where(['id' => $this->bugId])->one();
        if ($bug === null)
            return false;

        //主要是更改状态为关闭,introduce添加记录
        $bugStatus = Json::decode(BUG_STATUS);
        $bug->status = array_search(BUG_STATUS_CLOSED, $bugStatus);

        date_default_timezone_set('Asia/Shanghai');
        $tempTime = date('Y-m-d H:i:s', time());
        $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $this->reason, '激活', $tempTime);

        $result = false;
        try {
            $result = $bug->update();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

}