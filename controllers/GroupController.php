<?php
/**
 * 团队控制器
 * Created by GuLang on 2015-04-30.
 */
/* @var $this \yii\web\View */
namespace app\controllers;

use app\models\Group;
use app\models\GroupEditForm;
use app\models\Product;
use app\models\User;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;
use yii\base\Exception;

class GroupController extends Controller
{
    public function actionIndex()
    {
        $query = Group::find()->joinWith(['createUser']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 6,
        ]);
        $groups = $query->limit($pagination->limit)->offset($pagination->offset)->all();
        foreach ($groups as $group) {
            $group->member = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
        }

        return $this->render('index', [
            'groups' => $groups,
            'pagination' => $pagination,
        ]);
    }

    public function actionAdd()
    {
        $groupEditForm = new GroupEditForm();
        $groupEditForm->creator = 2;//\Yii::$app->user
        $groupEditForm->isModify = false;

        if (isset($_POST['GroupEditForm']) && $groupEditForm->loadData() && $groupEditForm->validate()) {
            $result = $groupEditForm->addGroupToDb();
            if ($result) {
                \Yii::$app->session->setFlash(OPT_RESULT, '新建团队成功!');
                return $this->refresh();
            } else {
                \Yii::$app->session->setFlash(OPT_RESULT, '新建团队失败!');
            }
        }

        $allUser = User::find()->select(['id', 'name'])->all();
        $selectedMember = [];
        if (isset($groupEditForm->member) && is_array($groupEditForm->member)) {
            $selectedMember = User::find()->select(['id', 'name'])->where(['id' => $groupEditForm->member])->all();
        }

        return $this->render('edit', [
            'groupEditForm' => $groupEditForm,
            'allUser' => $allUser,
            'selectedMember' => $selectedMember,
            'isAdd' => true,
        ]);
    }

    public function actionModify($id)
    {
        $groupEditForm = new GroupEditForm();
        $groupEditForm->creator = 2;//\Yii::$app->user
        $groupEditForm->id = $id;
        $groupEditForm->isModify = true;

        if (isset($_POST['GroupEditForm']) && $groupEditForm->loadData() && $groupEditForm->validate()) {
            $result = $groupEditForm->modifyGroupOfDb();
            if ($result) {
                \Yii::$app->session->setFlash(OPT_RESULT, '修改团队信息成功!');
                return $this->refresh();
            } else {
                \Yii::$app->session->setFlash(OPT_RESULT, '修改团队信息失败!');
            }
        }

        $group = Group::find()->where(['id' => $id])->one();
        if ($group != null) {
            $groupEditForm->name = $group->name;
            $groupEditForm->member = Json::decode($group->member);
            $groupEditForm->introduce = $group->introduce;
        }

        $allUser = User::find()->select(['id', 'name'])->all();
        $selectedMember = [];
        if (isset($groupEditForm->member) && is_array($groupEditForm->member)) {
            $selectedMember = User::find()->select(['id', 'name'])->where(['id' => $groupEditForm->member])->all();
        }

        return $this->render('edit', [
            'groupEditForm' => $groupEditForm,
            'allUser' => $allUser,
            'selectedMember' => $selectedMember,
            'isAdd' => false,
        ]);
    }

    public function actionDelete($id)
    {
        /* 删除团队信息之前，要先确认是否有产品是该团队负责的，如果有，则不允许删除 */
        $group = Group::find()->where(['id' => $id])->one();
        if ($group === null) {
            echo '{"type":"failure","message":"指定的团队不存在！"}';
        } else {
            $product = Product::find()->where(['group_id' => $id])->one();
            if ($product === null) {//可删除
                try {
                    $result = $group->DELETE();
                } catch (Exception $e) {
                    $result = false;
                }
                if ($result)
                    echo '{"type":"success","message":"删除成功！"}';
                else
                    echo '{"type":"failure","message":"删除失败！"}';
            } else {//还有产品在改团队的负责下
                echo '{"type":"failure","message":"该团队还负责着一些产品，无法删除！"}';
            }
        }
    }

}