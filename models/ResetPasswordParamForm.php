<?php
/**
 *
 * Created by GuLang on 2015-05-27.
 */

namespace app\models;


class ResetPasswordParamForm extends BaseForm
{
    public $userName;

    public function rules()
    {
        return [
            ['userName', 'required', 'message' => '用户名称必填'],
            ['userName', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', 'message' => '只能输入中文、英文、数字字符、下划线'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'userName' => '用户名称',
        ];
    }


}