<?php
/**
 *
 * Created by GuLang on 2015-05-06.
 */

namespace app\models;


use app\tools\BugOpt;
use yii\base\Exception;
use yii\helpers\Json;

class ResolveForm extends BaseForm
{
    public $bugId;
    public $type = 0;
    public $introduce;

    public function rules()
    {
        return [
            ['type', 'required', 'message' => '解决类型必选'],
            ['introduce', 'required', 'message' => '解决注释必填'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => '选择类型',
            'introduce' => '解决注释',
        ];
    }

    public function modifyBugOfDb()
    {
        $bugStatus = Json::decode(BUG_STATUS);

        /* 需改变：1、状态(改成已解决或者其它)；  2、introduce*/
        $bug = Bug::find()->where(['id' => $this->bugId])->one();
        if ($bug !== null) {
            try {
                $bug->resolve_id = \Yii::$app->user->identity->getId();
                date_default_timezone_set('Asia/Shanghai');
                $dateTime = date('Y-m-d H:i:s', time());
                $bug->resolve_id = $dateTime;
                if ($this->type == 0) {
                    $bug->status = array_search(BUG_STATUS_SOLVED, $bugStatus);
                    $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $this->introduce, '解决', $dateTime);
                } else {
                    $bug->status = array_search(BUG_STATUS_OTHER, $bugStatus);
                    $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $this->introduce, '改成其他状态', $dateTime);
                }
                $result = $bug->save();
            } catch (Exception $e) {
                $result = false;
            }
            return $result;
        } else {
            return false;
        }

    }

}