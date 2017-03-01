<?php
namespace hiilee\coolq\util;
/**
 * Created by PhpStorm.
 * User: HiiLee
 * Date: 17/3/2
 * Time: 02:32
 */

/**
 * Class CqCode
 * 酷Q CQ码
 * @package hiilee\coolq\util
 */
class CqCode
{
    /** 图片 */
    const CQ_IMAGE = 'image';
    /** emoji */
    const CQ_EMOJI = 'emoji';
    /** QQ表情 */
    const CQ_FACE = 'face';

    /** CQ码正则匹配表达式 */
    const CQ_PATTERN = '@\[CQ:(\w+),\w+=(\w+(\.\w+)?)\]@';

    /** @var  CQ码类型 */
    public $type;
    /** @var  CQ码值 */
    public $value;
}