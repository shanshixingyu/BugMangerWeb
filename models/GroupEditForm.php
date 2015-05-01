<?php
/**
 * 团队编辑模型
 * Created by GuLang on 2015-05-01.
 */

namespace app\models;


use yii\helpers\Json;
use yii\base\Exception;

class GroupEditForm extends BaseForm
{
    public $id;
    public $name;
    public $creator;
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
     * 注意：当修改的时候，如果存在相同的并且id相同，则说明name没有发生改变
     * @param $attribute
     * @param $param
     */
    public function validateNameUnique($attribute, $param)
    {
        $group = Group::find()->where(['name' => $this->name])->one();
        if ($group != null) {
            if ($this->isModify && $this->id == $group->id) {

            } else {
                $this->addError($attribute, "团队名称已存在");
            }
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
        $group = new Group();
        $group->name = $this->name;
        $group->member = Json::encode($this->member);
        $group->introduce = $this->introduce;
        $group->creator = $this->creator;
        date_default_timezone_set('Asia/Shanghai');
        $group->create_time = date('Y-m-d H:i:s', time());
        $group->setIsNewRecord(true);
        $result = false;
        try {
            $result = $group->save();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * 修改团队信息
     */
    public function modifyGroupOfDb()
    {
        $result = false;
        try {
            $result = Group::updateAll([
                    'name' => $this->name,
                    'member' => Json::encode($this->member),
                    'introduce' => $this->introduce,
                ], ['id' => $this->id]) > 0;
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }


}