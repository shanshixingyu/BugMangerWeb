<?php
/**
 *
 * Created by GuLang on 2015-04-26.
 */

namespace app\controllers;

use app\models\Module;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\Product;
use app\models\ProductForm;
use app\models\Group;
use app\models\ModuleForm;
use app\models\User;
use Yii;


class ProductController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->joinWith(['createUser', 'group']),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider,]);
    }

    public function actionSeeModule($productId)
    {
        $productModules = Module::find()->joinWith(['createUser'])->where(['product_id' => $productId])->all();

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

    public function actionAddProduct()
    {
        $productForm = new ProductForm();

        if (isset($_POST['ProductForm']) && $productForm->loadData() && $productForm->validate()) {
            $result = $productForm->addProductToDb();
            if ($result) {
                /* 数据修改成功 */
                Yii::$app->session->setFlash(OPT_RESULT, '产品信息添加成功！');
                return $this->refresh();
            } else {
                /* 数据修改失败 */
                Yii::$app->session->setFlash(OPT_RESULT, '产品信息添加失败！');
            }
        }

        $groups = Group::find()->select(['id', 'name'])->all();
        return $this->render('edit_product', [
            'productForm' => $productForm,
            'groups' => $groups,
            'isAdd' => true
        ]);
    }

    public function actionModifyProduct($id)
    {
        $productForm = new ProductForm();

        if (isset($_POST['ProductForm']) && $productForm->loadData()) {
            $productForm->id = $id;
            $productForm->isModify = true;
            if ($productForm->validate()) {
                $result = $productForm->modifyProductOfDb($id);
                if ($result) {
                    /* 数据修改成功 */
                    Yii::$app->session->setFlash(OPT_RESULT, '产品信息修改成功！');
                    return $this->refresh();
                } else {
                    /* 数据修改失败 */
                    Yii::$app->session->setFlash(OPT_RESULT, '产品信息修改失败！');
                }
            }
        } else {
            $product = Product::findOne(['id' => $id]);
            $productForm->name = $product->name;
            $productForm->groupId = $product->group_id;
            $productForm->introduce = $product->introduce;
        }

        $groups = Group::find()->select(['id', 'name'])->all();
        return $this->render('edit_product', [
            'productForm' => $productForm,
            'groups' => $groups,
            'isAdd' => false
        ]);
    }

    public function actionAddModule()
    {
        $moduleForm = new ModuleForm();
        if (isset($_POST['ModuleForm']) && $moduleForm->loadData() && $moduleForm->validate()) {
            $result = $moduleForm->addModuleToDb();
            if ($result) {
                /* 数据修改成功 */
                Yii::$app->session->setFlash(OPT_RESULT, '产品模块信息添加成功！');
                return $this->refresh();
            } else {
                /* 数据修改失败 */
                Yii::$app->session->setFlash(OPT_RESULT, '产品模块信息添加失败！');
            }
        }

        /* 获得所有产品信息 */
        $products = Product::find()->select(['id', 'name', 'group_id'])->all();
        if (count($products) > 0) {
            $group = Group::find()->select(['member'])->where(['id' => $products[0]->group_id])->one();
            if ($group != null) {
                $memberIds = Json::decode($group->member);
                $groupMembers = User::find()->select(['id', 'name'])->where(['id' => $memberIds])->all();
            } else {
                $groupMembers = [];
            }
        } else {
            $groupMembers = [];
        }

        return $this->render('edit_module', [
            'moduleForm' => $moduleForm,
            'products' => $products,
            'groupMembers' => $groupMembers,
            'isAdd' => true,
        ]);
    }

    public function actionModifyModule($id)
    {
        $moduleForm = new ModuleForm();

        if (isset($_POST['ModuleForm']) && $moduleForm->loadData()) {
            $moduleForm->isModify = true;
            $moduleForm->productId = $id;
            if ($moduleForm->validate()) {
                $result = $moduleForm->modifyModuleOfDb();
                if ($result) {
                    /* 数据修改成功 */
                    Yii::$app->session->setFlash(OPT_RESULT, '产品模块信息修改成功！');
                    return $this->refresh();
                } else {
                    /* 数据修改失败 */
                    Yii::$app->session->setFlash(OPT_RESULT, '产品模块信息修改失败！');
                }
            }
        }

        /* 获得所有产品信息 */
        $product = Product::find()->select(['name', 'group_id'])->where(['id' => $id])->one();
        $modules = [];
        $groupMembers = [];
        if ($product != null) {
            $moduleForm->productName = $product->name;
//            $modules = ProductModule::findAll(['product_id' => $id]);
            $modules = Module::find()->select(['id', 'name', 'fuzeren', 'introduce'])->where(['product_id' => $id])->all();
            if (count($modules) > 0) {
                $moduleForm->id = $modules[0]->id;
                $moduleForm->name = $modules[0]->name;
                $moduleForm->fuzeren = Json::decode($modules[0]->fuzeren);
                $moduleForm->introduce = $modules[0]->introduce;
            }

//            $groupMembers = UserGroup::find()->joinWith('user')->where(['group_id' => $product->group_id])->all();
            $group = Group::find()->where(['id' => $product->group_id])->one();
            if ($group != null)
                $groupMembers = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
            else
                $groupMembers = [];
        }

        return $this->render('edit_module', [
            'moduleForm' => $moduleForm,
            'modules' => $modules,
            'groupMembers' => $groupMembers,
            'isAdd' => false,
        ]);
    }

    public function actionGetModule($moduleId)
    {
        $module = Module::find()->select(['name', 'fuzeren', 'introduce'])->where(['id' => $moduleId])->one();
        echo Json::encode($module);
    }

    public function actionGetGroupMember($productId)
    {
        //由产品id找到groupId，然后由groupId找到userId,然后从userId找到useName
        $chooseProduct = Product::find()->select(['group_id'])->where(['id' => $productId])->one();
        if ($chooseProduct == null)
            return '';
        $group = Group::find()->select(['member'])->where(['id' => $chooseProduct->group_id])->one();
        if ($group != null)
            $allMembers = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
        else
            $allMembers = [];

        return Json::encode($allMembers);
    }

    public function actionDeleteProduct($productId)
    {
        /* 删除产品的同时，也需要将产品的模块删除 */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Module::deleteAll(['product_id' => $productId]);
            Product::deleteAll(['id' => $productId]);
            $transaction->commit();
            echo 'success';
        } catch (Exception $e) {
            $transaction->rollBack();
            echo 'failure';
        }
    }

}