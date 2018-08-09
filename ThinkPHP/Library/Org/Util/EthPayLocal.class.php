<?php
namespace Org\Util;
class EthPayLocal extends EthCommon
{



    function __construct($host, $port = "80",$version,$caiwu)
    {
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        $this->caiwu = $caiwu;
    }

    /**
     * 获得以太坊账号以太坊的余额
     * @author qiuphp2
     * @since 2017-9-21
     * @return float|int 返回eth数量 10进制
     */
    function eth_getBalance($account)
    {
        //echo 11;exit();
        //$account = $_REQUEST['account'];//获得账号公钥
        $params = [
            $account,
            "latest"
        ];
        $data = $this->request(__FUNCTION__, $params);
        if (empty($data['error']) && !empty($data['result'])) {
            return $this->fromWei($data['result']);//返回eth数量，自己做四舍五入处理
        } else {
            return $data['error']['message'];
        }
    }

    /**
     * 转账以太坊 coinpay
     * @author qiuphp2
     * @since 2017-9-15
     */
    function eth_sendTransaction($array)
    {
        $from = $this->caiwu;
        $to = $array["toaddress"];
        $value = $this->toWei($array["amount"]);
        //如果不是16进制 转化为16进制
        //$gas = $this->eth_estimateGas($from, $to, $value);//16进制 消耗的gas 0x5209
        $gas = "0xea60";//16进制 消耗的gas 0x5209
        $gasPrice = $this->eth_gasPrice();//价格 0x430e23400
        //$password = $data["user_id"];//解锁密码
        $password = COIN_KEY;//解锁密码
        $status = $this->personal_unlockAccount($from, $password);//解锁
        if (!$status) {
            //@SaveLog('EthPayLocal', "personal_unlockAccount_解锁失败", _FUNCTION__);
			//return "personal_unlockAccount_解锁失败";
			$data['error']['message'] = "personal_unlockAccount_解锁失败";
			return json_encode($data);
           return false;
        }
        $params = array(
            "from" => $from,
            "to" => $to,
            "gas" => $gas,//$gas,//2100
            "gasPrice " => $gasPrice,//18000000000
            "value" => $value,//2441406250
            "data" => "",
        );
        $data = $this->request(__FUNCTION__, [$params]);
		return json_encode($data);
        //@SaveLog('EthPayLocal', "eth_sendTransaction_" . json_encode($data), _FUNCTION__);
        //var_dump($data);
        if (empty($data['error']) && !empty($data['result'])) {
			//echo  "eth_success_" . $data['result'];
           // @SaveLog('EthPayLocal', "eth_success_" . $data['result'], _FUNCTION__);
          //  return $data['result'];//转账之后，生成HASH
			return $data['result'];
        } else {
			return json_encode($data);
            //@SaveLog('EthPayLocal', "eth_error_" . $data['error']['message'], _FUNCTION__);
            return false;
            //return $data['error']['message'];
        }
        //0x536135ef85aa8015b086e77ab8c47b8d40a3d00a975a5b0cc93b2a6345f538cd
    }

    /**
     * 定时任务查询是否有入账的以太坊 coinpay
     */
    public function EthInCrontab($oldnum = 0)
    {
        $ucinsert = $params = array();
        $n = $this->request("eth_blockNumber", $params);
        $number = $n['result'];
		$nums = base_convert($number, 16, 10);
		$isk = 1;
		$ucinsert = array();
		$ps = 5; 
		if($oldnum>0 && $nums>0){
			$ps = $nums - $oldnum;
			if($ps > 6){
				$ps = 6;
				$nums = $oldnum +5;
			}else{
				$ps = 5; 
			}
		}
		//$nums = 5353843;
        for ($i = 1; $i < $ps; $i++) {
			//echo $nums - $i ."<br>";
            $np = '0x' . base_convert($nums - $i, 10, 16);
            $params = array(
                $np,
                true
            );
            $data = $this->request("eth_getBlockByNumber", $params);
            if (isset($data["result"]) && isset($data["result"]["transactions"])) {
                foreach ($data["result"]["transactions"] as $k => $t) {
					$bs = base_convert($t["value"], 16, 10);
					$b = $this->fromWei2($bs);
					$ucinsert[$isk]["number"] = $b;
					$ucinsert[$isk]["hash"] = $t["hash"];
					$ucinsert[$isk]["from"] = $t["from"];
					$ucinsert[$isk]["to"] = $t["to"];
					$ucinsert[$isk]["input"] = $t["input"];
					$isk ++;
                }

            } else {
                continue;
            }
        }
		$ucinsert[0]["block"] = $nums;
		$ucinsert[0]["num"] = $ps;
        return $ucinsert;
    }

