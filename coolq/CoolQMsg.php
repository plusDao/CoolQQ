<?php
/**
 * ==============================================
 * Create at 2017-02-15 下午5:52
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @link http://www.igong.com
 * @author hiilee <heretreeli@163.com>
 */

namespace hiilee\coolq;


class CoolQMsg
{
    /**
     * 发送消息的类型(私聊,群,讨论组);与zxzjb中类型常量一致
     */
    const SEND_MSG_TYPE_PRIVATE = '0';
    const SEND_MSG_TYPE_GROUP = '1';
    const SEND_MSG_TYPE_DISCUSS = '2';

    /** @var string qq号:群号,个人号,讨论组号 */
    public $qqNO;
    /** @var array 消息体 */
    public $msg = [];
    /** @var string 发送消息的类型:私聊,群聊,讨论组 */
    public $sendType = self::SEND_MSG_TYPE_PRIVATE;

    /**
     * 判断是否为有效消息
     * @return bool
     */
    public function isInvalidMsg()
    {
        if (empty($this->qqNO) && empty($this->sendType) && ($this->msg === [] || $this->msg === '')) {
            return false;
        } else {
            return true;
        }
    }
}