<?php
namespace Home\Controller;

class FinanceController extends HomeController
{
    /**
     *手机端个人中心
     */
    public function index()
    {
        if (!userid()) {
            redirect('/login');
        }

        $CoinList = M('Coin')->where(array('status' => 1))->select();
        $UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
        }

        $cny['zj'] = 0;

        foreach ($CoinList as $k => $v) {
            if ($v['name'] == 'cny') {
                $cny['ky'] = round($UserCoin[$v['name']], 2) * 1;
                $cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
                $cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];
            }
            else {


                /* 				if ($Market[$v['name'] . '_cny']['new_price']) {
                                    $jia = $Market[$v['name'] . '_cny']['new_price'];
                                } */



                if ($Market[C('market_type')[$v['name']]]['new_price']) {
                    $jia = $Market[C('market_type')[$v['name']]]['new_price'];
                }
                else {
                    $jia = 1;
                }
                if(in_array($v['name'],C('coin_on'))){
                    $coinList[$v['name']] = array('name' => $v['name'], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                }

                $cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
            }
        }

        $this->assign('cny', $cny);
        $this->assign('coinList', $coinList);
        $this->assign('prompt_text', D('Text')->get_content('finance_index'));
        $this->display();
    }

    /**
     * 手机页面（我的账户）
     */
    public function index_list(){
        $this->display();
    }

    /**
     * 手机（我的资产）
     */
    public function details()
    {
        if (!userid()) {
            redirect('/login');
        }

        $CoinList = M('Coin')->where(array('status' => 1))->select();
        $UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
        }

        $cny['zj'] = 0;
        $cny['ky_zj'] = 0;
        foreach ($CoinList as $k => $v) {
            if ($v['name'] == 'cny') {
                $cny['ky'] = round($UserCoin[$v['name']], 2) * 1;//可用数量
                $cny['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;//冻结数量
                $cny['ky_zj'] = $cny['ky_zj'] + $cny['ky'];//计算预估总可用资产
                $cny['zj'] = $cny['zj'] + $cny['ky'] + $cny['dj'];//计算预估总资产
            }
            else {
                if($Market[C('market_type')[$v['name']]]['status'] != 1){
                    continue;
                }
                if ($Market[C('market_type')[$v['name']]]['new_price']) {
                    $jia = $Market[C('market_type')[$v['name']]]['new_price'];
                } else {
                    $jia = 1;
                }

                if(in_array($v['name'],C('coin_on'))){
                    $coinList[$v['name']] = array(
                        'zr_jz' => $v['zr_jz'],
                        'zc_jz' => $v['zc_jz'],
                        'name' => $v['name'],
                        'img' => $v['img'],
                        'title' => $v['title'] . '(' . strtoupper($v['name']) . ')',
                        'url' => $v['name'].'_cnyt',
                        'xnb' => round($UserCoin[$v['name']], 6) * 1,//可用数量
                        'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1,//冻结数量
                        'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6),
                        'jia' => $jia * 1,
                        'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                }

                $cny['ky_zj'] = round($cny['ky_zj'] + ($UserCoin[$v['name']] * $jia), 2) * 1;//计算预估总可用资产
                $cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;//计算预估总资产
            }
        }
        $this->assign('cny', $cny);
        $this->assign('coinList', $coinList);
        $this->assign('prompt_text', D('Text')->get_content('finance_index'));
        $this->display();
    }

    public function asset_detail($coin = 'btc'){
        if($coin){
            if (!userid()) {
                redirect('/Login');
            }
            $Market = M('Market')->where(array('status' => 1,'name'=>$coin.'_cny'))->find();
            $UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
            $jia = $Market['new_price'];
            if(in_array($coin,C('coin_on'))){
                $coin_info = [
                    'num' => $UserCoin[$coin],
                    'num_d' => $UserCoin[$coin . "d"],
                    'zh' => round((($UserCoin[$coin] + $UserCoin[$coin . 'd']) * $jia), 2) * 1];
            }
            $where['userid'] = userid();
            $where['coinname'] = $coin;
            $Moble = M('Myzr');
            //查询充币
            $list1 = $Moble->field("*,IF(id IS NOT NULL,1,1) AS direction")->where($where)->order('id desc')->select();
            $Moble = M('Myzc');
            //查询提币
            $list2 = $Moble->field("*,IF(id IS NOT NULL,2,2) AS direction")->where($where)->order('id desc')->select();
            if($list1 && $list2) {
                $list = array_merge($list1, $list2);
                $times = array();
                foreach ($list as $value) {
                    $times[] = $value['addtime'];
                }
                array_multisort($times, SORT_DESC, $list);
            }else{
                $list = array_merge($list1, $list2);
            }
            $this->assign('coin_info',$coin_info);
            $this->assign('xnb', $coin);
            $this->assign('list', $list);
            $this->display();
        }
    }

    /**
     * 邀请反佣页面
     */
    public function promotion()
    {
        if (!userid()) {
            redirect('/Login');
        }

//        $this->assign('prompt_text', D('Text')->get_content('finance_mytj'));
//        check_server();
        $user = M('User')->where(array('id' => userid()))->find();

        if (!$user['invit']) {
            for (; true;) {
                $tradeno = tradenoa();

                if (!M('User')->where(array('invit' => $tradeno))->find()) {
                    break;
                }
            }

            M('User')->where(array('id' => userid()))->save(array('invit' => $tradeno));
            $user = M('User')->where(array('id' => userid()))->find();
        }
        //获取奖励排序
        $yqlist = M('FenhongMyyq')->alias('f')->field('u.username,SUM(f.num) as num')
            ->join("__USER__ as u on u.id = f.userid","LEFT")
            ->order('SUM(f.num) desc')
            ->group('f.userid')
            ->page(1,3)
            ->select();
        foreach ($yqlist as $key=>$value){
            $yqlist[$key]['num']=round($value['num'],2);
        }
        $this->assign('yqlist', $yqlist);
        $this->assign('user', $user);
        $this->display();
    }


    public function myc2c($type = NULL, $num = NULL, $tpl = NULL)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }
        $userid = userid();
        $time = time();
        if ($tpl == 1) {
            if ($num < 500) $this->error('最低买入500.00 CNYT！');
            if ($num % 100) $this->error('买入CNYT需为100的倍数！');
            $pri = $num * 1;
        } elseif ($tpl == 2) {
            //$this->error('C2C维护中！');
            $user = M('User')->where(array('id' => userid()))->find();
            if (!$user['idcardauth']) {
                $this->error('您还没有认证，请先认证！');
            }
            $isc = M()->query("SELECT cid FROM  a_ctc where uid = '$userid' and type = '1' and stu = 2; ");
            $isz = M()->query("SELECT id FROM  qq3479015851_myzr where userid = '$userid' ; ");
            if (!$isc && !$isz) $this->error('未查询到您的充值记录，卖出失败！');

            //检验是否绑定了银行卡
            if ($num >= 10000) {
                $iscard = M()->query("SELECT id, bankcard, bank FROM qq3479015851_user_bank where status = 1 and bank like '%银行%' and bankcard is not null limit 1");
                if (!$iscard)
                    $this->error('卖出超过10000.00 CNYT必须绑定银行卡！');
            }
            if ($num < 500) $this->error('最低卖出500.00 CNYT！');
            if ($num % 100) $this->error('卖出CNYT需为100的倍数！');
            $pri = $num * 0.995;
            $user_coin = M('UserCoin')->where(array('userid' => $userid))->find();
            if ($user_coin['cny'] < $num) $this->error('可卖出余额不足！');
        } else {
            $this->error('参数错误！');
        }

        $mycz = M()->query("SELECT cid FROM  a_ctc where uid = '$userid' and type = '$tpl' and stu = 1; ");
        if ($mycz) {
            $this->error('您有未完成的订单！' . $userid);
        }

        if (!check($num, 'cny')) {
            $this->error('交易金额格式错误！');
        }

        if (100000 < $num) {
            $this->error('交易金额不能大于100000元！');
        }

        if ($type == "alipay") {
            $type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank = '支付宝' and userid='" . userid() . "' ");
            if (!$type1) $this->error('您还没有绑定支付宝！');
            $myczType = M('MyczType')->where(array('title' => '支付宝', 'status' => 1))->select();
        } elseif ($type == "bank") {
            $type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%银行%' and userid='" . userid() . "' ");
            if (!$type1) $this->error('您还没有绑定银行卡！');
            $myczType = M('MyczType')->where(array('title' => '银行卡', 'status' => 1))->select();
        } elseif ($type == "weixin") {
            $this->error('交易方式错误！');
            if ($tpl == 2) $this->error('交易方式错误！');
            $type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%微信%' and userid='" . userid() . "' ");
            if (!$type1) $this->error('您还没有绑定微信！');
            $myczType = M('MyczType')->where(array('title' => '微信', 'status' => 1))->select();
        } else {
            $this->error('交易方式不存在1！');
        }

        if (!$myczType) {
            $this->error('交易方式不存在2！');
        }
        $sjs = rand(0, count($myczType) - 1);
        $tid = $myczType[$sjs]['id'];
        if (!$tid) {
            $this->error('交易方式不存在3！');
        }

        if ($tpl == 2) {
            M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cny` = cny-$num,`cnyd` =  cnyd+$num WHERE `userid` ='$userid';");
            M()->execute("INSERT INTO `a_ctc` (`cid`, `uid`, `pri`, `num`, `tid`, `type`, `typec`, `typer`, `uptime`) VALUES (NULL, '$userid', '$pri', '$num',  '$tid','2', '1', '1', '$time');");
        } else {
            M()->execute("INSERT INTO `a_ctc` (`cid`, `uid`, `pri`, `num`, `tid`, `type`, `typec`, `typer`, `uptime`) VALUES (NULL, '$userid', '$pri', '$num',  '$tid','1', '1', '1', '$time');");
        }
        $this->success('交易订单创建成功！');

        if ($mycz) {
            $this->success('交易订单创建成功！');
        } else {
            $this->error('提现订单创建失败！');
        }
    }

    public function mycz($status = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }
       // $otime = time() - 48 * 60 * 60;
        $userid = userid();
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin['cny'] = round($user_coin['cny'], 2);
        $user_coin['cnyd'] = round($user_coin['cnyd'], 2);
        $this->assign('user_coin', $user_coin);

        $list = M()->query("SELECT * FROM  a_ctc a,qq3479015851_mycz_type b where a.tid = b.id and a.uid = '$userid' order by a.cid desc limit 0,10; ");//大于时间范围 and a.uptime > $otime
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $list[$k]['tpl'] = '买入';
                $list[$k]['status'] = $v['typer'];
            } else {
                $list[$k]['tpl'] = '卖出';
                $list[$k]['status'] = $v['typec'];
            }
            if ($v['typer'] == 1 && $v['typec'] == 1) $list[$k]['pp'] = 1;
            if ($v['stu'] == 2) $list[$k]['status'] = 99;
            if ($v['stu'] == 0) $list[$k]['status'] = 0;
        }
        $this->assign('list', $list);
        $this->display();
    }

    public function mycz_list(){
        if (!userid()) {
            redirect('/Login');
        }
        // $otime = time() - 48 * 60 * 60;
        $userid = userid();

        $count = M()->query("SELECT count(*) as count FROM  a_ctc a,qq3479015851_mycz_type b where a.tid = b.id and a.uid = '$userid' order by a.cid desc; ")[0]['count'];
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M()->query("SELECT * FROM  a_ctc a,qq3479015851_mycz_type b where a.tid = b.id and a.uid = ".$userid." order by a.cid desc limit ".$Page->firstRow .",".$Page->listRows.";");//大于时间范围 and a.uptime > $otime
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $list[$k]['tpl'] = '买入';
                $list[$k]['status'] = $v['typer'];
            } else {
                $list[$k]['tpl'] = '卖出';
                $list[$k]['status'] = $v['typec'];
            }
            if ($v['typer'] == 1 && $v['typec'] == 1) $list[$k]['pp'] = 1;
            if ($v['stu'] == 2) $list[$k]['status'] = 99;
            if ($v['stu'] == 0) $list[$k]['status'] = 0;
        }
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    public function mycz_del($id = 0)
    {
        if (!userid()) {
            redirect('/Login');
        }
        $userid = userid();
        if ($id > 0) {
            $mycz = M()->query("SELECT * FROM  a_ctc where uid = '$userid' and cid = '$id' and stu = 1; ");
            if ($mycz) {
                if ($mycz[0]['type'] == 1) {
                    M()->execute("UPDATE  `a_ctc` SET  `typer` = 0,stu = 0 WHERE `cid` ='$id';");
                } else {
                    $this->error('卖单不可撤销！');
//					M()->execute("UPDATE  `a_ctc` SET  `typec` = 0,stu = 0 WHERE `cid` ='$id';");
//					$num = $mycz[0]['num'];
//					M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cny` = cny+$num,`cnyd` =  cnyd-$num WHERE `userid` ='$userid';");
                }
                $this->success('操作成功！');
            } else {
                $this->error('操作失败！');
            }

        }
    }

    public function mycz_q($id)
    {
        if (!userid()) {
            redirect('/Login');
        }
        $userid = userid();
        $mycz = M()->query("SELECT * FROM  a_ctc where uid = '$userid' and cid = '$id' and stu = 1; ");
        if ($mycz) {
            if ($mycz[0]['type'] == 1) {
                M()->execute("UPDATE  `a_ctc` SET  `typer` = 2 WHERE `cid` ='$id';");
            } else {
                $this->error('操作失败！');
//				M()->execute("UPDATE  `a_ctc` SET  `typec` = 2,stu=2 WHERE `cid` ='$id';");
//				$num = $mycz[0]['num'];
//				M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnyd` =  cnyd-$num WHERE `userid` ='$userid';");

            }
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }

    public function bank()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $UserBankType = M('UserBankType')->where(array('status' => 1))->order('id desc')->select();
        $this->assign('UserBankType', $UserBankType);
        $truename = M('User')->where(array('id' => userid()))->getField('truename');
        $this->assign('truename', $truename);
        $userbank = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%银行%' and userid='" . userid() . "' order by id desc LIMIT 1");
        $useralipay = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%支付宝%' and userid='" . userid() . "' order by id desc LIMIT 1");
        $userweixin = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%微信%' and userid='" . userid() . "' order by id desc LIMIT 1");
        $useralipay[0]['backimg'] = $useralipay[0]['backimg'] ? "Upload/pay/" . $useralipay[0]['backimg'] : "Upload/pay/alipay.jpg";
        $userweixin[0]['backimg'] = $userweixin[0]['backimg'] ? "Upload/pay/" . $userweixin[0]['backimg'] : "Upload/pay/weixin.jpg";
        if ($useralipay[0]['imgm']) {
            $useralipay[0]['backimg'] = WAP_URL . $useralipay[0]['backimg'];
        } else {
            $useralipay[0]['backimg'] = PC_URL . $useralipay[0]['backimg'];
        }
        if ($userweixin[0]['imgm']) {
            $userweixin[0]['backimg'] = WAP_URL . $userweixin[0]['backimg'];
        } else {
            $userweixin[0]['backimg'] = PC_URL . $userweixin[0]['backimg'];
        }
//        print_r($userbank[0]);
//        print_r($useralipay[0]);
//        print_r($userweixin[0]);
//        exit();
        $this->assign('userbank', $userbank[0]);
        $this->assign('useralipay', $useralipay[0]);
        $this->assign('userweixin', $userweixin[0]);
        $this->assign('prompt_text', D('Text')->get_content('user_bank'));
        $this->display();
    }

    public function address(){
        if (!userid()) {
            redirect('/Login');
        }
        $this->display();
    }

    public function bankup($bank = '', $bankaddr = '', $bankcard = '', $bankpwd = '', $type = '', $bankimg = '')
    {
        if (!userid()) {
            redirect('/Login');
        }
        if (!$bankpwd) {
            $this->error('支付密码格式错误！');
        }
        if ($type == 1) {
            $if_userbank1 = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%银行%' and userid='" . userid() . "' order by id desc LIMIT 1");
            if ($if_userbank1) $this->error('上传过的银行卡不可修改');
            if (strlen($bankcard) < 16 || strlen($bankcard) > 19) {
                $this->error('银行卡号格式错误！');
            }
            if (!$bankaddr) {
                $this->error('开户支行格式错误！');
            }
            $userbank = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%银行%' and userid='" . userid() . "' order by id desc LIMIT 1");
        } elseif ($type == 2) {
            $if_userbank2 = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%支付宝%' and userid='" . userid() . "' order by id desc LIMIT 1");
            if ($if_userbank2) $this->error('上传过的支付宝不可修改');
            if (strlen($bankcard) < 5) {
                $this->error('支付宝格式错误！');
            }
            if (!$bankimg) {
                $this->error('请上传支付宝收款码！');
            }
            $bank = "支付宝";
            $userbank = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%支付宝%' and userid='" . userid() . "' order by id desc LIMIT 1");
        } elseif ($type == 3) {
            $if_userbank3 = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%微信%' and userid='" . userid() . "' order by id desc LIMIT 1");
            if ($if_userbank3) $this->error('上传过的微信不可修改');
            if (strlen($bankcard) < 2) {
                $this->error('微信格式错误！');
            }
            if (!$bankimg) {
                $this->error('请上传微信收款码！');
            }
            $bank = "微信";
            $userbank = M()->query("SELECT id FROM  qq3479015851_user_bank where bank like '%微信%' and userid='" . userid() . "' order by id desc LIMIT 1");
        }
        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');
        if ($bankpwd != $user_paypassword) {
            $this->error('交易密码错误！');
        }
        if ($userbank) {
            $bankid = $userbank[0]['id'];
            M()->execute("UPDATE  `qq3479015851_user_bank` SET  `bank` = '$bank',`bankaddr` =  '$bankaddr',`bankcard` =  '$bankcard',`backimg` =  '$bankimg',`imgm` =  '0' WHERE `id` ='$bankid';");
            $this->success('更新成功！');
        } else {

            if (M('UserBank')->add(array('userid' => userid(), 'name' => $bank, 'bank' => $bank, 'bankprov' => 0, 'bankcity' => 0, 'bankaddr' => $bankaddr, 'backimg' => $bankimg, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1))) {
                $this->success('添加成功！');
            } else {
                $this->error('添加失败！');
            }

        }


    }


    public function upbank($name = NULL, $bank = NULL, $bankprov = NULL, $bankcity = NULL, $bankaddr = NULL, $bankcard = NULL, $paypassword = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }

        if (!check($name, 'a')) {
            $this->error('备注名称格式错误！');
        }

        if (!check($bank, 'a')) {
            $this->error('开户银行格式错误！');
        }

