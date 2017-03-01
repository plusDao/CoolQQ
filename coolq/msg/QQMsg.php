<?php
/**
 * ==============================================
 * Create at 2017-02-15 下午5:52
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author hiilee <heretreeli@163.com>
 */

namespace hiilee\coolq\msg;

/**
 * Class QQMsg
 * 要发送的qq消息
 * @package hiilee\coolq
 */
class QQMsg extends Msg
{
    /**
     * 发送消息的类型(私聊,群,讨论组)
     */
    const MSG_TYPE_PRIVATE = '1';
    const MSG_TYPE_GROUP = '2';
    const MSG_TYPE_DISCUSS = '3';

    public $type = self::MSG_TYPE_PRIVATE;

    public function __construct()
    {
        $this->createTime = time();
    }

    /**
     * 判断是否为有效消息
     * @return bool
     */
    public function isInvalidMsg()
    {
        if (empty($this->qqNo) && empty($this->type) && ($this->msg === [] || $this->msg === '')) {
            return false;
        } else {
            return true;
        }
    }
}