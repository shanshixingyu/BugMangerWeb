<?php
/**
 *
 * Created by GuLang on 2015-05-08.
 */

namespace app\controllers;

use app\tools\MyConstant;
use Yii;
use app\models\Bug;
use yii\data\Pagination;
use yii\filters\AccessControl;

class PersonController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['submit'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'verbs' => ['?']
                    ],
                ],
            ],
        ];
    }

    public function actionSubmit()
    {
        $query = Bug::find()->where([
            'creator_id' => Yii::$app->user->identity->getId()
        ])->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('show', ['type' => MyConstant::PERSON_TYPE_SUBMIT, 'bugs' => $bugs, 'pagination' => $pagination]);
    }

    public function actionAssign()
    {
        $query = Bug::find()->where([
            'assign_id' => Yii::$app->user->identity->getId()
        ])->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('show', ['type' => MyConstant::PERSON_TYPE_ASSIGN, 'bugs' => $bugs, 'pagination' => $pagination]);
    }

    public function actionOpt()
    {
        $query = Bug::find()->where('introduce regexp "by *' . Yii::$app->user->identity->name . '\""')->addOrderBy(Bug::tableName() . '.create_time DESC');
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $bugs = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('show', ['type' => MyConstant::PERSON_TYPE_MY_OPT, 'bugs' => $bugs, 'pagination' => $pagination]);
    }
}