//		if (!check($bankprov, 'c')) {
//			$this->error('开户省市格式错误！');
//		}
//
//		if (!check($bankcity, 'c')) {
//			$this->error('开户省市格式错误2！');
//		}

        if (!check($bankaddr, 'a')) {
            if ($bank != '支付宝') $this->error('开户行地址格式错误！');
        }

        if (!check($bankcard, 'd')) {
            if ($bank != '支付宝') $this->error('银行账号格式错误！');
        }

        if (strlen($bankcard) < 16 || strlen($bankcard) > 19) {

            if ($bank != '支付宝') $this->error('银行账号格式错误！');

        }
        if ($bank == "支付宝" && strlen($bankcard) < 5) {
            $this->error('支付宝账号格式错误！');
        }


        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if ($paypassword != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('UserBankType')->where(array('title' => $bank))->find()) {
            $this->error('开户银行错误！');
        }
        if ($bank == "支付宝") {
            $type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank = '支付宝' and userid='" . userid() . "' ");
            if ($type1) $this->error('支付宝已添加！');
        } else {
            $type1 = M()->query("SELECT * FROM  qq3479015851_user_bank where bank like '%银行%' and userid='" . userid() . "' ");
            if ($type1) $this->error('银行卡已添加！');
        }
        $userBank = M('UserBank')->where(array('userid' => userid()))->select();

        foreach ($userBank as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('请不要使用相同的备注名称！');
            }

            if ($v['bankcard'] == $bankcard) {
                $this->error('银行卡号已存在！');
            }
            if ($v['bank'] == $bank) {
                $this->error($bank . '卡已存在！');
            }

        }

        if (2 <= count($userBank)) {
            $this->error('每个用户最多只能添加2个账户！');
        }


        if (M('UserBank')->add(array('userid' => userid(), 'name' => $name, 'bank' => $bank, 'bankprov' => $bankprov, 'bankcity' => $bankcity, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1))) {
            $this->success('银行添加成功！');
        } else {
            $this->error('银行添加失败！');
        }
    }

    public function delbank($id, $paypassword)
    {

        if (!userid()) {
            redirect('/Login');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (!check($id, 'd')) {
            $this->error('参数错误！');
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if ($paypassword != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('UserBank')->where(array('userid' => userid(), 'id' => $id))->find()) {
            $this->error('非法访问！');
        } else if (M('UserBank')->where(array('userid' => userid(), 'id' => $id))->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function myczChakan($id = NULL)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($id, 'd')) {
            $this->error('参数错误！');
        }

        $mycz = M('Mycz')->where(array('id' => $id))->find();

        if (!$mycz) {
            $this->error('充值订单不存在！');
        }

        if ($mycz['userid'] != userid()) {
            $this->error('非法操作！');
        }

        if ($mycz['status'] != 0) {
            $this->error('订单已经处理过！');
        }

        $rs = M('Mycz')->where(array('id' => $id))->save(array('status' => 3));

        if ($rs) {
            $this->success('', array('id' => $id));
        } else {
            $this->error('操作失败！');
        }
    }

    public function myczUp($type, $num, $tpl = NULL)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        $mycz = M('Mycz')->where(array('userid' => userid(), 'status' => 0))->find();
        if ($mycz) {
            $this->error('您有未完成的订单！');
        }
        $tpl = $tpl ? 2 : 1;

        if (!check($type, 'n')) {
            $this->error('交易方式格式错误！');
        }

        if (!check($num, 'cny')) {
            $this->error('充值金额格式错误！');
        }
        if ($num < 100) {
            $this->error('充值金额不能小于100元！');
        }

        if (100000 < $num) {
            $this->error('充值金额不能大于100000元！');
        }

        if ($type == "alipay") {
            $myczType = M('MyczType')->where(array('title' => '支付宝', 'status' => 1))->select();
        } elseif ($type == "bank") {
            $myczType = M('MyczType')->where(array('title' => '银行卡', 'status' => 1))->select();
        } else {
            $this->error('交易方式不存在1！');
        }

        if (!$myczType) {
            $this->error('交易方式不存在2！');
        }
        $sjs = rand(0, count($myczType) - 1);
        $tid = $myczType[$sjs]['id'];
        if (!$tid) {
            $this->error('交易方式不存在3！');
        }
        for (; true;) {
            $tradeno = tradeno();
            if (!M('Mycz')->where(array('tradeno' => $tradeno))->find()) {
                break;
            }
        }
        $mycz = M('Mycz')->add(array('userid' => userid(), 'num' => $num, 'type' => $type, 'tid' => $tid, 'tpl' => $tpl, 'tradeno' => $tradeno, 'addtime' => time(), 'status' => 0));

        if ($mycz) {
            $this->success('交易订单创建成功！', array('id' => $mycz));
        } else {
            $this->error('提现订单创建失败！');
        }
    }

    public function mytxUp($moble_verify, $num, $paypassword, $type)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($moble_verify, 'd')) {
            $this->error('短信验证码格式错误！');
        }

        if (!check($num, 'd')) {
            $this->error('提现金额格式错误！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (!check($type, 'd')) {
            $this->error('提现方式格式错误！');
        }
        $this->_verify_count_check($moble_verify,session('mytx_verify'));

        $userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($userCoin['cny'] < $num) {
            $this->error('可用人民币余额不足！');
        }

        $user = M('User')->where(array('id' => userid()))->find();

        if ($paypassword != $user['paypassword']) {
            $this->error('交易密码错误！');
        }

        $userBank = M('UserBank')->where(array('id' => $type))->find();

        if (!$userBank) {
            $this->error('提现地址错误！');
        }

        $mytx_min = (C('mytx_min') ? C('mytx_min') : 1);
        $mytx_max = (C('mytx_max') ? C('mytx_max') : 1000000);
        $mytx_bei = C('mytx_bei');
        $mytx_fee = C('mytx_fee');

        if ($num < $mytx_min) {
            $this->error('每次提现金额不能小于' . $mytx_min . '元！');
        }

        if ($mytx_max < $num) {
            $this->error('每次提现金额不能大于' . $mytx_max . '元！');
        }

        if ($mytx_bei) {
            if ($num % $mytx_bei != 0) {
                $this->error('每次提现金额必须是' . $mytx_bei . '的整倍数！');
            }
        }

        $fee = round(($num / 100) * $mytx_fee, 2);
        $mum = round(($num / 100) * (100 - $mytx_fee), 2);
        $mo = M();
        $mo->execute('set autocommit=0');
        //$mo->execute('lock tables qq3479015851_mytx write , qq3479015851_user_coin write ,qq3479015851_finance write');
        $rs = array();
        $finance = $mo->table('qq3479015851_finance')->where(array('userid' => userid()))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->find();
        $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec('cny', $num);
        $rs[] = $finance_nameid = $mo->table('qq3479015851_mytx')->add(array('userid' => userid(), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'name' => $userBank['name'], 'truename' => $user['truename'], 'bank' => $userBank['bank'], 'bankprov' => $userBank['bankprov'], 'bankcity' => $userBank['bankcity'], 'bankaddr' => $userBank['bankaddr'], 'bankcard' => $userBank['bankcard'], 'addtime' => time(), 'status' => 0));
        $finance_mum_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->find();
        $finance_hash = md5(userid() . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mum . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'auth.qq3479015851.com');
        $finance_num = $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'];

        if ($finance['mum'] < $finance_num) {
            $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
        } else {
            $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
        }

        $rs[] = $mo->table('qq3479015851_finance')->add(array('userid' => userid(), 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $num, 'type' => 2, 'name' => 'mytx', 'nameid' => $finance_nameid, 'remark' => '人民币提现-申请提现', 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

        if (check_arr($rs)) {
            session('mytx_verify', null);
            $mo->execute('commit');
            //$mo->execute('unlock tables');
            session('mytx_verify',null);
            $this->success('提现订单创建成功！');
        } else {
            $mo->execute('rollback');
            $this->error('提现订单创建失败！');
        }
    }

    public function mytxChexiao($id)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($id, 'd')) {
            $this->error('参数错误！');
        }

        $mytx = M('Mytx')->where(array('id' => $id))->find();

        if (!$mytx) {
            $this->error('提现订单不存在！');
        }

        if ($mytx['userid'] != userid()) {
            $this->error('非法操作！');
        }

        if ($mytx['status'] != 0) {
            $this->error('订单不能撤销！');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        //$mo->execute('lock tables qq3479015851_user_coin write,qq3479015851_mytx write,qq3479015851_finance write');
        $rs = array();
        $finance = $mo->table('qq3479015851_finance')->where(array('userid' => $mytx['userid']))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => $mytx['userid']))->find();
        $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $mytx['userid']))->setInc('cny', $mytx['num']);
        $rs[] = $mo->table('qq3479015851_mytx')->where(array('id' => $mytx['id']))->setField('status', 2);
        $finance_mum_user_coin = $mo->table('qq3479015851_user_coin')->where(array('userid' => $mytx['userid']))->find();
        $finance_hash = md5($mytx['userid'] . $finance_num_user_coin['cny'] . $finance_num_user_coin['cnyd'] . $mytx['num'] . $finance_mum_user_coin['cny'] . $finance_mum_user_coin['cnyd'] . MSCODE . 'auth.qq3479015851.com');
        $finance_num = $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'];

        if ($finance['mum'] < $finance_num) {
            $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
        } else {
            $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
        }

        $rs[] = $mo->table('qq3479015851_finance')->add(array('userid' => $mytx['userid'], 'coinname' => 'cny', 'num_a' => $finance_num_user_coin['cny'], 'num_b' => $finance_num_user_coin['cnyd'], 'num' => $finance_num_user_coin['cny'] + $finance_num_user_coin['cnyd'], 'fee' => $mytx['num'], 'type' => 1, 'name' => 'mytx', 'nameid' => $mytx['id'], 'remark' => '人民币提现-撤销提现', 'mum_a' => $finance_mum_user_coin['cny'], 'mum_b' => $finance_mum_user_coin['cnyd'], 'mum' => $finance_mum_user_coin['cny'] + $finance_mum_user_coin['cnyd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

        if (check_arr($rs)) {
            $mo->execute('commit');
            //$mo->execute('unlock tables');
            $this->success('操作成功！');
        } else {
            $mo->execute('rollback');
            $this->error('操作失败！');
        }
    }

    public function myzr($coin = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_myzr'));

        if (C('coin')[$coin]) {
            $coin = trim($coin);
        } else {
            $coin = C('xnb_mr');
        }

        $this->assign('xnb', $coin);
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'cny')
        ))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin[$coin] = round($user_coin[$coin], 6);
        $this->assign('user_coin', $user_coin);
        $Coin = M('Coin')->where(array('name' => $coin))->find();
        $this->assign('zr_jz', $Coin['zr_jz']);


        $qq3479015851_getCoreConfig = qq3479015851_getCoreConfig();
        if (!$qq3479015851_getCoreConfig) {
            $this->error('核心配置有误');
        }

        $this->assign("qq3479015851_opencoin", $qq3479015851_getCoreConfig['qq3479015851_opencoin']);

        if ($qq3479015851_getCoreConfig['qq3479015851_opencoin'] == 1) {

            if (!$Coin['zr_jz']) {
                $qianbao = '当前币种禁止转入！';
            } else {
                $qbdz = $coin . 'b';

                if (!$user_coin[$qbdz]) {
                    if ($Coin['type'] == 'rgb') {
                        $qianbao = md5(username() . $coin);
                        $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));

                        if (!$rs) {
                            $this->error('生成钱包地址出错！');
                        }
                    }
                    //eth QQ357898628
                    if ($Coin['type'] == 'eth') {
                        $heyue = $Coin['dj_yh'];//合约地址
                        $EthCommon = new \Org\Util\EthCommon(COIN_ADDR, COIN_PORT, "2.0");
                        $EthPayLocal = new \Org\Util\EthPayLocal(COIN_ADDR, COIN_PORT, "2.0", COIN_CAIWU);
                        if (!$heyue) {
                            //eth
                            //调用接口生成新钱包地址
                            $qianbao = $EthPayLocal->personal_newAccount(COIN_KEY);
                            if ($qianbao) {
                                $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));
                            } else {
                                $this->error('生成钱包地址出错2！');
                            }
                        } else {
                            //eth合约
                            $rs1 = M('UserCoin')->where(array('userid' => userid()))->find();
                            if ($rs1['ethb']) {
                                $qianbao = $rs1['ethb'];
                                $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));
                            } else {
                                //调用接口生成新钱包地址
                                $qianbao = $EthPayLocal->personal_newAccount(COIN_KEY);
                                if ($qianbao) {
                                    $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao, "ethb" => $qianbao));
                                } else {
                                    $this->error('生成钱包地址出错2！');
                                }

                            }
                        }

                    }
                    //eth QQ357898628
                    if ($Coin['type'] == 'qbb') {
                        $dj_username = $Coin['dj_yh'];
                        $dj_password = $Coin['dj_mm'];
                        $dj_address = $Coin['dj_zj'];
                        $dj_port = $Coin['dj_dk'];
                        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                        $json = $CoinClient->getinfo();

                        if (!isset($json['version']) || !$json['version']) {
                            $this->error('钱包链接失败！');
                        }

                        $qianbao_addr = $CoinClient->getaddressesbyaccount(username());

                        if (!is_array($qianbao_addr)) {
                            $qianbao_ad = $CoinClient->getnewaddress(username());

                            if (!$qianbao_ad) {
                                $this->error('生成钱包地址出错1！');
                            } else {
                                $qianbao = $qianbao_ad;
                            }
                        } else {
                            $qianbao = $qianbao_addr[0];
                        }

                        if (!$qianbao) {
                            $this->error('生成钱包地址出错2！');
                        }

                        $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $qianbao));

                        if (!$rs) {
                            $this->error('钱包地址添加出错3！');
                        }
                    }
                } else {
                    $qianbao = $user_coin[$coin . 'b'];
                }
            }
        } else {

            if (!$Coin['zr_jz']) {
                $qianbao = '当前币种禁止转入！';
            } else {
                $qianbao = $Coin['qq3479015851_coinaddress'];

                $moble = M('User')->where(array('id' => userid()))->getField('moble');

                if ($moble) {
                    $moble = substr_replace($moble, '****', 3, 4);
                } else {
                    redirect(U('Home/User/moble'));
                    exit();
                }

                $this->assign('moble', $moble);


            }

        }


        $this->assign('qianbao', $qianbao);
        $where['userid'] = userid();
        $where['coinname'] = $coin;
        $Moble = M('Myzr');
        $count = $Moble->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 我的推荐
     */
    public function mywd()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mywd'));
        check_server();
        $where['invit_1'] = userid();
        $Model = M('User');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);




        if ($_GET['p'] == '') {
            $_GET['p'] = 1;
        }
        $uppage=$_GET['p']-1;
        $dopage=$_GET['p']+1;
        if ($count <= 10) {
            $Page->setConfig('theme', '');
        } else if ($_GET['p'] == 1) {
            $Page->setConfig('theme', '<a href="/Finance/mywd/p/'.$dopage.'">»</a>');
        } else if ($_GET['p'] == ceil($count / 10)) {
            $Page->setConfig('theme', '<a href="/Finance/mywd/p/'.$uppage.'">«</a>');
        } else if ($_GET['p'] > 1) {
            $Page->setConfig('theme', '<a href="/Finance/mywd/p/'.$uppage.'">«</a><a href="/Finance/mywd/p/'.$dopage.'">»</a>');
        }



        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show();


        $list = $Model->where($where)->order('id asc')->field('id,username,moble,addtime,invit_1')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['invits'] = M('User')->where(array('invit_1' => $v['id']))->order('id asc')->field('id,username,moble,addtime,invit_1')->select();
            $list[$k]['invitss'] = count($list[$k]['invits']);

            foreach ($list[$k]['invits'] as $kk => $vv) {
                $list[$k]['invits'][$kk]['invits'] = M('User')->where(array('invit_1' => $vv['id']))->order('id asc')->field('id,username,moble,addtime,invit_1')->select();
                $list[$k]['invits'][$kk]['invitss'] = count($list[$k]['invits'][$kk]['invits']);
            }
        }
        $s_count = 0;
        if(is_array($list)){
            foreach($list as $k => $v){
                $s_count++;

                foreach($v['invits'] as $kk => $vv){
                    $s_count++;

                    foreach($vv['invits'] as $kkk=>$vvv){
                        $s_count++;
                    }
                }

            }
        }
        //echo $s_count;
        //die;
        //print_r($list[0]['invits']);
        //die;
        //$this->assign('s_count_1', $s_count_1);
        //$this->assign('s_count_2', $s_count_2);
        //$this->assign('s_count_3', $s_count_3);
        $this->assign('s_count', $s_count);

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 我的奖品
     */
    public function myjp()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_myjp'));
        check_server();
        $where['userid'] = userid();
        $Model = M('Invit');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['invit'] = M('User')->where(array('id' => $v['invit']))->getField('id');
        }

        $s_count = 0;
        $s_count = $Model->where($where)->sum('fee');
        //$s_count = round($s_count,2);

        $this->assign('s_count', $s_count);


        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }


    /**
     * 最新活动
     */
    public function myzc($coin = NULL,$addr = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }
        $user = M('User')->where(array('id' => userid()))->find();
        if (!$user['idcardauth']) {
            $this->error('您还没有认证，请先认证！', "/user/nameauth.html");
        }
        $this->assign('prompt_text', D('Text')->get_content('finance_myzc'));

        if (C('coin')[$coin]) {
            $coin = trim($coin);
        } else {
            $coin = C('xnb_mr');
        }

        $this->assign('xnb', $coin);
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'cny')
        ))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin[$coin] = round($user_coin[$coin], 6);
        $this->assign('user_coin', $user_coin);

        if (!$coin_list[$coin]['zc_jz']) {
            $this->assign('zc_jz', '当前币种禁止转出！');
        } else {
            $userQianbaoList = M('UserQianbao')->where(array('userid' => userid(), 'status' => 1, 'coinname' => $coin))->order('id desc')->select();
            $this->assign('userQianbaoList', $userQianbaoList);
            $moble = M('User')->where(array('id' => userid()))->getField('moble');

            if ($moble) {
                $moble = substr_replace($moble, '****', 3, 4);
            } else {
                redirect(U('Home/User/moble'));
                exit();
            }

            $this->assign('moble', $moble);
        }

        $where['userid'] = userid();
        $where['coinname'] = $coin;
        $Moble = M('Myzc');
        $count = $Moble->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('addr', $addr);
        $this->display();
    }

    public function upmyzc($coin, $num, $addr, $paypassword, $moble_verify)
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }
        $userid = userid();
        $isc = M()->query("SELECT cid FROM  a_ctc where uid = '$userid' and type = '1' and stu = 2; ");
        $isz = M()->query("SELECT id FROM  qq3479015851_myzr where userid = '$userid' ; ");
        if (!$isc && !$isz) $this->error('未查询到您的充值、转入记录，转出失败！');

        if (!check($moble_verify, 'd')) {
            $this->error('短信验证码格式错误！');
        }
        $this->_verify_count_check($moble_verify,session('myzc_verify'));

        $num = abs($num);

        if (!check($num, 'currency')) {
            $this->error('数量格式错误！');
        }

        if (!check($addr, 'dw')) {
            $this->error('钱包地址格式错误！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (!check($coin, 'n')) {
            $this->error('币种格式错误！');
        }

        if (!C('coin')[$coin]) {
            $this->error('币种错误！');
        }

        $Coin = M('Coin')->where(array('name' => $coin))->find();

        if (!$Coin) {
            $this->error('币种错误！');
        }

        $myzc_min = ($Coin['zc_min'] ? abs($Coin['zc_min']) : 0.0001);
        $myzc_max = ($Coin['zc_max'] ? abs($Coin['zc_max']) : 10000000);

        if ($num < $myzc_min) {
            $this->error('转出数量超过系统最小限制！');
        }

        if ($myzc_max < $num) {
            $this->error('转出数量超过系统最大限制！');
        }

        $user = M('User')->where(array('id' => userid()))->find();

        if ($paypassword != $user['paypassword']) {
            $this->error('交易密码错误！');
        }

        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($user_coin[$coin] < $num) {
            $this->error('可用余额不足');
        }

        $qbdz = $coin . 'b';
        $fee_user = M('UserCoin')->where(array($qbdz => $Coin['zc_user']))->find();

//		if ($fee_user) {
//			$fee = round(($num / 100) * $Coin['zc_fee'], 8);
//			$mum = round($num - $fee, 8);
//
//			if ($mum < 0) {
//				$this->error('转出手续费错误！');
//			}
//
//			if ($fee < 0) {
//				$this->error('转出手续费设置错误！');
//			}
//		}
//		else {
//			$fee = 0;
//			$mum = $num;
//		}
        //eth 系列转出手续费重新计算，扣除个数
        if ($fee_user) {
            $fee = $Coin['zc_fee'];
            $mum = round($num - $fee, 8);

            if ($mum < 0) {
                $this->error('转出手续费错误！');
            }

            if ($fee < 0) {
                $this->error('转出手续费设置错误！');
            }
        } else {
            $fee = 0;
            $mum = $num;
        }
        //eth 系列转出手续费重新计算，扣除个数end

        $mo = M();


        //eth 357898628
        if ($Coin['type'] == 'eth') {
            $heyue = $Coin['dj_yh'];
//            $mo = M();
//            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();
            $peer = $mo->table('qq3479015851_user_coin')->where(array($qbdz => $addr))->find();

            if ($peer) {

                $mo = M();
                $rs = array();
                $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                $rs[] = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('qq3479015851_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $this->success('您已提币成功，后台审核后将自动转出！');
            } else {
                //eth 钱包转出
                $heyue = $Coin['dj_yh'];//合约地址
                $auto_status = ($Coin['zc_zd'] && ($num < $Coin['zc_zd']) ? 1 : 0);
                $mo = M();
                $rs = array();
                $rs[] = $r = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $aid = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
                if ($auto_status) {
                    $EthCommon = new \Org\Util\EthCommon(COIN_ADDR, COIN_PORT, "2.0");
                    $EthPayLocal = new \Org\Util\EthPayLocal(COIN_ADDR, COIN_PORT, "2.0", COIN_CAIWU);
                    if ($heyue) {
                        //合约地址转出
                        $zhuan['toaddress'] = $addr;
                        $zhuan['token'] = $heyue;
                        $zhuan['type'] = $coin;
                        $zhuan['amount'] = floatval($mum);
                        $sendrs = $EthPayLocal->eth_ercsendTransaction($zhuan);

                    } else {
                        //eth
                        $zhuan['toaddress'] = $addr;
                        $zhuan['amount'] = floatval($mum);
                        $sendrs = $EthPayLocal->eth_sendTransaction($zhuan);
                    }

                    if ($sendrs && $aid) {
                        $arr = json_decode($sendrs, true);
                        $hash = $arr['result'] ? $arr['result'] : $arr['error']['message'];
                        if ($hash) M()->execute("UPDATE `qq3479015851_myzc` SET  `hash` =  '$hash' WHERE id = '$aid' ");
                    }
                    $this->success('您已提币成功，后台审核后将自动转出！' . $mum);
                }
                $this->success('您已提币成功，后台审核后将自动转出！');

            }
        }
        //eth 357898628

        if ($Coin['type'] == 'rgb') {
            debug($Coin, '开始认购币转出');
//            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

            $peer = $mo->table('qq3479015851_user_coin')->where(array($qbdz => $addr))->find();

            if (!$peer) {
                $this->error('转出认购币地址不存在！');
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            //$mo->execute('lock tables  qq3479015851_user_coin write  , qq3479015851_myzc write  , qq3479015851_myzr write , qq3479015851_myzc_fee write');
            $rs = array();
            $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
            $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

            if ($fee) {
                if ($mo->table('qq3479015851_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
                    $rs[] = $mo->table('qq3479015851_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
                    debug(array('msg' => '转出收取手续费' . $fee), 'fee');
                } else {
                    $rs[] = $mo->table('qq3479015851_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
                    debug(array('msg' => '转出收取手续费' . $fee), 'fee');
                }
            }

            $rs[] = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            $rs[] = $mo->table('qq3479015851_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

            if ($fee_user) {
                $rs[] = $mo->table('qq3479015851_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $Coin['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                //$mo->execute('unlock tables');
                session('myzc_verify', null);
                $this->success('转账成功！');
            } else {
                $mo->execute('rollback');
                $this->error('转账失败!');
            }
        }

        if ($Coin['type'] == 'qbb') {
//            $mo = M();

//            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();
            $peer = $mo->table('qq3479015851_user_coin')->where(array($qbdz => $addr))->find();


            if ($peer) {

                $mo = M();
                $rs = array();
                $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('qq3479015851_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                $rs[] = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('qq3479015851_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                $this->success('您已提币成功，后台审核后将自动转出！');
            } else {
                $dj_username = $Coin['dj_yh'];
                $dj_password = $Coin['dj_mm'];
                $dj_address = $Coin['dj_zj'];
                $dj_port = $Coin['dj_dk'];
                $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                $json = $CoinClient->getinfo();

                if (!isset($json['version']) || !$json['version']) {
                    //$this->error('钱包链接失败！');
                }

                $valid_res = $CoinClient->validateaddress($addr);

                if (!$valid_res['isvalid']) {
                    $this->error($addr . '不是一个有效的钱包地址！');
                }

                $auto_status = ($Coin['zc_zd'] && ($num < $Coin['zc_zd']) ? 1 : 0);

                if ($json['balance'] < $num) {
                    //$this->error('钱包余额不足');
                }

                $mo = M();
                $rs = array();
                $rs[] = $r = $mo->table('qq3479015851_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $aid = $mo->table('qq3479015851_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($auto_status) {
                    $sendrs = $CoinClient->sendtoaddress($addr, floatval($mum));
                    $this->success('您已提币成功，后台审核后将自动转出!');
                } else {
                    $this->success('您已提币成功，后台审核后将自动转出！');
                }
                session('myzc_verify',null);
            }
        }
    }

    public function mywt($market = NULL, $type = NULL, $status = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }
        if($market != null){
            $market = str_replace("cnyt", "cny", $market);
        }
        $this->assign('prompt_text', D('Text')->get_content('finance_mywt'));
        check_server();
        $Coin = M('Coin')->where(array('status' => 1))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $market_list[$v['name']] = $v;
        }

        $this->assign('market_list', $market_list);

        if($market != null) {
            if (!$market_list[$market]) {
                $market = $Market[0]['name'];
            }

            $where['market'] = $market;
        }
        if (($type == 1) || ($type == 2)) {
            $where['type'] = $type;
        }

        if (($status == 1) || ($status == 2) || ($status == 3)) {
            $where['status'] = $status - 1;
        }

        $where['userid'] = userid();
        $this->assign('market', $market);
        $this->assign('type', $type);
        $this->assign('status', $status);
        $Moble = M('Trade');
        $count = $Moble->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $Page->parameter .= 'type=' . $type . '&status=' . $status . '&market=' . $market . '&';
        $show = $Page->show();
        $list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = $v['num'] * 1;
            $list[$k]['price'] = $v['price'] * 1;
            $list[$k]['deal'] = $v['deal'] * 1;
        }
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function introduce_main(){//主区介绍页面
        $this->display();
    }

    public function introduce_new(){//创新区介绍页面
        $this->display();
    }

    public function introduce_experiment(){//实验区介绍页面
        $this->display();
    }


    public function myzr_list(){//用户转入币记录
        if (!userid()) {
            redirect('/Login');
        }
        $where['userid'] = userid();
        $Moble = M('Myzr');
        $count = $Moble->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $show = $Page->show();
        $list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function myzc_list(){//用户转出币记录
        if (!userid()) {
            redirect('/Login');
        }
        $where['userid'] = userid();
        $Moble = M('Myzc');
        $count = $Moble->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $show = $Page->show();
        $list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

}

?>
