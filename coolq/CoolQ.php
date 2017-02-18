<?php
namespace hiilee\coolq;

use hiilee\coolq\hstb\WebSocketClient;

/**
 * Created by PhpStorm.
 * User: hiilee
 * Date: 16/9/6
 * Time: 上午10:13
 */
class CoolQ
{
    /**
     * @var WebSocketClient
     */
    protected $WebSocketClient;

    /** @var  CoolQ */
    protected static $coolq;

    protected function __construct()
    {
    }

    /**
     * 获取coolq
     * @param string $ip
     * @param int $port
     * @return CoolQ
     * @throws \Exception
     */
    public static function getCoolQ($ip = '', $port = null)
    {
        if (self::$coolq instanceof CoolQ) {
            return self::$coolq;
        } else {
            try {
                self::$coolq = new self();
                if (class_exists('yii') && empty($ip) && empty($port)) {
                    $ip = (string)\Yii::$app->params['coolqBot']['host'];
                    $port = (int)\Yii::$app->params['coolqBot']['port'];
                    self::$coolq->WebSocketClient = new WebSocketClient($ip, $port);//建立连接,失败则无法运行本脚本
                } else {
                    self::$coolq->WebSocketClient = new WebSocketClient((string)$ip, (int)$port);
                }
                return self::$coolq;
            } catch (\Exception $exception) {
                throw new \Exception('获取coolq实列失败, 请检查配置或网络是否连接;');
            }
        }
    }

    public function SendData($text)
    {
        $Get = $this->WebSocketClient->sendData($text);
        return $Get;
    }

    /**
     * @某人
     * @param $QQ
     * @param bool $NeedSpace
     * @return string
     */
    public static function sendAt($QQ, $NeedSpace = true)
    {
        $QQ = $QQ == -1 ? 'all' : $QQ;
        $a = "[CQ:at,qq=$QQ]";
        $a .= $NeedSpace ? ' ' : '';
        return $a;
    }

    /**
     * 发送Emoji表情
     * @param $id
     * @return string
     */
    public static function sendEmoji($id)
    {
        return "[CQ:emoji,id=$id]";
    }

    /**
     * 发送表情
     * @param $id
     * @return string
     */
    public static function sendFace($id)
    {
        return "[CQ:face,id=$id]";
    }

    /**
     * 发送窗口抖动
     * @return string
     */
    public static function sendShake()
    {
        return "[CQ:shake]";
    }

    /**
     * 反转义
     * @param $msg
     * @return mixed
     */
    public static function AntiEscape($msg)
    {
        $msg = str_replace("&#91;", "[", $msg);
        $msg = str_replace("&#93;", "]", $msg);
        $msg = str_replace("&#44;", ",", $msg);
        $msg = str_replace("&amp;", "&", $msg);
        return $msg;
    }

    /**
     * 转义
     * @param $msg
     * @param bool $Comma_Escape
     * @return mixed
     */
    public static function Escape($msg, $Comma_Escape = false)
    {
        //$Comma_$this->Escape 逻辑型[bit] => 逗号是否转义
        $msg = str_replace("[", "&#91;", $msg);
        $msg = str_replace("]", "&#93;", $msg);
        $msg = str_replace("&", "&amp;", $msg);
        if ($Comma_Escape) $msg = str_replace(",", "&#44;", $msg);
        return $msg;
    }

    /**
     * 发送链接分享
     * @param $Url
     * @param null $Title
     * @param null $Content
     * @param null $PicUrl
     * @return string
     */
    public static function sendShare($Url, $Title = null, $Content = null, $PicUrl = null)
    {
        /*
        $Url [text] => 点击卡片后跳转的网页地址
        $Title [text] => 可空,分享的标题，建议12字以内
        $Content [text] => 可空,分享的简介，建议30字以内
        $PicUrl [text] => 可空,分享的图片链接，留空则为默认图片
        */
        $msg = "[CQ:share,url=" . self::Escape($Url, true);
        if ($Title) $msg .= ",title=" . self::Escape($Title, true);
        if ($Content) $msg .= ",content=" . self::Escape($Content, true);
        if ($PicUrl) $msg .= ",image=" . self::Escape($PicUrl, true);
        $msg .= "]";
        return $msg;
    }

