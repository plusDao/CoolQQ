<?php
/**
 * ==============================================
 * Create at 2017-02-22 上午10:13
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author HiiLee <heretreeli@163.com>
 */

namespace hiilee\coolq\msg;


abstract class Msg
{
    /** @var string qq号:群号,个人号,讨论组号 */
    public $qqNo;
    /** @var string 发送消息的类型:私聊,群聊,讨论组 */
    public $msg = [];
    /** @var string 消息类型*/
    public $type;
    /** @var int 创建时间 */
    public $createTime;
}