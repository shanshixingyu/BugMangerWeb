<?php
/**
 * 定义常量文件
 * Created by GuLang on 2015-04-16.
 */

defined("IMG_PATH") or define("IMG_PATH", '/BugManagerWeb/web/img/');
defined("CSS_PATH") or define("CSS_PATH", '/BugManagerWeb/web/css/');
defined("JS_PATH") or define("JS_PATH", '/BugManagerWeb/web/js/');
defined("ASSETS_PATH") or define("ASSETS_PATH", '/BugManagerWeb/web/assets/');

/* 定义js中影响行数的键值 */
defined("OPT_RESULT") or define("OPT_RESULT", 'opt_result');

/* 添加产品的错误状态 */
defined("PRODUCT_HAS_EXIST_ERROR") or define("PRODUCT_HAS_EXIST_ERROR", 'product_has_exist_error');
defined("ADD_PRODUCT_OTHER_ERROR") or define("ADD_PRODUCT_OTHER_ERROR", 'add_product_other_error');

/* 该常量主要用于用户密码的加密 */
defined("PASSWORD_KEY") or define("PASSWORD_KEY", 'GuLang');

/* 设置bug的当前状态常量，因为常量只能是普通的变量 */
defined("BUG_STATUS") or define("BUG_STATUS", \yii\helpers\Json::encode(['0' => '关闭', '1' => '未解决', '2' => '解决', '3' => '重新激活', '4' => '其它']));
/* 严重程度，因为常量只能是普通的变量 */
defined("BUG_SERIOUS") or define("BUG_SERIOUS", \yii\helpers\Json::encode(['0' => '影响不大', '1' => '不严重', '2' => '严重', '3' => '较严重', '4' => '非常严重']));