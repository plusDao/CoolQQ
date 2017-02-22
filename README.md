# CoolQQ

coolq插件php对接的封装, 实现php发送qq消息

## requrie
- php对接插件1.3
- coolq平台

## 使用说明
coolq(酷q)

![](https://camo.githubusercontent.com/a2e5496c6d9722cf10622d4cf9e030b0cda26d62/687474703a2f2f7773312e73696e61696d672e636e2f6c617267652f38633931666661626779316663767261646a3671756a32306775306375337a69)

###具体使用细节

```php
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
```