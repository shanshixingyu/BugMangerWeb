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

/* 该常量主要用于用户密码的加密 */
defined("PASSWORD_KEY") or define("PASSWORD_KEY", 'GuLang');

/* 设置bug的当前状态常量，因为常量只能是普通的变量 */
defined("BUG_STATUS_CLOSED") or define("BUG_STATUS_CLOSED", '关闭');
defined("BUG_STATUS_UNSOLVED") or define("BUG_STATUS_UNSOLVED", '未解决');
defined("BUG_STATUS_SOLVED") or define("BUG_STATUS_SOLVED", '解决');
defined("BUG_STATUS_ACTIVE") or define("BUG_STATUS_ACTIVE", '重新激活');
defined("BUG_STATUS_OTHER") or define("BUG_STATUS_OTHER", '其它');
defined("BUG_STATUS") or define("BUG_STATUS", \yii\helpers\Json::encode([
    '0' => BUG_STATUS_CLOSED,
    '1' => BUG_STATUS_UNSOLVED,
    '2' => BUG_STATUS_SOLVED,
    '3' => BUG_STATUS_ACTIVE,
    '4' => BUG_STATUS_OTHER
]));
/* 严重程度，因为常量只能是普通的变量 */
defined("BUG_SERIOUS_NO") or define("BUG_SERIOUS_NO", '不影响');
defined("BUG_SERIOUS_LITTLE") or define("BUG_SERIOUS_LITTLE", '轻度影响');
defined("BUG_SERIOUS_SERIOUS") or define("BUG_SERIOUS_SERIOUS", '影响');
defined("BUG_SERIOUS_BIGGER") or define("BUG_SERIOUS_BIGGER", '影响较大');
defined("BUG_SERIOUS_VERY") or define("BUG_SERIOUS_VERY", '严重影响');
defined("BUG_SERIOUS") or define("BUG_SERIOUS", \yii\helpers\Json::encode([
    '0' => BUG_SERIOUS_NO,
    '1' => BUG_SERIOUS_LITTLE,
    '2' => BUG_SERIOUS_SERIOUS,
    '3' => BUG_SERIOUS_BIGGER,
    '4' => BUG_SERIOUS_VERY
]));
/* 优先级，因为常量只能是普通的变量 */
defined("BUG_PRIORITY_LOW") or define("BUG_PRIORITY_LOW", '低');
defined("BUG_PRIORITY_MIDDLE") or define("BUG_PRIORITY_MIDDLE", '中');
defined("BUG_PRIORITY_HIGH") or define("BUG_PRIORITY_HIGH", '高');
defined("BUG_PRIORITY_URGENT") or define("BUG_PRIORITY_URGENT", '紧急');
defined("BUG_PRIORITY") or define("BUG_PRIORITY", \yii\helpers\Json::encode([
    '0' => BUG_PRIORITY_LOW,
    '1' => BUG_PRIORITY_MIDDLE,
    '2' => BUG_PRIORITY_HIGH,
    '3' => BUG_PRIORITY_URGENT,
]));