    public function EthInCrontab_back()
    {
        $params = array();
        $n = $this->request("eth_blockNumber", $params);
        $number = $n['result'];
        for ($i = 1; $i < 11; $i++) {
            $np = '0x' . base_convert($number - $i, 10, 16);
            $params = array(
                $np,
                true
            );
           // @SaveLog('EthPay', "TotalNumber" . $number - $i, _FUNCTION__);
            $data = $this->request("eth_getBlockByNumber", $params);
            if (isset($data["result"]) && isset($data["result"]["transactions"])) {
               // $config = Config::get('database');
                //$this->connection = Db::connect($config['AccessData']);
                foreach ($data["result"]["transactions"] as $k => $t) {
                    //$uc = $this->connection->table('user_coin')->where('tx', $t["hash"])->find();
					echo $t["to"]."<br>";
                    if (isset($uc["id"])) {
                        continue;
                    } else {
                       // $tc = $this->connection->table('user_token')->where('token_account', $t["to"])->find();
                        if (isset($tc["user_id"])) {
                            //@SaveLog('EthPayLocal', "Number" . $number - $i, _FUNCTION__);
                           // @SaveLog('EthPayLocal', "EthInCrontab" . json_encode($data["result"]["transactions"][$k]), _FUNCTION__);
                            $b = base_convert($t["value"], 16, 10);
                            $b = $this->fromWei2($b);
                            //$mod = new \app\model\Common();
                            //$mod::startTrans();
                          //  try {
                                //$this->connection->table("user_token")->where('id', $tc["id"])->setInc("token_balance", $b);
                                //插入
                                $ucinsert["user_id"] = $tc["user_id"];
                                $ucinsert["number"] = $b;
                                $ucinsert["tx"] = $t["hash"];
                                $ucinsert["token_out"] = $t["from"];
                                $ucinsert["status"] = 2;
                                $ucinsert["operate_id"] = 1;
                                $ucinsert["income"] = 1;
                                $ucinsert["token_type"] = 2;
                                $ucinsert["time"] = time();
                                $ucinsert["ip"] = $_SERVER["SERVER_ADDR"];
                                //$this->connection->table("user_coin")->insert($ucinsert);
                         //   } catch (\Exception $e) {
                                // 回滚事务
                                //$mod::rollback();
                        //    }
                            //echo $this->connection->table("user_token")->getLastSql();
                        } else {
                            continue;
                        }
                    }

                }

            } else {
                continue;
            }
        }

        return true;
    }

    /**
     * 定时任务查询是否有入账的以太坊 coinpay
     */
    public function EthInCrontabNumber($number)
    {
        $np = '0x' . base_convert($number, 10, 16);
        $params = array(
            $np,
            true
        );
        $data = $this->request("eth_getBlockByNumber", $params);
        if (isset($data["result"]) && isset($data["result"]["transactions"])) {
            $config = Config::get('database');
            $this->connection = Db::connect($config['AccessData']);
            foreach ($data["result"]["transactions"] as $k => $t) {
                $uc = $this->connection->table('user_coin')->where('tx', $t["hash"])->find();
                if (isset($uc["id"])) {
                    continue;
                } else {
                    $tc = $this->connection->table('user_token')->where('token_account', $t["to"])->find();
                    if (isset($tc["user_id"])) {
                        @SaveLog('EthPay', "Number" . $number, _FUNCTION__);
                        @SaveLog('EthPay', "EthInCrontabNumber" . json_encode($data["result"]["transactions"][$k]), _FUNCTION__);
                        $b = base_convert($t["value"], 16, 10);
                        $b = $this->fromWei2($b);
                        $mod = new \app\model\Common();
                        $mod::startTrans();
                        try {
                            $this->connection->table("user_token")->where('id', $tc["id"])->setInc("token_balance", $b);
                            //插入
                            $ucinsert["user_id"] = $tc["user_id"];
                            $ucinsert["number"] = $b;
                            $ucinsert["tx"] = $t["hash"];
                            $ucinsert["token_out"] = $t["from"];
                            $ucinsert["status"] = 2;
                            $ucinsert["operate_id"] = 1;
                            $ucinsert["income"] = 1;
                            $ucinsert["token_type"] = 2;
                            $ucinsert["time"] = time();
                            $ucinsert["ip"] = $_SERVER["SERVER_ADDR"];
                            $this->connection->table("user_coin")->insert($ucinsert);
                            // 提交事务
                            $mod::commit();
                        } catch (\Exception $e) {
                            // 回滚事务
                            $mod::rollback();
                        }
                        //echo $this->connection->table("user_token")->getLastSql();
                    } else {
                        continue;
                    }
                }

            }


        }

        return true;
    }


