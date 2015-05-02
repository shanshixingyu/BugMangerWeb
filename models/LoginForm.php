<?php
namespace app\models;

use app\controllers\BaseController;
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

    public $user = false;

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
            ['password', 'validateUser'],
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

    /**
     * 验证用户信息
     * @param $attribute
     * @param $params
     */
    public function  validateUser($attribute, $params)
    {
        //先对登录密码信息进行加密,好和数据库比对
        $encryptedPassword = BaseController::getEncryptedPassword($this->password);
        $this->user = User::findOne(['name' => $this->username, 'password' => $encryptedPassword]);
        if ($this->user === null) {
            $this->addError($attribute, '用户不存在或者密码不正确');
        }
    }

    /**
     * 用户登录操作
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * 找到当前用户
     * @return User|null
     */
    private function getUser()
    {
        if ($this->user === false) {
            $this->user = User::findOne(['name' => $this->username, 'password' => $this->password]);
        }
        return $this->user;
    }

}