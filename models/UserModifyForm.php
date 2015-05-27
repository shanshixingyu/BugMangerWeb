<?php
/**
 * 用户修改个人信息的表单
 * Created by GuLang on 2015-04-20.
 */
namespace app\models;


use app\controllers\BaseController;
use app\tools\PasswordUtils;

class UserModifyForm extends BaseForm
{
    public $userId;
    public $userName;
    public $oldPassword;
    public $password;
    public $password2;
    public $roleName;
    public $email;

    public $modifyPassword = false;

    public function rules()
    {
        if ($this->modifyPassword) {
            return [
                ['oldPassword', 'required', 'message' => '原始密码必填'],
                ['password', 'required', 'message' => '新密码必填'],
                ['email', 'required', 'message' => '邮箱必填'],
                ['email', 'email', 'message' => '邮箱格式不正确'],
                ['password2', 'compare', 'compareAttribute' => 'password', 'message' => '密码不一致'],
                ['oldPassword', 'validateOldCorrect'],
            ];
        } else {
            return [
                ['email', 'required', 'message' => '邮箱必填'],
                ['email', 'email', 'message' => '邮箱格式不正确'],
            ];
        }
    }

    public function validateOldCorrect($attribute, $param)
    {
        $user = User::find()->where(['id' => $this->userId, 'password' => PasswordUtils::getEncryptedPassword($this->oldPassword)])->one();
        if ($user === null) {
            $this->addError($attribute, '原密码不正确');
        }
    }

    public function attributeLabels()
    {
        return [
            'userId' => '用户ID',
            'userName' => '账号',
            'oldPassword' => '原密码',
            'password' => '新密码',
            'password2' => '确认密码',
            'roleName' => '身份',
            'email' => '邮箱',
        ];
    }

    public function loadData()
    {
        $parentResult = parent::loadData();
        /* 只要原密码、新密码、确认密码中有一个有输入表示需要修改密码 */
        if ($this->oldPassword != '' || $this->password != '' || $this->password2 != '')
            $this->modifyPassword = true;
        else
            $this->modifyPassword = false;

        return $parentResult;
    }

    /**
     * 修改个人信息
     */
    public function modifyPimOfDb()
    {
        if ($this->modifyPassword) {
            $changedItem = ['password' => PasswordUtils::getEncryptedPassword($this->password), 'email' => $this->email];
        } else {
            $changedItem = ['email' => $this->email];
        }
        $rowCount = User::updateAll($changedItem, 'id=:userId', [':userId' => $this->userId]);
        return ($rowCount > 0);
    }


}