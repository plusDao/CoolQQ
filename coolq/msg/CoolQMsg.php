<?php
/**
 * ==============================================
 * Create at 2017-02-22 上午10:33
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @copyright Copyright (c) 2017 重庆路威科技发展有限公司
 * @link http://www.igong.com
 * @author Hein Lee <heretreeli@gmail.com>
 */

namespace hiilee\coolq\msg;

/**
 * Class CoolQMsg
 * CoolQ提交的消息
 * @package hiilee\coolq
 */
class CoolQMsg extends Msg
{
    /** 私人消息 */
    const MSG_TYPE_PRIVATE = '1';
    /** 群消息 */
    const MSG_TYPE_GROUP = '2';
    /** 讨论组消息 */
    const MSG_TYPE_DISCUSS = '4';
    /** @var string 群号或讨论组号 */
    public $groupNo;
    /** @var int 创建时间 */
    public $createTime;

    public function __construct(array $rec)
    {
        $this->setMsg($rec);
        $this->createTime = time();
    }

    public function setMsg(array $rec)
    {
        $this->type = (string)$rec['Type'];
        $this->qqNo = (string)$rec['QQ'];
        $this->msg = urldecode($rec['Msg']);
        if (isset($rec['Group']) || isset($rec['Discuss'])) {
            $this->groupNo = @$rec['Group'] ?: $rec['Discuss'];
        }
    }
}