<?php
/**
 *
 * Created by GuLang on 2015-04-24.
 */

namespace app\models;


use yii\base\Exception;
use yii\helpers\Json;

class ModuleForm extends BaseForm
{
    public $id;/* 主要是在修改模块信息的时候有用 */
    public $name;
    public $projectId;
    public $projectName;/* 主要是在修改模块信息的时候有用 */
    public $fuzeren;
    public $introduce;

    public $isModify = false;

    public function rules()
    {
        $tempRules = [
            ['name', 'required', 'message' => '模块名称必填'],
            ['name', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', 'message' => '只能输入中文、英文、数字字符、下划线'],
            ['projectId', 'validateProjectExist'],
            ['name', 'validateNameUnique'],/* 同一个项目下的模块名称必须唯一*/
            ['fuzeren', 'required', 'message' => '模块负责人必选'],
            ['fuzeren', 'validateFuZeRenExist'],
        ];
        if ($this->isModify) {
            $tempRules[] = ['name', 'validateModuleExist'];/* 修改的时候验证模块是否存在 */
        } else {
            $tempRules[] = ['projectId', 'required', 'message' => '项目名称必选'];
        }
        return $tempRules;
    }

    public function validateModuleExist($attribute, $params)
    {
        if ($this->isModify && !isset($this->id))
            $this->addError('模块不存在');
    }

    /**
     * 验证同一个项目下的名称的唯一性
     * @param $attribute
     * @param $params
     */
    public function validateNameUnique($attribute, $params)
    {
        $projectModule = Module::findOne(['name' => $this->name, 'project_id' => $this->projectId]);
        if ($projectModule !== null) {
            /* 验证唯一性的时候，如果是修改，且修改的名字没改变的话
                                 (即模块id与name同时和查询出来的记录相同)，允许验证通过 */
            if ($this->isModify && $this->id == $projectModule->id) {

            } else {
                $this->addError($attribute, "指定项目下该模块已存在");
            }
        }

    }

    /**
     * 验证项目是否存在
     * @param $attribute
     * @param $params
     */
    public function validateProjectExist($attribute, $params)
    {
        $project = Project::findOne(['id' => $this->projectId]);
        if ($project === null)
            $this->addError($attribute, '项目名称信息过期，请刷新重选');
    }

    public function validateFuZeRenExist($attribute, $params)
    {
        if (isset($this->fuzeren) && is_array($this->fuzeren)) {
            $breakFlag = false;
            foreach ($this->fuzeren as $userId) {
                $user = User::findOne(['id' => $userId]);
                if ($user === null) {
                    $breakFlag = true;
                    break;
                }
                $user = null;
            }
            if ($breakFlag)
                $this->addError($attribute, '模块负责人数据过时,请重试');
        } else {
            $this->addError($attribute, '模块负责人必选');
        }
    }

    public function attributeLabels()
    {
        return [
            'projectId' => '项目名称',
            'projectName' => '项目名称',
            'id' => '模块选择',
            'name' => '模块名称',
            'fuzeren' => '负责人',
            'introduce' => '模块简介',
        ];
    }

    public function addModuleToDb()
    {
        $projectModule = new Module();
        $projectModule->name = $this->name;
        $projectModule->project_id = $this->projectId;
        $projectModule->fuzeren = Json::encode($this->fuzeren);
        $projectModule->creator = \Yii::$app->user->identity->getId();
        date_default_timezone_set('Asia/Shanghai');
        $projectModule->create_time = date('Y-m-d H:i:s', time());
        $projectModule->introduce = $this->introduce;
        $projectModule->setIsNewRecord(true);
        $result = null;
        try {
            $result = $projectModule->save();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function modifyModuleOfDb()
    {
//        var_dump($this);
        return Module::updateAll([
            'name' => $this->name,
            'fuzeren' => Json::encode($this->fuzeren),
            'introduce' => $this->introduce,
        ], ['id' => isset($this->id) ? $this->id : 0]) > 0;
    }


}