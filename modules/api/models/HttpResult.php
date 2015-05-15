<?php
/**
 * 访问结果模型类
 * Created by GuLang on 2015-05-12.
 */

namespace app\modules\api\models;


use yii\helpers\Json;

class HttpResult
{
    public $code = 0;
    public $message = '';
    public $result = '';

    /**
     *  处理返回本结果类中的数据称Json字符串
     * 返回结果的基本JSON格式:
     *      {
     *          "code":"",          //0表示成功，其它表示失败
     *          "message":"",       //提示消息
     *          "result":""         //表示返回结果,其中result里面的内容可以是任何自定义的json字符串
     *       }
     */
    public function parseJson()
    {
        return Json::encode($this);
    }
}