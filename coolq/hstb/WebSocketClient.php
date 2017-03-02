<?php
namespace hiilee\coolq\hstb;
/**
 * Created by PhpStorm.
 * User: hiilee
 * Date: 16/9/6
 * Time: 上午10:10
 */

class WebSocketClient
{
    private $_Socket = null;
    public $_ErrorStr = null;
    public $_ErrorNo = null;


    public function __construct($host, $port)
    {
        $this->_connect($host, $port);
    }

    public function __destruct()
    {
        fclose($this->_Socket);
    }

    public function sendData($data)
    {//send actual data:
        fwrite($this->_Socket, $this->encode($data));
        $wsData = fread($this->_Socket, 8192);
        $retData = trim($wsData, chr(0) . chr(255));
        return trim(substr($retData, stripos($retData, "\x01") + 1));
    }

    private function encode($data)
    {
        $data = is_array($data) || is_object($data) ? json_encode($data) : (string)$data;
        $len = strlen($data);
        $mask = array();
        for ($j = 0; $j < 4; $j++) {
            $mask[] = mt_rand(1, 255);
        }
        $head[0] = 129;
        if ($len <= 125) {
            $head[1] = $len;
        } elseif ($len <= 65535) {
            $split = str_split(sprintf('%016b', $len), 8);
            $head[1] = 126;
            $head[2] = bindec($split[0]);
            $head[3] = bindec($split[1]);
        } else {
            $split = str_split(sprintf('%064b', $len), 8);
            $head[1] = 127;
            for ($i = 0; $i < 8; $i++) {
                $head[$i + 2] = bindec($split[$i]);
            }
            if ($head[2] > 127) {
                return false;
            }
        }
        $head[1] += 128;
        $head = array_merge($head, $mask);
        foreach ($head as $k => $v) {
            $head[$k] = chr($v);
        }
        $mask_data = '';
        for ($j = 0; $j < $len; $j++) {
            $mask_data .= chr(ord($data[$j]) ^ $mask[$j % 4]);
        }
        return implode('', $head) . $mask_data;
    }

    /**
     * @param string $host
     * @param int $port
     * @return bool
     * @throws \Exception
     */
    private function _connect($host, $port)
    {
        $key1 = $this->_generateRandomString(32);
        $key2 = $this->_generateRandomString(32);
        $key3 = $this->_generateRandomString(8, false, true);
        $header = "GET ws://" . $host . ":" . $port . "/ HTTP/1.1\r\n";
        $header .= "Host: " . $host . ":" . $port . "\r\n";
        $header .= "Connection: Upgrade\r\n";
        $header .= "Upgrade: websocket\r\n";
        $header .= "Sec-WebSocket-Version: 13\r\n";
        $header .= "Sec-WebSocket-Key: " . $key1 . "\r\n";
        $header .= "\r\n";
        $this->_Socket = fsockopen($host, $port, $errno, $errstr, 2);
        if ($this->_Socket === false) {
            throw new \Exception($errno . ':' . $errstr);
        }
        fwrite($this->_Socket, $header);
        $response = fread($this->_Socket, 8192);
        return true;
    }

    private function _generateRandomString($length = 10, $addSpaces = true, $addNumbers = true)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $useChars = array();
        // select some random chars:
        for ($i = 0; $i < $length; $i++) {
            $useChars[] = $characters[mt_rand(0, strlen($characters) - 1)];
        }
        // add spaces and numbers:
        if ($addSpaces === true) {
            array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');
        }
        if ($addNumbers === true) {
            array_push($useChars, rand(0, 9), rand(0, 9), rand(0, 9));
        }
        shuffle($useChars);
        $randomString = trim(implode('', $useChars));
        $randomString = substr($randomString, 0, $length);
        return $randomString;
    }
}