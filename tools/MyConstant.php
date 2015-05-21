<?php
/**
 *
 * Created by GuLang on 2015-05-05.
 */

namespace app\tools;


class MyConstant
{
    const IMAGE_PATH = 'BufferFile/images/';
    const IMAGE_HEIGHT = 250;
    const ATTACHMENT_PATH = 'BufferFile/attachments/';
    /* 下拉列表的全部项的值 */
    const OPTION_ALL = -100;

    const PERSON_TYPE_SUBMIT = 'submit';
    const PERSON_TYPE_ASSIGN = 'assign';
    const PERSON_TYPE_MY_OPT = 'my_opt';


    /* 图标类型 */
    const ECHART_TYPE_SUBMIT_DAY = "ECHART_TYPE_SUBMIT_DAY";
    const ECHART_TYPE_SUBMIT_MONTH = "ECHART_TYPE_SUBMIT_MONTH";
    const ECHART_TYPE_TOTAL_DAY = "ECHART_TYPE_TOTAL_DAY";
    const ECHART_TYPE_TOTAL_MONTH = "ECHART_TYPE_TOTAL_MONTH";
    const ECHART_TYPE_MODULE = "ECHART_TYPE_MODULE";
    const ECHART_TYPE_PRIORITY = "ECHART_TYPE_PRIORITY";
    const ECHART_TYPE_STATUS = "ECHART_TYPE_STATUS";


    const VISIT_CODE_NOT_PERMISSION= 800;
    const VISIT_CODE_OUT_DATE= 700;
    const VISIT_CODE_NOT_LOGIN = 600;
    const VISIT_CODE_ADD_EXIST = 500;
    const VISIT_CODE_NOT_EXIST = 400;
    const VISIT_CODE_PASSWORD_WRONG = 300;
    const VISIT_CODE_FAILURE = 200;
    /**
     * 响应状态码不是200
     */
    const VISIT_CODE_NO_OK = 100;
    /**
     * 访问成功
     */
    const VISIT_CODE_SUCCESS = 0;

    /**
     * 连接超时
     */
    const VISIT_CODE_CONNECT_TIME_OUT = -100;
    /**
     * 连接错误
     */
    const VISIT_CODE_CONNECT_ERROR = -200;

    const VISIT_CODE_NO_LOGIN = -300;

    const VISIT_CODE_NO_POST = -400;
    const VISIT_CODE_NO_USER = -500;


}