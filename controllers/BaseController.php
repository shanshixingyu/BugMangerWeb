<?php
/**
 *
 * Created by GuLang on 2015-05-02.
 */

namespace app\controllers;


use app\models\Module;
use app\models\Product;
use yii\web\Controller;
use Yii;

class BaseController extends Controller
{
    public $productModuleInfo;

    /**
     * 用户身份控制,没登录的用户不允许访问
     * @return \yii\web\Response
     */
    protected function auth()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('index.php?r=site/login');
        }
    }

    protected function permission()
    {

    }

    public function init()
    {
        $this->productModuleInfo = $this->getProductModuleInfo();
    }


    public function getProductModuleInfo()
    {
        $products = Product::find()->select(['id', 'name'])->all();
        $modules = [];
        if (count($products) > 0) {
            $modules = Module::find()->select(['id', 'name'])->where(['product_id' => $products[0]->id])->all();
        } else {
            $modules = [];
        }
        return ['products' => $products, 'modules' => $modules];
    }


    /**
     * 根据原始密码获得加密后的密码
     * @param string $rawPassword 原始密码，也是加密前的密码,默认密码为:123456
     * @return string 返回加密后的密码
     */
    public static function getEncryptedPassword($rawPassword = '123456')
    {

        if ($rawPassword === null || trim($rawPassword) == '') {
            $rawPassword = '123456';
        }
        $tempPassword = md5($rawPassword . PASSWORD_KEY);
        return substr($tempPassword, 3, 20);
    }

}