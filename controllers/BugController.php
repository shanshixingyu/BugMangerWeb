<?php
/**
 *
 * Created by GuLang on 2015-05-02.
 */

namespace app\controllers;


use app\models\SearchBugForm;

class BugController extends BaseController
{
    public function actionIndex()
    {
        $this->auth();

        return $this->render('index', []);
    }

    public function actionBug()
    {
        $this->auth();

        $searchBugForm = new SearchBugForm();
        if (isset($_POST['SearchBugForm']))
            var_dump($_POST['SearchBugForm']);


        return $this->render('bug', ['searchBugForm' => $searchBugForm]);
    }
}