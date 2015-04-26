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
    public $name;
    public $groupId;
    public $introduce;

    public function rules()
    {
        return [
            ['name', 'required', 'message' => '产品名称必填'],
            ['name', 'validateNameExist'],
            ['groupId', 'validateGroupExist'],//验证用户组是否还存在
        ];
    }

    public function validateNameExist($attribute, $params)
    {
        $product = Product::findOne(['name' => $this->name]);
        if ($product !== null)
            $this->addError($attribute, '产品名称已经存在');
    }

    public function validateGroupExist($attribute, $params)
    {
        $groupDetail = GroupDetail::findOne(['id' => $this->groupId]);
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
     * @return bool|array 当添加成功则返回true，没有添加成功则返回false，抛出异常则返回数组
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


}