    /**
     * 转账以太坊ERC20代币 coinpay
     * @author qiuphp2
     * @since 2017-9-15
     */
    function eth_sendContactTransaction($array)
    {
        $from = $this->caiwu; //财务账号出账
        $heyue = $array["contact"];//合约地址
        //$value = "0x9184e72a";
        //$value = "0x" . base_convert("5000000000000000000", 10, 16);
        $value = $this->toWei($array["amount"]);
        //如果不是16进制 转化为16进制
//        if (!ctype_xdigit($value)) {
//            $value = $this->toWei($value);
//        }
        //$gas = $this->eth_estimateGas($from, $heyue, $value);//16进制 消耗的gas 0x5209
        $gas = base_convert("50000", 10, 16);
        $gas = "0x" . $gas;
        //echo $gas;die();
        $gasPrice = $this->eth_gasPrice();//价格 0x430e23400
        $password = $array["user_id"];//解锁密码
        $status = $this->personal_unlockAccount($from, $password);//解锁
        if (!$status) {
            return '解锁失败';
        }
        //参数组装
        //"dd62ed3e": "allowance(address,address)",
        //    "095ea7b3": "approve(address,uint256)",
        //    "cae9ca51": "approveAndCall(address,uint256,bytes)",
        //    "70a08231": "balanceOf(address)",
        //    "42966c68": "burn(uint256)",
        //    "79cc6790": "burnFrom(address,uint256)",
        //    "313ce567": "decimals()",
        //    "06fdde03": "name()",
        //    "95d89b41": "symbol()",
        //    "18160ddd": "totalSupply()",
        //    "a9059cbb": "transfer(address,uint256)",
        //    "23b872dd": "transferFrom(address,address,uint256)"

        $data_base = "0xa9059cbb";//70a08231
        $to = $array["toaddress"];//转出地址
        $to_data = substr($to, 2);
        $data_base .= "000000000000000000000000" . $to_data;
        $value_data = substr($value, 2);
        $v = str_pad($value_data, 64, 0, STR_PAD_LEFT);
        //echo $v;
        //die();
        $data_base .= $v;
        $params = array(
            "from" => $from,
            "to" => $heyue,
            //"gas" => $gas,//$gas,//2100
            //"gasPrice " => $gasPrice,//18000000000
            //"value" => $value,//2441406250
            "data" => $data_base,
        );
        @SaveLog('EthPayLocal', "eth_sendContactTransaction_" . json_decode($params), _FUNCTION__);
        $data = $this->request("params", [$params]);
        if (empty($data['error']) && !empty($data['result'])) {
            @SaveLog('EthPayLocal', 'Success_' . $data['result'], __FUNCTION__);
            return $data['result'];//转账之后，生成HASH
        } else {
            @SaveLog('EthPayLocal', 'ContactTransaction_' . $data['error']['message'], __FUNCTION__);
            return false;
            //return $data['error']['message'];
        }
        //0x536135ef85aa8015b086e77ab8c47b8d40a3d00a975a5b0cc93b2a6345f538cd
    }

    /**
     * 转账以太坊ERC20代币 测试代码
     * @author qiuphp2
     * @since 2017-9-15
     */
  function eth_ercsendTransaction($zhuan)
    {
        $from = $this->caiwu;//转出地址0.000000000000001014
		$to = $zhuan['toaddress'];
       $heyue = $zhuan['token'];//合约地址
        $password = COIN_KEY;//解锁密码
        //$value = "0x9184e72a";
          $value = $this->toWei($zhuan['amount']);
		if($zhuan['type']=="btm"){
          $value = $this->toWei3($zhuan['amount']);
		}
        //$gas = $this->eth_estimateGas($from, $heyue, $value);//16进制 消耗的gas 0x5209
        $gas = base_convert("50000", 10, 16);
        $gas = "0x" . $gas;
        //echo $gas;die();
        $gasPrice = $this->eth_gasPrice();//价格 0x430e23400
        $status = $this->personal_unlockAccount($from, $password);//解锁
        if (!$status) {
            return '解锁失败';
        }
        $data_base = "0xa9059cbb";//70a08231
        $to_data = substr($to, 2);
        $data_base .= "000000000000000000000000" . $to_data;
        $value_data = substr($value, 2);
        $v = str_pad($value_data, 64, 0, STR_PAD_LEFT);
        //echo $v;
        //die();
        $data_base .= $v;
        $params = array(
            "from" => $from,
            "to" => $heyue,
            //"gas" => $gas,//$gas,//2100
            //"gasPrice " => $gasPrice,//18000000000
            //"value" => $value,//2441406250
            "data" => $data_base,
        );

        $data = $this->request("eth_sendTransaction", [$params]);
		return json_encode($data);
        if (empty($data['error']) && !empty($data['result'])) {
            return $data;//转账之后，生成HASH
        } else {
            return $data['error']['message'];
        }
        //0x536135ef85aa8015b086e77ab8c47b8d40a3d00a975a5b0cc93b2a6345f538cd
    }

