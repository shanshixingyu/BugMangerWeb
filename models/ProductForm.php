<?php
/**
 *
 * Created by GuLang on 2015-04-23.
 */

namespace app\models;

use Yii;
use yii\base\Exception;

class ProductForm extends BaseForm
{
    public $id;
    public $isModify = false;
    public $name;
    public $groupId;
    public $introduce;

    public function rules()
    {
        return [
            ['name', 'required', 'message' => '产品名称必填'],
            ['name', 'validateNameUnique'],
            ['groupId', 'validateGroupExist'],//验证用户组是否还存在
        ];
    }

    /**
     * 验证产品名称是否唯一
     * @param $attribute
     * @param $params
     */
    public function validateNameUnique($attribute, $params)
    {
        $product = Product::findOne(['name' => $this->name]);
        if ($product !== null) {
            /* 验证唯一性的时候，如果是修改，且修改的名字没改变的话
                (即模块id与name同时和查询出来的记录相同)，允许验证通过 */
            if ($this->isModify && $product->id == $this->id) {

            } else {
                $this->addError($attribute, '产品名称已经存在');
            }
        }

    }

    /**
     * 验证负责用户组是否存在
     * @param $attribute
     * @param $params
     */
    public function validateGroupExist($attribute, $params)
    {
        $groupDetail = Group::findOne(['id' => $this->groupId]);
        if ($groupDetail === null) {
            $this->addError('负责团队数据过时，请刷新重试');
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => '产品名称',
            'groupId' => '负责团队',
            'introduce' => '产品简介',
        ];
    }

    /**
     * 添加产品到数据库中
     * @return bool 当添加成功则返回true，没有添加成功则返回false，
     */
    public function addProductToDb()
    {
        $product = new Product();
        $product->name = $this->name;
        $product->group_id = $this->groupId;
        $product->creator = 2;//将在后期使用：Yii::$app->user->id替代;
        date_default_timezone_set('Asia/Shanghai');
        $product->create_time = date('Y-m-d H:i:s', time());
        $product->introduce = $this->introduce;
        $product->setIsNewRecord(true);
        $result = null;
        try {
            $result = $product->save();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * 修改数据库中的产品信息
     * @return bool 当修改成功则返回true，没有修改成功则返回false
     */
    public function modifyProductOfDb()
    {
        return Product::updateAll([
            'name' => $this->name,
            'group_id' => $this->groupId,
            'introduce' => $this->introduce
        ], ['id' => isset($this->id) ? $this->id : 0]) > 0;//使用0是因为没有这项
    }


}