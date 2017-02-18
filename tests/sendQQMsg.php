<?php
/**
 * Created by PhpStorm.
 * User: hiilee
 * Date: 17/2/19
 * Time: 02:37
 */

require(__DIR__ . '/../vendor/autoload.php');
include '../coolq/CoolQ.php';
include '../coolq/CoolQMsg.php';
include '../coolq/hstb/WebSocketClient.php';

use hiilee\coolq\CoolQ;
use hiilee\coolq\CoolQMsg;

$msg = new CoolQMsg();
//讨论组qqtask
$msg->qqNO = '1624648313';
$msg->sendType = CoolQMsg::SEND_MSG_TYPE_DISCUSS;
$msg->msg = 'fighting';
CoolQ::getCoolQ('192.168.1.28', 19730);
CoolQ::sendQqMsg($msg);