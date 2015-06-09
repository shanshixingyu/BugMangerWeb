<?php
/**
 * 项目信息模型
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\Json;


class Project extends ActiveRecord
{
    public $bugStatusArr;

    public function init()
    {
        parent::init();
        $this->bugStatusArr = Json::decode(BUG_STATUS);
    }


    public static function tableName()
    {
        return 'project';
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator']);
    }

    public function getProjectModules()
    {
        return $this->hasMany(Module::className(), ['project_id' => 'id']);
    }

    /**
     * 获得与本项目相关的bug数量
     * @return int|string 项目的bug数量
     */
    public function getBugCount()
    {
        return Bug::find()->where(['project_id' => $this->id])->count();
    }

    /**
     * 获得当前项目中某种状态下的bug数量
     * @param string $status 状态字符串
     * @return int|string 本项目中指定状态的bug数量
     * @throws Exception 如果传入的状态变量为null，或者不存在的状态，抛出异常
     */
    public function getStatusBugCount($status)
    {
        if ($status !== null && in_array($status, $this->bugStatusArr)) {
            return Bug::find()->where([
                'project_id' => $this->id,
                'status' => array_search($status, $this->bugStatusArr)
            ])->count();
        } else {
            throw new Exception("传入的bug状态为null或者不存在");
        }
    }

    /**
     * 获得负责本项目的团队成员信息
     * @param string|array|null $selectColumn 选中的字段，用户必须保证字段是正确的
     * @return array|User[] 用户信息列表，没有的时候为[]
     * @throws Exception 之前没有将负责团队id查询出来
     */
    public function getGroupMember($selectColumn = null)
    {
        /* 防止没有查询出该项的情况 */
        if (isset($this->group_id)) {
            $group = Group::find(['member'])->where(['id' => $this->group_id])->one();
            if ($group !== null) {
                $memberId = Json::decode($group->member);
                if ($selectColumn === null)
                    return User::find()->where(['id' => $memberId])->all();
                else
                    return User::find()->select($selectColumn)->where(['id' => $memberId])->all();
            } else {
                return [];
            }
        } else {
            throw new Exception('没有查询出负责团队信息字段');
        }
    }

    /**
     * 获得本项目的所有模块信息
     * @return array|Module[]
     * @throws Exception 当没查询出项目id的时候抛出异常
     */
    public function getModules()
    {
        /* 防止没有查询出该项的情况 */
        if (isset($this->id)) {
            return Module::find()->where(['project_id' => $this->id])->all();
        } else {
            throw new Exception('没有查询出负责团队信息');
        }
    }

}