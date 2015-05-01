<?php
/**
 *
 * Created by GuLang on 2015-04-29.
 */

namespace app\models;

use yii\base\Exception;

class UserForm extends BaseForm
{
    public $id;
    public $name;
    public $roleId;
    public $email;

    public $isModify = false;/* 默认不是修改 */

    public function rules()
    {
        return [
            ['name', 'required', 'message' => '用户名称必填'],
            ['name', 'validateNameUnique'],
            ['roleId', 'validateRoleExist'],
            ['email', 'email', 'message' => '用户邮箱不合法'],
        ];

    }

    /**
     * 验证用户名称是否已经存在，如果存在的话，验证抛出错误
     * 注意：当用户在修改的时候，如果提交的用户名字和修改之前的名字相同，即为不修改名称，视为验证通过
     * @param $attribute
     * @param $param
     */
    public function validateNameUnique($attribute, $param)
    {
        $user = User::findOne(['name' => $this->name]);
        if ($user !== null)
            /* 判断条件 !($this->isModify && $user->id == $this->id) */
            if (!$this->isModify || $user->id != $this->id)
                $this->addError($attribute, '指定名称的用户已存在');
    }


    /**
     * 验证指定的用户角色是否存在，如果不存在的话，验证抛出错误
     * @param $attribute
     * @param $param
     */
    public function validateRoleExist($attribute, $param)
    {
        $role = Role::findOne(['id' => $this->roleId]);
        if ($role === null) {
            $this->addError($attribute, '用户角色信息已经过期');
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => '用户名称',
            'roleId' => '用户角色',
            'email' => '用户邮箱',
        ];
    }


    public function addUserToDb()
    {
        $user = new User();
        $user->name = $this->name;
        $user->role_id = $this->roleId;
        $user->email = $this->email;
        $user->creator = 2;//这个在后期还需要更改
        date_default_timezone_set('Asia/Shanghai');
        $user->create_time = date('Y-m-d H:i:s', time());
        $user->setIsNewRecord(true);
        $result = false;
        try {
            $result = $user->save();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function modifyUserOfDb()
    {
        $result = false;
        try {
            $result = User::updateAll([
                    'name' => $this->name,
                    'role_id' => $this->roleId,
                    'email' => $this->email,
                ], ['id' => $this->id]) > 0;
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }


}