    /**
     * 发送名片分享
     * @param string $Type
     * @param $ID
     * @return string
     */
    public static function sendCardShare($Type = 'qq', $ID)
    {
        return "[CQ:contact,type=" . self::Escape($Type, true) . ",id=$ID";
    }

    /**
     * 发送匿名消息
     * @param bool $ignore
     * @return string
     */
    public static function sendAnonymous($ignore = false)
    {
        //$ignore =>是否不强制匿名,如果希望匿名失败时，将消息转为普通消息发送(而不是取消发送)，请置本参数为真。
        $a = "[CQ:anonymous";
        $a .= $ignore ? ',ignore=true]' : ']';
        return $a;
    }

    /**
     * 发送图片
     * @param $Filename
     * @return string
     */
    public static function sendImage($Filename)
    {
        return "[CQ:image,file=" . self::Escape($Filename) . "]";
    }

    /**
     * 发送音乐
     * @param $SongID
     * @param string $Type
     * @return string
     */
    public static function sendMusic($SongID, $Type = "qq")
    {
        //$Type => 音乐网站类型,目前支持 qq/QQ音乐 163/网易云音乐 xiami/虾米音乐，默认为qq
        $msg = "[CQ:music,id=$SongID,type=" . self::Escape($Type, true) . "]";
        return $msg;
    }

    /**
     * 发送自定义音乐分享
     * @param $Url
     * @param $Audio
     * @param null $Title
     * @param null $Content
     * @param null $Image
     * @return string
     */
    public static function sendCustomMusic($Url, $Audio, $Title = null, $Content = null, $Image = null)
    {
        /*
        参数 分享链接, 文本型, , 点击分享后进入的音乐页面（如歌曲介绍页）
        参数 音频链接, 文本型, , 音乐的音频链接（如mp3链接）
        参数 标题, 文本型, 可空, 音乐的标题，建议12字以内
        参数 内容, 文本型, 可空, 音乐的简介，建议30字以内
        参数 封面图片链接, 文本型, 可空, 音乐的封面图片链接，留空则为默认图片
        */
        $para = ',url=' . self::Escape($Url, true) . ',audio=' . self::Escape($Audio, true);
        if ($Title) $para .= ',title=' . self::Escape($Title, true);
        if ($Content) $para .= ',content=' . self::Escape($Content, true);
        if ($Image) $para .= ',image=' . self::Escape($Image, true);
        return "[CQ:music,type=custom$para]";
    }

    /**
     * 发送语音
     * @param $Filename
     * @return string
     */
    public static function sendVoice($Filename)
    {
        return "[CQ:record,file=" . self::Escape($Filename) . "]";
    }

    /**
     * 发送大表情(原创表情)
     * @param $ID
     * @param $Sid
     * @return string
     */
    public static function sendBigFace($ID, $Sid)
    {
        /*
        $ID [int] => 大表情所属系列的标识
        $Sid [text] => 大表情的唯一标识
        */
        return "[CQ:bface,p=$ID,id=$Sid]";
    }

    /**
     * 发送小表情
     * @param $id
     * @return string
     */
    public static function Send_SmallFace($id)
    {
        /*
        参数: $id [int] => 小表情代号
        */
        return "[CQ:sface,id=$id]";
    }

    /**
     * 发送厘米秀
     * @param $id
     * @param null $qq
     * @param null $content
     * @return string
     */
    public static function sendShow($id, $qq = null, $content = null)
    {
        /*
        参数:
        $id [int] => 动作代号
        $qq [int64] => 双人动作的对象,非必须
        $content [text] => 动作顺带的消息内容,不建议发送长文本
        */
        $msg = "[CQ:show,id=$id";
        if ($qq) $msg .= ",qq=$qq";
        if ($content) $msg .= ",content=$content";
        $msg .= "]";
        return $msg;
    }

