<?php
/**
 * 添加产品模块Action
 * Created by GuLang on 2015-04-25.
 */

namespace app\controllers\action;

use app\models\ModuleForm;
use app\models\Product;
use app\models\UserGroup;
use yii\base\Action;
use Yii;

class AddModuleAction extends Action
{
    public function run()
    {
        $moduleForm = new ModuleForm();
        if (isset($_POST['ModuleForm']) && $moduleForm->loadData() && $moduleForm->validate()) {
            $result = $moduleForm->addModuleToDb();
            if ($result) {
                /* 数据保存成功 */
                return $this->controller->refresh();
            } else {
                /* 插入数据库失败 */
                $this->controller->getView()->params[OPT_RESULT] = false;
            }
        } else {
            if (isset($this->controller->getView()->params[OPT_RESULT]))
                unset($this->controller->getView()->params[OPT_RESULT]);
        }

        /* 获得所有产品信息 */
        $products = Product::find()->select(['id', 'name', 'group_id'])->all();
        if (count($products) > 0)
            $groupMembers = UserGroup::find()->joinWith('user')->where(['group_id' => $products[0]->group_id])->all();
        else
            $groupMembers = new UserGroup();

        return $this->controller->render('add_module', [
            'moduleForm' => $moduleForm,
            'products' => $products,
            'groupMembers' => $groupMembers,
        ]);
    }
}