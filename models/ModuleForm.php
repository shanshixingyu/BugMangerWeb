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
    public $name;
    public $productId;
    public $fuzeren;
    public $introduce;

    public function rules()
    {
        return [
            ['name', 'required', 'message' => '模块名称必填'],
            ['productId', 'required', 'message' => '产品名称必选'],
            ['productId', 'validateProductExist'],
            ['name', 'validateNameUnique'],/* 同一个产品下的模块名称必须唯一*/
            ['fuzeren', 'required', 'message' => '模块负责人必选'],
            ['fuzeren', 'validateFuZeRenExist'],
        ];
    }

    /**
     * 验证同一个产品下的名称是否唯一
     * @param $attribute
     * @param $params
     */
    public function validateNameUnique($attribute, $params)
    {
        $productModule = ProductModule::findOne(['name' => $this->name, 'product_id' => $this->productId]);
        if ($productModule !== null)
            $this->addError($attribute, "指定产品下该模块已存在");
    }

    /**
     * 验证产品是否存在
     * @param $attribute
     * @param $params
     */
    public function validateProductExist($attribute, $params)
    {
        $product = Product::findOne(['id' => $this->productId]);
        if ($product === null)
            $this->addError($attribute, '产品名称信息过期，请刷新重选');
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
            'productId' => '产品列表',
            'name' => '模块名称',
            'fuzeren' => '负责人',
            'introduce' => '模块简介',
        ];
    }

    public function addModuleToDb()
    {
        $productModule = new ProductModule();
        $productModule->name = $this->name;
        $productModule->product_id = $this->productId;
        $productModule->fuzeren = Json::encode($this->fuzeren);
        $productModule->creator = 2; //后期优化***************
        date_default_timezone_set('Asia/Shanghai');
        $productModule->create_time = date('Y-m-d H:i:s', time());
        $productModule->introduce = $this->introduce;
        $productModule->setIsNewRecord(true);
        $result = null;
        try {
            $result = $productModule->save();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }


}