<?php
/**
 *
 * Created by GuLang on 2015-05-18.
 */

namespace app\modules\api\controllers;

use app\modules\api\models\HttpResult;
use app\models\Bug;
use Yii;
use yii\data\Pagination;
use app\tools\MyConstant;


class PersonController extends BaseController
{
    public function actionSubmit()
    {
        $result = new HttpResult();

        $query = Bug::find()->select([
            Bug::tableName() . '.id',
            Bug::tableName() . '.name',
            'priority',
            'serious_id',
            'status',
            Bug::tableName() . '.create_time'
        ])->where([
            'creator_id' => Yii::$app->user->identity->getId()
        ])->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取成功";
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $bugs
        ];;
        return $result->parseJson();
    }

    public function actionAssign()
    {
        $result = new HttpResult();

        $query = Bug::find()->select([
            Bug::tableName() . '.id',
            Bug::tableName() . '.name',
            'priority',
            'serious_id',
            'status',
            Bug::tableName() . '.create_time'
        ])->where([
            'assign_id' => Yii::$app->user->identity->getId()
        ])->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取成功";
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $bugs
        ];;
        return $result->parseJson();
    }

    public function actionOpt()
    {
        $result = new HttpResult();

        $query = Bug::find()->select([
            Bug::tableName() . '.id',
            Bug::tableName() . '.name',
            'priority',
            'serious_id',
            'status',
            Bug::tableName() . '.create_time'
        ])->andFilterWhere([
            'like', 'introduce', '"name":"' . Yii::$app->user->identity->name . '"'
        ])->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获取成功";
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $bugs
        ];;
        return $result->parseJson();
    }

}