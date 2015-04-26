<?php
/**
 * 用户修改个人信息的表单
 * Created by GuLang on 2015-04-20.
 */
namespace app\models;


class UserModifyForm extends BaseForm
{
    public $userId;
    public $userName;
    public $password;
    public $password2;
    public $roleName;
    public $email;

    public function rules()
    {
        return [
            ['password', 'required', 'message' => '密码必填'],
            ['email', 'email', 'message' => '格式不正确'],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message' => '密码不一致'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'userId' => '用户ID',
            'userName' => '账号',
            'password' => '新密码',
            'password2' => '确认密码',
            'roleName' => '身份',
            'email' => '邮箱',
        ];
    }
}