<?php
/**
 * 团队控制器
 * Created by GuLang on 2015-04-30.
 */
/* @var $this \yii\web\View */
namespace app\controllers;

use app\models\GroupDetail;
use app\models\GroupEditForm;
use app\models\User;
use app\models\UserGroup;
use yii\data\Pagination;
use yii\web\Controller;

class GroupController extends Controller
{
    public function actionIndex()
    {
        $query = GroupDetail::find()->joinWith(['createUser']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 6,
        ]);
        $groupDetails = $query
            ->limit($pagination->limit)->offset($pagination->offset)->all();
        foreach ($groupDetails as $groupDetail) {
            $groupObjs = UserGroup::find()->select(['user_id'])->where(['group_id' => $groupDetail->id])->all();
            $groupIds = [];
            foreach ($groupObjs as $groupObj) {
                $groupIds[] = $groupObj->user_id;
            }
            $groupDetail->member = User::find()->select(['name'])->where(['id' => $groupIds])->all();
        }

        return $this->render('index', [
            'groupDetails' => $groupDetails,
            'pagination' => $pagination,
        ]);
    }

    public function actionAdd()
    {
        $groupEditForm = new GroupEditForm();

        if (isset($_POST['GroupEditForm']) && $groupEditForm->loadData() && $groupEditForm->validate()) {
            var_dump($groupEditForm);
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
        ]);
    }


}