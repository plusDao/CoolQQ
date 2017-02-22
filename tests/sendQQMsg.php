<?php
/**
 * Created by PhpStorm.
 * User: hiilee
 * Date: 17/2/19
 * Time: 02:37
 */

require(__DIR__ . '/../vendor/autoload.php');

use hiilee\coolq\CoolQ;
use hiilee\coolq\CoolQMsg;
use hiilee\coolq\msg\QQMsg;

//发送消息前需获取coolq实列;实列为私有Coolq静态成员
CoolQ::getCoolQ('192.168.137.217', 19739);

//常用功能
$msg = new QQMsg();
//讨论组qqtask
$msg->qqNo = '1624648313';//讨论组号
$msg->type = QQMsg::MSG_TYPE_DISCUSS;//发送消息类型为讨论组
$msg->msg = '现在北京时间: ' . date('Y-m-d H:i:s');
CoolQ::sendQqMsg($msg);

//多条内容拼接为一条消息,最终qq消息内容以换行符分隔
$msgBody = ['第一行'];
$msgBody[] = '第二行';
$msg->msg = $msgBody;
CoolQ::sendQqMsg($msg);

//发送qq群消息,并@某人
$msg->msg = [
    CoolQ::sendAt('123321123'),//@某群成员,填写qq号
    '老板, 今天开会不',
];
//CoolQ::sendQqMsg($msg);