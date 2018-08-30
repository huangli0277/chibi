<?php
namespace Org\Util;
class EthCommon
{

    protected $host, $port, $version;
    protected $id = 0;
    public $base = "1000000000000000000";//1e18 wei  基本单位
    //高精度函数参考http://www.jb51.net/article/80726.htm

    /**
     * 构造函数
     * Common constructor.
     * @param $host
     * @param string $port
     * @param string $version
     */
    function __construct($host, $port = "80",$version)
    {
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
    }


    /**
     * 发送请求
     * @author qiuphp2
     * @since 2017-9-21
     * @param $method
     * @param array $params
     * @return mixed
     */
    function request($method, $params = array())
    {
        $data = array();
        $data['jsonrpc'] = $this->version;
        $data['id'] = $this->id + 1;
        $data['method'] = $method;
        $data['params'] = $params;
        //@SaveLog('EthPayLocal', "params_".$method.json_encode($data), __FUNCTION__);
        //echo json_encode($data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);
        //echo "$ret".$ret;
        //返回结果
        if ($ret) {
            curl_close($ch);
            return json_decode($ret, true);
        } else {
            $error = curl_errno($ch);
            //echo $error;
            curl_close($ch);
            return false;
            //throw new Exception("curl出错，错误码:$error");
        }
    }

    /**
     * @author qiuphp2
     * @since 2017-9-21
     * @param $weiNumber 16进制wei单位
     * @return float|int 10进制eth单位【正常单位】
     */
    function fromWei($weiNumber)
    {
        $tenNumber = base_convert($weiNumber, 16, 10);
        //echo $tenNumber."<br/>";
        $ethNumber = bcdiv($tenNumber,$this->base,8);
        return $ethNumber;
    }

    function fromWei2($weiNumber)
    {
        $ethNumber = bcdiv($weiNumber,$this->base,8);//高精度浮点数相除
        return $ethNumber;
    }
    function fromWei3($weiNumber)
    {
        $tenNumber = base_convert($weiNumber, 16, 10);
        //echo $tenNumber."<br/>";
        $ethNumber = bcdiv($tenNumber,100000000,8);
        return $ethNumber;
    }

    /**
     * @author qiuphp2
     * @since 2017-9-21
     * @param $ethNumber 10进制eth单位
     * @return string    16进制wei单位
     */
    function toWei($ethNumber)
    {
        //echo "ethNumber".$ethNumber;
        //echo "base".$this->base;
        $tenNumer=bcmul($ethNumber,$this->base);//高精度浮点数相乘
        //echo $tenNumer;die();

        $weiNumber = base_convert($tenNumer, 10, 16);
        return  '0x'.$weiNumber;
    }
    function toWei3($ethNumber)
    {
        //echo "ethNumber".$ethNumber;
        //echo "base".$this->base;
        $tenNumer=bcmul($ethNumber,100000000);//高精度浮点数相乘
        //echo $tenNumer;die();

        $weiNumber = base_convert($tenNumer, 10, 16);
        return  '0x'.$weiNumber;
    }

    /**
     * @param $ethNumber
     * @return string  start 10  end 10
     */
    function toWei2($ethNumber)
    {
        $weiNumber = bcmul($ethNumber,$this->base);
        return $weiNumber;
    }

    /**
     * 判断是否是16进制
     * @author qiuphp2
     * @since 2017-9-21
     * @param $a
     * @return int
     */
    function assertIsHex($a)
    {
        if (ctype_xdigit($a)) {
            return true;
        } else {
            return false;
        }
    }


}
