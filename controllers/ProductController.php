<?php
/**
 *
 * Created by GuLang on 2015-04-26.
 */

namespace app\controllers;

use app\models\ProductModule;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\Product;
use app\models\ProductForm;
use app\models\GroupDetail;
use app\models\UserGroup;
use app\models\ModuleForm;
use app\controllers\action\GetGroupMemberAction;
use app\models\User;
use Yii;


class ProductController extends Controller
{
    public function actions()
    {
        return [
            'getGroupMember' => [
                'class' => GetGroupMemberAction::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->joinWith(['createUser', 'groupDetail']),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider,]);
    }

    public function actionAddProduct()
    {
        $productForm = new ProductForm();

        if (isset($_POST['ProductForm']) && $productForm->loadData() && $productForm->validate()) {
            $result = $productForm->addProductToDb();
            if ($result) {
                /* 数据插入成功 */
                return $this->refresh();
            } else if ($result) {
                /*返回的是错误信息*/
                $this->getView()->params[OPT_RESULT] = $result['error'];
            }
        }

        $groupDetails = GroupDetail::find()->select(['id', 'name'])->all();
        return $this->render('add_product', [
            'productForm' => $productForm,
            'groupDetails' => $groupDetails
        ]);
    }

    public function actionAddModule()
    {
        $moduleForm = new ModuleForm();
        if (isset($_POST['ModuleForm']) && $moduleForm->loadData() && $moduleForm->validate()) {
            $result = $moduleForm->addModuleToDb();
            if ($result) {
                /* 数据保存成功 */
                return $this->refresh();
            } else {
                /* 插入数据库失败 */
                $this->getView()->params[OPT_RESULT] = false;
            }
        } else {
            if (isset($this->getView()->params[OPT_RESULT]))
                unset($this->getView()->params[OPT_RESULT]);
        }

        /* 获得所有产品信息 */
        $products = Product::find()->select(['id', 'name', 'group_id'])->all();
        if (count($products) > 0)
            $groupMembers = UserGroup::find()->joinWith('user')->where(['group_id' => $products[0]->group_id])->all();
        else
            $groupMembers = new UserGroup();

        return $this->render('add_module', [
            'moduleForm' => $moduleForm,
            'products' => $products,
            'groupMembers' => $groupMembers,
        ]);
    }

    public function actionSeeModule($productId)
    {
        $productModules = ProductModule::find()->joinWith(['createUser'])->where(['product_id' => $productId])->all();

        foreach ($productModules as $productModule) {
            /* 对模块创建者信息进行二次处理 */
            if (isset($productModule->createUser) && isset($productModule->createUser->name)) {
                $productModule->creator = $productModule->createUser->name;
            } else {
                $productModule->creator = "";
            }
            /* 对负责人信息进行二次处理 */
            if (isset($productModule->fuzeren)) {
                $fzrs = Json::decode($productModule->fuzeren);
                if (is_array($fzrs)) {
                    $fzrUsers = User::find()->select('name')->where(['id' => $fzrs])->all();
                    $tempFzr = '';
                    foreach ($fzrUsers as $fzrUser) {
                        $tempFzr .= $fzrUser->name . ' , ';
                    }
                    $tempFzr = substr($tempFzr, 0, strlen($tempFzr) - 3);/*去掉最后的' , ' */
                    if ($tempFzr === false)/* 当数据库中出现‘[]’或者‘[""]’的时候截取字符串会返回false */
                        $tempFzr = '';
                    $productModule->fuzeren = $tempFzr;
                } else {
                    $productModule->fuzeren .= '';
                }
            } else {
                $productModule->fuzeren = '';
            }
        }

        return Json::encode($productModules);
    }

    public function actionModifyProduct($id)
    {
        return $id;
//        return $this->render();
    }

    public function actionModifyModule($id)
    {
        return $id;
//        return $this->render();
    }


    public function actionDeleteProduct($productId)
    {
        /* 删除产品的同时，也需要将产品的模块删除 */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            ProductModule::deleteAll(['product_id' => $productId]);
            Product::deleteAll(['id' => $productId]);
            $transaction->commit();
            echo 'success';
        } catch (Exception $e) {
            $transaction->rollBack();
            echo 'failure';
        }
    }

}