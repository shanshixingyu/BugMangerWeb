<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 *  登录模型类
 * Created by GuLang on 2015-04-16.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $verifyCode;
    public $rememberMe = false;

    /**
     * 登录表单中服务器端验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'required', 'message' => '账号必填'],
            ['password', 'required', 'message' => '密码必填'],
            ['verifyCode', 'captcha', 'message' => '验证码不正确'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * 登录界面表单中各个输入框对应的中文信息
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => '账 号：',
            'password' => '密 码：',
            'verifyCode' => '验证码：',
            'rememberMe' => '记住我',
        ];
    }

    public function login()
    {
        if ($this->validate()) {
//            return Yii::$app->user->login(null, $this->rememberMe ? 3600 * 24 * 30 : 0);
            return true;
        } else {
            return false;
        }
    }

}