    //以太坊合约查询操作
    function eth_call()
    {
        $from = "0x64fcc62e4d2e7d09907b10ad5ed76c8503363e8a";//转出地址
        $to = "0x4c4d11f7ec61d0cff19a80bb513695bf12177398";//合约地址
        //参数组装
        //"dd62ed3e": "allowance(address,address)",
        //    "095ea7b3": "approve(address,uint256)",
        //    "cae9ca51": "approveAndCall(address,uint256,bytes)",
        //    "70a08231": "balanceOf(address)",
        //    "42966c68": "burn(uint256)",
        //    "79cc6790": "burnFrom(address,uint256)",
        //    "313ce567": "decimals()",
        //    "06fdde03": "name()",
        //    "95d89b41": "symbol()",
        //    "18160ddd": "totalSupply()",
        //    "a9059cbb": "transfer(address,uint256)",
        //    "23b872dd": "transferFrom(address,address,uint256)"

        $data_base = "0x70a08231";//70a08231
        $from_data = substr($from, 2);
        //echo $from_data;die();
        $data_base .= "000000000000000000000000" . $from_data;
        $params = array(
            "from" => $from,
            "to" => $to,
            "data" => $data_base,
        );

        $data = $this->request("eth_call", [$params, "latest"]);
        //var_dump($data);
        if (empty($data['error']) && !empty($data['result'])) {
            return $data['result'];//转账之后，生成HASH
        } else {
            return $data['error']['message'];
        }
    }

    /**根据转账hash获得转账详情
     * 转账详细信息
     * @author qiuphp2
     * @since 2017-9-20
     */
    function eth_getTransactionReceipt($transactionHash)
    {
        //交易hash
        $params = array(
            $transactionHash,
        );
        $data = $this->request(__FUNCTION__, $params);
        if (empty($data['error'])) {
            if (count($data['result']) == 0) {
                echo '等待确认';
            } else {
                if ($data['result']['status'] = "0x1") {
                    return $data['result']['blockHash'];//转账成功
                } else {
                    return "转账失败";
                }

                //return $data['result'];//转账成功了
            }
        } else {
            return $data['error']['message'];
        }
    }

    /** 需要
     * 新建账号 有点耗时 最好给用户生成的之后，密码保存在数据库里面
     * @author qiuphp2
     * @since 2017-9-19
     */
    function personal_newAccount($password)
    {
        //$password = "123";//密码
        $params = array(
            $password,
        );
        $data = $this->request(__FUNCTION__, $params);
		//return json_encode($data);
        if (empty($data['error']) && !empty($data['result'])) {
            //@SaveLog('EthPay', "account" . $data['result'], "personal_newAccount");
            return $data['result'];//新生成的账号公钥
        } else {
            //@SaveLog('EthPay', "password" . $password, "personal_newAccount");
            return false;
        }
    }

    /**
     * 获得消耗多少GAS
     * @author qiuphp2
     * @since 2017-9-15
     */
    function eth_estimateGas($from, $to, $value)
    {
        $params = array(
            "from" => $from,
            "to" => $to,
            "value" => $value
        );
        //echo "$value".$value;die();
        $data = $this->request(__FUNCTION__, [$params]);
        //var_dump($data);die();
        return $data['result'];
    }

    /**
     * 获得当前GAS价格
     * @author qiuphp2
     * @since 2017-9-15
     */
    function eth_gasPrice()
    {
        $params = array();
        $data = $this->request(__FUNCTION__, $params);
        return $data['result'];
    }


    /**需要
     * 解锁账号 此函数可能比较耗时
     * @author qiuphp2
     * @since 2017-9-15
     */
    function personal_unlockAccount($account, $password)
    {
        $params = array(
            $account,
            $password,
            100,
        );
        $data = $this->request(__FUNCTION__, $params);
        if (!empty($data['error'])) {
            return $data['error']['message'];//解锁失败
        } else {
            return $data['result'];//成功返回true
        }
    }

    function eth_getTransactionCount()
    {
        $params = array(
            "0x8d7c0440e01f4840aeafe4a9039b41e00f4157af",
            "latest"
        );
        $data = $this->request(__FUNCTION__, $params);
        var_dump($data);
        die();
        if (!empty($data['error'])) {
            return $data['error']['message'];//解锁失败
        } else {
            return $data['result'];//成功返回true
        }
    }

    function eth_getBlockByNumber()
    {
        $number = '0x' . base_convert('4569782', 10, 16);
        echo $number;
        $params = array(
            $number, // 436
            true
        );
        $data = $this->request(__FUNCTION__, $params);
        var_dump($data);
        var_dump($data["result"]["transactions"]["0"]);
        die();
        if (!empty($data['error'])) {
            return $data['error']['message'];//解锁失败
        } else {
            return $data['result'];//成功返回true
        }
    }


}

