<?php
/**
 * 用户登录控制器
 * Created by GuLang on 2015-04-16.
 */
namespace app\controllers;

use app\models\Product;
use app\models\Module;
use app\models\Role;
use app\models\User;
use app\models\Group;
use app\models\UserModifyForm;
use Yii;
use app\models\LoginForm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class SiteController extends Controller
{
    public $oldUserModifyForm = false;


    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'foreColor' => 0x00FF00,
                'backColor' => 0xD0F2FB,
                'width' => 75,
                'height' => 25,
                'padding' => 1,
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 0,
            ],
        ];
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();
        if (isset($_POST['LoginForm'])) {
            /* 表示按了登录过后过来的 */
            if ($loginForm->load(Yii::$app->request->post("LoginForm")) && $loginForm->login()) {
                $this->redirect("./index.php?r=site/bug");
            }
        }
        $loginForm->verifyCode = "";
        return $this->renderPartial("login", ['loginForm' => $loginForm]);
    }

    public function actionBug()
    {
        $productDataList = ['孤狼软件', '毕设'];
        return $this->render('bug', ["productDataList" => $productDataList]);
    }

    public function actionPim()
    {
        $userModifyForm = new UserModifyForm();

        if (isset($_POST['UserModifyForm']) && $userModifyForm->loadData() && $userModifyForm->validate()) {
            /* 表示修改个人信息部分过来的 */
            $result = $userModifyForm->modifyPimOfDb();
            $this->getView()->params[OPT_RESULT] = $result;
            //更新数据库后，还要将Yii中的user也更新下
            // Yii::$app->user = $user;
            //在这里不能使用refresh(),因为他会使得一些params数据不存在
            $userModifyForm->oldPassword = "";
            $userModifyForm->password = "";
            $userModifyForm->password2 = "";
        } else {
            if (isset($this->getView()->params[OPT_RESULT]))
                unset($this->getView()->params[OPT_RESULT]);
            //到这里表示修改信息验证不成功或者还没修改信息
            /*从中获得信息，并且最好还是能够在进入界面的时候重新访问下数据库，
                              保证数据最新,不过数据也一般只有自己能够修改，也可不修改*/
            $user = User::findOne(2);//这句主要是为了测试用，之后要用Yii->app->user替代
            $role = Role::findOne($user->role_id);
            $userModifyForm->userId = $user->id;
            $userModifyForm->userName = $user->name;
            $userModifyForm->roleName = $role->name;
            $userModifyForm->email = $user->email;
        }
        /* 查询指定用户id下的所有组名 */
        $groups = Group::find()->where('member like "%' . $userModifyForm->userId . '%"')->all();
        $groupNames = [];
        foreach ($groups as $group) {
            $groupNames[] = $group->name;
        }
        unset($groups);

        /* 参与项目与模块 */
        $where = 'fuzeren like "%\"' . $userModifyForm->userId . '\"%"';
        $productModules = Module::find()->joinWith('product')->where($where)->all();
        $productModuleData = [];
        foreach ($productModules as $productModule) {
            $productModuleData[] = ['product' => $productModule->product->name, 'module' => $productModule->name];
        }
        unset($productModules);

        return $this->render("pim", [
            'userModifyForm' => $userModifyForm,
            'groupNames' => $groupNames,
            'productModuleData' => $productModuleData,
        ]);
    }

    public function actionManager()
    {
        return $this->render('manager');
    }

    public function actionProductManager()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->joinWith(['createUser', 'groupDetail']),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        return $this->render('product_manager', ['dataProvider' => $dataProvider,]);
    }


    public function actionTest()
    {
        return $this->render('test');
    }

}