    //下面为动态API，一般情况下返回状态码(0为成功),详细说明请见 http://d.cqp.me/Pro/%E5%BC%80%E5%8F%91/Error

    /**
     * 发送私聊信息
     * @param $QQ
     * @param $Msg
     * @return mixed
     */
    protected function sendPrivateMsg($QQ, $Msg)
    {
        $array = array(
            'Fun' => 'sendPrivateMsg',
            'QQ' => $QQ,
            'Msg' => $Msg
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 发送群信息
     * @param $Group
     * @param $Msg
     * @return mixed
     */
    protected function sendGroupMsg($Group, $Msg)
    {
        $array = array(
            'Fun' => 'sendGroupMsg',
            'Group' => $Group,
            'Msg' => $Msg
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 发送讨论组信息
     * @param $Discuss
     * @param $Msg
     * @return mixed
     */
    protected function sendDiscussMsg($Discuss, $Msg)
    {
        $array = array(
            'Fun' => 'sendDiscussMsg',
            'Group' => $Discuss,
            'Msg' => $Msg
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 发送赞
     * @param $QQ
     * @param int $Count
     * @return mixed
     */
    public function sendLike($QQ, $Count = 1)
    {
        $array = array(
            'Fun' => 'sendLike',
            'QQ' => $QQ,
            'Count' => $Count//赞的次数,最多为10
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 接收语音
     * @param $FileName
     * @param $Format
     * @return mixed
     */
    public function getRecord($FileName, $Format)
    {
        $array = array(
            'Fun' => 'getRecord',
            'File' => $FileName,//语音文件名,不带路径
            'Format' => $Format//所需的语音文件格式，目前支持 mp3,amr,wma,m4a,spx,ogg,wav,flac
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};//返回转换后的文件名
    }

    /**
     * 取登录QQ
     * @return mixed
     */
    public function getLoginQQ()
    {
        $array = array('Fun' => 'getLoginQQ');
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};
    }

    /**
     * 置群退出
     * @param $Group
     * @param bool $Dissolution
     * @return mixed
     */
    public function setGroupLeave($Group, $Dissolution = false)
    {
        $Temp = $Dissolution ? 1 : 0;//是否解散
        $array = array(
            'Fun' => 'setGroupLeave',
            'Group' => $Group,
            'Dissolution' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 取Cookies
     * @return mixed
     */
    public function getCookies()
    {
        $array = array('Fun' => 'getCookies');
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};
    }

    /**
     * 取登录昵称
     * @return mixed
     */
    public function getLoginNick()
    {
        $array = array('Fun' => 'getLoginNick');
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};
    }

    /**
     * 取CsrfToken
     * @return mixed
     */
    public function getCsrfToken()
    {
        $array = array('Fun' => 'getCsrfToken');
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};
    }

    /**
     * 取群成员信息
     * @param string $Group 群号
     * @param string $QQ
     * @param bool $UseCache
     * @return string
     */
    public function getGroupMemberInfo($Group, $QQ, $UseCache = true)
    {
        $Temp = $UseCache ? 1 : 0;//真为使用缓存
        $array = array(
            'Fun' => 'getGroupMemberInfo',
            'Group' => $Group,
            'QQ' => $QQ,
            'Cache' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        return $Get;//返回带有数据的Json文本
    }

    /**
     * 取陌生人信息
     * @param $QQ
     * @param bool $UseCache
     * @return string
     */
    public function getStrangerInfo($QQ, $UseCache = true)
    {
        $Temp = $UseCache ? 1 : 0;//真为使用缓存
        $array = array(
            'Fun' => 'getStrangerInfo',
            'QQ' => $QQ,
            'Cache' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        return $Get;//返回带有数据的Json文本
    }

    /**
     * 其他_字体转换
     * @param $ID
     * @return string
     */
    public function GetFontInfo($ID)
    {
        $array = array(
            'Fun' => 'GetFontInfo',
            'ID' => $ID,
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        return $Get;//返回带有数据的Json文本
    }

    /**
     * 其他_转换_文本到匿名
     * @param $source
     * @return string
     */
    public function GetAnonymousInfo($source)
    {
        $array = array(
            'Fun' => 'GetAnonymousInfo',
            'source' => $source,
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        return $Get;//返回带有数据的Json文本
    }

    /**
     * 其他_转换_文本到群文件
     * @param $source
     * @return string
     */
    public function GetFileInfo($source)
    {
        $array = array(
            'Fun' => 'GetFileInfo',
            'source' => $source,
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        return $Get;//返回带有数据的Json文本
    }

    /**
     * 其他_转换_悬浮窗到文本,实际上就是设置悬浮窗
     * @param $Data
     * @param $Unit
     * @param $Color
     * @return mixed
     */
    public function SetStatus($Data, $Unit, $Color)
    {
        $array = array(
            'Fun' => 'SetStatus',
            'Data' => $Data,//数据
            'Unit' => $Unit,//数据单位
            'Color' => $Color//显示的颜色 1/绿 2/橙 3/红 4/深红 5/黑 6/灰
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};//返回转换的文本,无卵用
    }

    /**
     * 置成员移除
     * @param $Group
     * @param $QQ
     * @param bool $RefuseJoin
     * @return mixed
     */
    public function setGroupKick($Group, $QQ, $RefuseJoin = false)
    {
        $Temp = $RefuseJoin ? 1 : 0;//真为不再接受加群申请
        $array = array(
            'Fun' => 'setGroupKick',
            'Group' => $Group,
            'QQ' => $QQ,
            'RefuseJoin' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置成员禁言
     * @param $Group
     * @param $QQ
     * @param int $Time
     * @return mixed
     */
    public function setGroupBan($Group, $QQ, $Time = 0)
    {
        $array = array(
            'Fun' => 'setGroupBan',
            'Group' => $Group,
            'QQ' => $QQ,
            'Time' => $Time//0为解除禁言
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置群管理员
     * @param $Group
     * @param $QQ
     * @param bool $Become
     * @return mixed
     */
    public function setGroupAdmin($Group, $QQ, $Become = false)
    {
        $Temp = $Become ? 1 : 0;//真为设置管理员,假为取消管理员
        $array = array(
            'Fun' => 'setGroupAdmin',
            'Group' => $Group,
            'QQ' => $QQ,
            'Become' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置全群禁言
     * @param $Group
     * @param bool $IsGag
     * @return mixed
     */
    public function setGroupWholeBan($Group, $IsGag = false)
    {
        $Temp = $IsGag ? 1 : 0;//真为开启,假为关闭
        $array = array(
            'Fun' => 'setGroupWholeBan',
            'Group' => $Group,
            'IsGag' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置群匿名设置
     * @param $Group
     * @param bool $Open
     * @return mixed
     */
    public function setGroupAnonymous($Group, $Open = false)
    {
        $Temp = $Open ? 1 : 0;//真为开启匿名,假为关闭
        $array = array(
            'Fun' => 'setGroupAnonymous',
            'Group' => $Group,
            'Open' => $Temp
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置群成员名片
     * @param $Group
     * @param $QQ
     * @param null $Card
     * @return mixed
     */
    public function setGroupCard($Group, $QQ, $Card = null)
    {
        $array = array(
            'Fun' => 'setGroupCard',
            'Group' => $Group,
            'QQ' => $QQ,
            'Card' => $Card//为空时清空群名片
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置讨论组退出
     * @param $Discuss
     * @return mixed
     */
    public function setDiscussLeave($Discuss)
    {
        $array = array(
            'Fun' => 'setDiscussLeave',
            'Group' => $Discuss
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置群添加请求
     * @param $responseFlag
     * @param $subtype
     * @param $type
     * @param null $Msg
     * @return mixed
     */
    public function setGroupAddRequest($responseFlag, $subtype, $type, $Msg = null)
    {
        $array = array(
            'Fun' => 'setGroupAddRequest',
            'responseFlag' => $responseFlag,
            'subtype' => $subtype,//  1/群添加,2/群邀请
            'type' => $type,//  1/通过,2/拒绝
            'Msg' => $Msg//拒绝时的理由
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置匿名群员禁言
     * @param $Group
     * @param $Anonymous
     * @param int $Time
     * @return mixed
     */
    public function setGroupAnonymousBan($Group, $Anonymous, $Time = 0)
    {
        $array = array(
            'Fun' => 'setGroupAnonymousBan',
            'Group' => $Group,
            'Anonymous' => $Anonymous,
            'Time' => $Time
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置好友添加请求
     * @param $responseFlag
     * @param $Type
     * @param null $Name
     * @return mixed
     */
    public function setFriendAddRequest($responseFlag, $Type, $Name = null)
    {
        $array = array(
            'Fun' => 'setFriendAddRequest',
            'responseFlag' => $responseFlag,
            'Type' => $Type,//  1/通过,2/拒绝
            'Name' => $Name//备注
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 置群成员专属头衔
     * @param $Group
     * @param $QQ
     * @param null $Tip
     * @param int $Time
     * @return mixed
     */
    public function setGroupSpecialTitle($Group, $QQ, $Tip = null, $Time = 0)
    {
        $array = array(
            'Fun' => 'setGroupSpecialTitle',
            'Group' => $Group,
            'QQ' => $QQ,
            'Tip' => $Tip,//头衔名称
            'Time' => $Time//过期时间
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Status'};
    }

    /**
     * 下载文件
     * @param $URL
     * @param null $Name
     * @param int $Type
     * @param null $MD5
     * @return mixed
     */
    public function downFile($URL, $Name = null, $Type = 1, $MD5 = null)
    {
        $array = array(
            'Fun' => 'downFile',
            'URL' => $URL,//文件的URL地址
            'Name' => $Name,//完整文件名,如果未传入则使用md5值
            'Type' => $Type,//文件类型,1为图片,2为音乐文件
            'MD5' => $MD5//用于校验文件完整性
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        $array = json_decode($Get);
        return $array->{'Result'};//返回文件的相对路径
    }

    /**
     * 获取图片信息
     * @param $FileName
     * @return string
     */
    public function getImageInfo($FileName)
    {
        $array = array(
            'Fun' => 'getImageInfo',
            'File' => $FileName//图片名,不带路径,并且必须是酷Q收到的图片
        );
        $Json = json_encode($array);
        $Get = $this->SendData($Json);
        return $Get;//返回带有数据的Json文本
    }

    /**
     * 发送qq消息
     * @param CoolQMsg $msgObj
     * @return mixed
     */
    public static function sendQqMsg(CoolQMsg $msgObj)
    {
        if (!$msgObj->isInvalidMsg()) {
            throw new \Exception('消息对象不合法');
        }
        if (is_array($msgObj->msg)) {
            $msg = implode("\n", $msgObj->msg);
        } else {
            $msg = $msgObj->msg;
        }

        $cqPlatForm = self::getCoolQ();
        switch ($msgObj->sendType) {
            case CoolQMsg::SEND_MSG_TYPE_GROUP:
                $res = $cqPlatForm->sendGroupMsg($msgObj->qqNO, $msg);
                break;
            case CoolQMsg::SEND_MSG_TYPE_DISCUSS:
                $res = $cqPlatForm->sendDiscussMsg($msgObj->qqNO, $msg);
                break;
            default:
                $res = $cqPlatForm->sendPrivateMsg($msgObj->qqNO, $msg);
        }
        unset($cqPlatForm);
        return $res;
    }
}