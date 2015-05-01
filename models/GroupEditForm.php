<?php
/**
 * 团队编辑模型
 * Created by GuLang on 2015-05-01.
 */

namespace app\models;


class GroupEditForm extends BaseForm
{
    public $id;
    public $name;
    public $creator;
    public $create_time;
    public $member;
    public $introduce;

    public $isModify = false;

    public function rules()
    {
        return [
            ['name', 'required', 'message' => '团队名称必填'],
            ['name', 'validateNameUnique'],
            ['member', 'required', 'message' => '团队成员不能为空'],
            ['member', 'validateMemberExist'],
        ];
    }

    /***
     * 验证团队名字的唯一性
     * @param $attribute
     * @param $param
     */
    public function validateNameUnique($attribute, $param)
    {
        $groupDetail = GroupDetail::find()->where(['name' => $this->name])->one();
        if ($groupDetail != null) {
            $this->addError($attribute, "团队名称已存在");
        }
    }

    /**
     * 验证团队成员是否存在，只要有一个不存在的，直接验证不通过，错误信息为：“供选团队成员信息过期，请刷新重试”
     * @param $attribute
     * @param $param
     */
    public function validateMemberExist($attribute, $param)
    {
        if (isset($this->member) && is_array($this->member)) {
            $users = User::find()->where(['id' => $this->member])->all();
            if (count($users) != count($this->member)) {
                $this->addError($attribute, '供选团队成员信息过期，请刷新重试');
            }
        } else {
            $this->addError("团队成员不能为空");
        }

    }

    public function attributeLabels()
    {
        return [
            'name' => '团队名称',
            'member' => '团队成员',
            'introduce' => '团队简介',
        ];
    }

    /**
     * 将新团队信息写入数据库
     * 首先，新增团队，然后获得团队id，然后再user_group表中增加团队成员
     */
    public function addGroupToDb()
    {

    }

}