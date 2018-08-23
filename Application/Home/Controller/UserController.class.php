<?php
namespace Home\Controller;

class UserController extends HomeController
{
    public function index($page = 0)
    {
        if (!userid()) {
            redirect('/Login');
        }

        $userid = userid();

        if ($userid == 11190811111) {
            echo "<br><br><br><br><br><br>";
            // 扣eos
            $market = M()->query("SELECT name,new_price FROM  `qq3479015851_market` order by id asc ");
            foreach ($market as $kk => $vv) {
                $xnbs = explode("_", $vv['name']);
                $markets[$xnbs[0]] = $vv['new_price'];
            }
            //print_r($markets);
            //$users1 = M()->query("SELECT userid,eosd FROM `qq3479015851_user_coin` WHERE eosd>=1 limit 0,1000");
            //print_r($users1);
            //exit;
            foreach ($users1 as $k => $v) {
                //计算价格
                $chong = $chongb = $chongbs = 0;
                $chongbs = array();
                $chong = M()->query("SELECT sum(pri) as sum1 FROM  `a_ctc` where uid ='" . $v['id'] . "' and stu = 2   ");
                if ($chong[0]['sum1'] < 200) {
                    //echo $v['id']."=>".($pri+$chong[0]['sum1'])."<br>";
                    //充值小于200  查转入币记录
                    $chongbs = "";
                    $chongbs = M()->query("SELECT coinname,num FROM  `qq3479015851_myzr` where userid = '" . $v['id'] . "' order by id asc ");
                    $pri = 0;
                    if ($chongbs) {
                        foreach ($chongbs as $k1 => $v1) {
                            $pri += $markets[$v1['coinname']] * $v1['num'];
                        }
                        if (($pri + $chong[0]['sum1']) < 200) {
                            //echo $v['id']."=>".($pri+$chong[0]['sum1'])."<br>";
                            $btarr[] = $v['id'];
                        } else {
                            //echo $v['id']."=>".($pri+$chong[0]['sum1'])."<br>";
                        }
                    } else {
                        //$bb++;
                        //echo $v['id']."=>".($pri)."<br>";
                        $btarr[] = $v['id'];
                    }
                } else {
                    //echo $v['id']."=>".$chong[0]['sum1']."<br>";
                }
            }
            echo count($btarr) . "<br>";
            //print_r($btarr);
            foreach ($btarr as $k2 => $v2) {
                $kou = 1;
                if ($v2 > 312312 && $v2 < 315861) $kou = 2;
                $btarrs[$v2] = $kou;
                //M()->execute("UPDATE  `qq3479015851_user_coin` SET  `eosd` = eosd-$kou WHERE `userid` ='".$v2." ';");

            }
            print_r($btarrs);
        }
        if ($userid == 111908000000) {
            echo "<br><br><br><br><br><br>";
            // 备份数据对比
            $users1 = M()->query("SELECT cny,userid FROM  `qq3479015851_user_coin` order by id asc limit 0,10000 ");
            foreach ($users1 as $k => $v) {
                //计算价格
                $chong = $allpri = 0;
                $chong = M()->query("SELECT cny FROM  `wanghong0426`.`qq3479015851_user_coin` where userid ='" . $v['userid'] . "'   ");
                if (($v['cny'] - $chong[0]['cny']) > 10) echo $v['userid'] . "=>cny:" . $v['cny'] . "=>备份cny:" . $chong[0]['cny'] . "=>误差:" . ($v['cny'] - $chong[0]['cny']) . "<br>";
            }
            echo "<br>" . $bb;
        }

        if ($userid == 11190800000) {
            //充值i 提现误差统计
            $coins = M()->query("SELECT name,new_price FROM  `qq3479015851_market` order by id desc ");
            foreach ($coins as $k1 => $v1) {
                $coiny[$v1['name']] = $v1['new_price'];
            }
            echo "<br><br><br><br><br><br>";
            //print_r($coiny);
            $users1 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.idcardauth = 1  order by b.userid asc limit 2000,2000  ");
            foreach ($users1 as $k => $v) {
                //计算价格
                $allpri = $allpris = 0;
                foreach ($coiny as $k2 => $v2) {
                    $bpri = $k3 = $chong = $ti = $spr = 0;
                    $k3 = str_replace("_cny", "", $k2);
                    $bpri = ($v[$k3] + $v[$k3 . "d"]) * $v2;
                    $allpri += $bpri;
                }
                $chong = M()->query("SELECT sum(num) as sum FROM  `a_ctc` where uid ='" . $v['userid'] . "' and type = 1 and stu = 2 ");
                $ti = M()->query("SELECT sum(num) as sum FROM  `a_ctc` where uid ='" . $v['userid'] . "' and type = 2 and stu = 2 ");
                $wall = M()->query("SELECT sum(ylcs) as sum FROM  `qq3479015851_a_sign` where userid ='" . $v['userid'] . "' and hdid = 5 ");
                $wd = M()->query("SELECT cnut FROM  `a_wakuang` where userid ='" . $v['userid'] . "' ");
                $allpri += $v['cny'] + $v['cnyd'];
                $allpris = $allpri;
                //$allpri-=($wall[0]['sum']-$wd[0]['sum'])*0.08+$ti[0]['sum']+$chong[0]['sum'];
                $allpri = $allpri - ($wall[0]['sum'] - $wd[0]['sum']) * 0.08 + $chong[0]['sum'] - $ti[0]['sum'];
                if ($allpri > 900) {

                    echo $v['userid'] . "=>总资产:" . $allpris . "=>误差:" . $allpri . "=>充值:" . $chong[0]['sum'] . "=>提现:" . $ti[0]['sum'] . "<br>";
                    $bb++;
                }
            }
            echo "<br>" . $bb;
        }


        if ($userid == 11190800000) {
            //31日之前未认证的用户d
            //$users1 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.addtime < 1522425600 and a.idcardauth = 0  order by b.cny asc  ");
            foreach ($users1 as $v) {
                //M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut-100,`doge` =  doge-100 WHERE `userid` ='".$v['userid']." ';");
                //echo $v['userid']."=>cnut:".$v['cnut']."=>doge:".$v['doge']."=>cny:".$v['cny']."<br>";
                $cnut += $v['cnut'];
                $doge += $v['doge'];
            }
            //echo $cnut."=>".$doge."=>".count($users1);
            //31--4.15日之前未认证的用户
            //$users11 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.addtime > 1522425600 and a.addtime < 1523721600 and a.idcardauth = 0 order by b.cny asc  ");
            foreach ($users11 as $v) {
                //M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnut` = cnut-200,`doge` =  doge-100 WHERE `userid` ='".$v['userid']." ';");
                //echo $v['userid']."=>cnut:".$v['cnut']."=>doge:".$v['doge']."=>cny:".$v['cny']."<br>";
                $cnut += $v['cnut'];
                $doge += $v['doge'];
            }
            //echo $cnut."=>".$doge."=>".count($users1);
            //31--4.15日之前未认证的用户
            //$users1 = M()->query("SELECT b.* FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid and a.addtime > 1522425600 and a.addtime < 1523721600 and a.idcardauth = 0 order by b.cny asc  ");
            foreach ($users111 as $v) {
                //echo $v['userid']."=>cnut:".$v['cnut']."=>doge:".$v['doge']."=>cny:".$v['cny']."<br>";
                $cnut += $v['cnut'];
                $doge += $v['doge'];
            }
            //echo $cnut."=>".$doge."=>".count($users1);
            //清理推荐赠送100cnut 100doge冻结
            echo "<br><br><br><br>";
            //已经处理2200；
            $pages = $page * 100;
            $users12 = M()->query("SELECT a.id,b.cnutd FROM  `qq3479015851_user` a,qq3479015851_user_coin b where a.id = b.userid  order by a.id asc limit $pages,100  ");
            foreach ($users12 as $v) {
                $users12_1 = 0;
                $users12_1 = M()->query("SELECT count(id) as count FROM  `qq3479015851_user` where invit_1 = '" . $v['id'] . "' and idcardauth = 0 and addtime < '1523721600'    ");
                if ($users12_1[0]['count']) {
                    $ks++;
                    $num = 0;
                    echo $v['id'] . "=>冻结CNUT:" . $v['cnutd'] . "=>未认证:" . $users12_1[0]['count'] . "<br>";
                    $num = $users12_1[0]['count'] * 100;
                    //M()->execute("UPDATE  `qq3479015851_user_coin` SET  `cnutd` = cnutd-$num,`doged` =  doged-$num WHERE `userid` ='".$v['id']." ';");
                }
            }
            echo $ks;
            $page = $page + 1;
            $this->success('操作成功！', '/Finance/index/page/' . $page);
            exit;
            //echo $cnut."=>".$doge."=>".count($users1);
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
            } else {
                if($Market[C('market_type')[$v['name']]]['status'] != 1){
                    continue;
                }
                $vsad = explode("_", $v['name']);
                if ($Market[C('market_type')[$v['name']]]['new_price']) {
                    $jia = $Market[C('market_type')[$v['name']]]['new_price'];
                } else {
                    $jia = 1;
                }
                //开启市场时才显示对应的币
                if (in_array($v['name'], C('coin_on'))) {
                    $coinList[$v['name']] = array('zr_jz' => $v['zr_jz'], 'zc_jz' => $v['zc_jz'], 'name' => $v['name'], 'xnbs' => $vsad[0], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                }
                $cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
            }
        }


        $this->assign('cny', $cny);
        $this->assign('coinList', $coinList);
        $this->assign('prompt_text', D('Text')->get_content('finance_index'));
        $this->display();
    }


    public function trade_log($market = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }

        $where['status'] = array('eq', 1);
        $where['userid'] = userid();

        if ($market) {
            $where['market'] = $market;
        }

        $Model = M('TradeLog');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);

        $this->display();
    }

    public function token_apply()
    {
        $this->display();
    }

    public function token_apply_submit($data1 = '', $data2 = '', $data3 = '', $data4 = '', $data5 = '', $data6 = '', $data7 = '', $data8 = '', $data9 = '', $data10 = '', $data11 = '', $data12 = '', $data13 = '', $data14 = '', $data15 = '', $data16 = '', $data17 = '', $data18 = '', $data19 = '', $data20 = '', $data21 = '', $data22 = '', $data23 = '', $data24 = '', $data25 = '', $data26 = '', $data27 = '', $data28 = '', $data29 = '')
    {
        if (!userid()) {
            redirect('/Login');
        }
        $time = time();
        //Email
        if ($data1) {
            $emailreg = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
            if (!(preg_match_all($emailreg, $data1))) {
                $this->error("您的Email格式错误");
            }
        }
        //項目方負責人聯係方式(必填)
        if (!$data2) {
            $this->error("请输入項目方負責人聯係方式");
        } else {
            if (strlen($data2) != "11") {
                $this->error("項目方負責人聯係方式格式错误");
            }

        }
        //英文名称（必填）
        if (!$data3) {
            $this->error("请输入币种英文名称");
        }
        if ($data3) {
            if (!(strlen($data3) > 0 && strlen($data3) < 40)) {
                $this->error("币种英文名称应小于40个字");
            } else {
                $ennamereg = "/^[a-zA-Z\/ ]{1,40}$/";
                if (!(preg_match_all($ennamereg, $data3))) {
                    $this->error("币种英文名称格式错误");
                }
            }
        }
        //中文名称（必填）
        if (!$data4) {
            $this->error("请输入币种中文名称");
        }
        if ($data4) {
            if (!(strlen($data4) > 0 && strlen($data4) < 20)) {
                $this->error("币种中文名称应小于20个字");
            }
        }
        //幣種交易符號（必填）
        if (!$data5) {
            $this->error("请输入幣種交易符號");
        }
        if ($data5) {
            if (!(strlen($data5) > 0 && strlen($data5) < 20)) {
                $this->error("幣種交易符號应小于20个字");
            }
        }
        //ICO日期
        if ($data6) {
            $icoreg = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
            if (!(preg_match_all($icoreg, $data6))) {
                $this->error("ICO日期格式错误");
            }
        }
        //可流通日期
        if ($data7) {
            $kltreg = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
            if (!(preg_match_all($kltreg, $data7))) {
                $this->error("可流通日期格式错误");
            }
        }
        //幣種區塊網絡類型(必填)
        if (!$data8) {
            $this->error("请输入幣種區塊網絡類型");
        } else {
            if (!($data8 == 'ETH' || $data8 == 'QTUM' || $data8 == 'NEO' || $data8 == 'XLM' || $data8 == 'BTS' || $data8 == '獨立鏈')) {
                $this->error("幣種區塊網絡類型错误");
            }
        }
        //代幣合約地址
        if ($data9) {
            $dbhyreg = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
            if (!(preg_match_all($dbhyreg, $data9))) {
                $this->error("代幣合約地址格式错误");
            }
        }
        //小数点位数
        if ($data10) {
            if (!(strlen($data10) > 0 && strlen($data10) < 3)) {
                $this->error("小数点位数应小于99");
            }
            $xsdreg = "/^([0-9]{1,2})$/";
            if (!(preg_match_all($xsdreg, $data10))) {
                $this->error("小数点位数格式错误");
            }
        }

        //幣種官方網站（必填）
        if (!$data11) {
            $this->error("请输入幣種官方網站");
        }
        if ($data11) {
            $bzgwreg = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
            if (!(preg_match_all($bzgwreg, $data11))) {
                $this->error("币种官方网站格式错误");
            }
        }
        //幣種白皮書網址
        if ($data12) {
            $bzbpsreg = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
            if (!(preg_match_all($bzbpsreg, $data12))) {
                $this->error("币种白皮书网址格式错误");
            }
        }
        //区块浏览器
        if ($data13) {
            if (!(strlen($data13) > 0 && strlen($data13) < 30)) {
                $this->error("区块浏览器应小于30个字");
            }
        }
        //Logo圖片鏈接
        if ($data14) {
            $logoreg = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
            if (!(preg_match_all($logoreg, $data14))) {
                $this->error("Logo圖片鏈接格式错误");
            }
        }
        //Twitter鏈接
        if ($data15) {
            $Twitterreg = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
            if (!(preg_match_all($Twitterreg, $data15))) {
                $this->error("Twitter鏈接格式错误");
            }
        }
        //Telegram鏈接
        if ($data16) {
            $Telegramreg = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";
            if (!(preg_match_all($Telegramreg, $data16))) {
                $this->error("Telegram鏈接格式错误");
            }
        }
        //幣種簡短中文介紹（必填）
        if (!$data17) {
            $this->error("请输入幣種簡短中文介紹");
        }
        if ($data17) {
            if (!(strlen($data17) > 0 && strlen($data17) < 200)) {
                $this->error("幣種簡短中文介紹应小于200个字");
            }
        }
        //幣種簡短英文介紹
        if ($data18) {
            if (!(strlen($data18) > 0 && strlen($data18) < 200)) {
                $this->error("幣種簡短英文介紹应小于200个字");
            }
        }
        //幣種总量(必填)
        if (!$data19) {
            $this->error("请输入幣種总量");
        }
        //幣種流通量
        if ($data20) {
            if (!(strlen($data20) > 0 && strlen($data20) < 9)) {
                $this->error("幣種流通量应小于999999999");
            }
        }
        //幣種分配比例
        if ($data21) {
            if (!(strlen($data21) > 0 && strlen($data21) < 50)) {
                $this->error("幣種分配比例应小于50个字");
            }
        }
        //成本价格
        if ($data22) {
            if (!(strlen($data22) > 0 && strlen($data22) < 20)) {
                $this->error("成本价格应小于20个字");
            }
        }
        //已上線交易平台
        if ($data23) {
            if (!(strlen($data23) > 0 && strlen($data23) < 100)) {
                $this->error("已上線交易平台应小于100个字");
            }
        }
        //其他信息說明
        if ($data24) {
            if (!(strlen($data24) > 0 && strlen($data24) < 500)) {
                $this->error("其他信息說明应小于500个字");
            }
        }
        //社区QQ群号(必填)
        if (!$data28) {
            $this->error("请输入社区QQ群号");
        }

        $sql = M()->execute("INSERT INTO a_bzsxsq (`id` ,`data1` ,`data2` ,`data3` ,`data4` ,`data5` ,`data6` ,`data7` ,`data8` ,`data9` ,`data10` ,`data11` ,`data12` ,`data13` ,`data14` ,`data15` ,`data16` ,`data17` ,`data18` ,`data19` ,`data20` ,`data21` ,`data22` ,`data23` ,`addtime` ,`data24` ,`data25` ,`data26` ,`data27` ,`data28` ,`data29`)
VALUES (NULL ,  '$data1',  '$data2',  '$data3',  '$data4',  '$data5',  '$data6',  '$data7',  '$data8',  '$data9',  '$data10',  '$data11',  '$data12',  '$data13',  '$data14',  '$data15',  '$data16',  '$data17',  '$data18',  '$data19',  '$data20',  '$data21',  '$data22',  '$data23',  '$time',  '$data24',  '$data25',  '$data26',  '$data27',  '$data28',  '$data29');");

        if ($sql) {
            $this->success('提交成功');
        } else {
            $this->error('操作失败，请重试');
        }
        $this->assign('bid', $bid);
        $this->assign('cny', $cny);
        $this->assign('blist', $blist);
        $this->assign('coinList', $coinList);
        $this->assign('prompt_text', D('Text')->get_content('finance_index'));
        $this->display();
    }

    public function sell($codes = NULL)
    {
        if (!userid()) {
            $this->error('请先登录！', '/');
        }
        if (!$codes) {
            $this->error('领取错误，错误码:0820', '/');
        }
        $mobile = M('User')->where(array('id' => userid()))->getField('moble');
        if (!$mobile) {
            $this->error('领取错误，错误码:0826！', '/');
        }
        $idcardauth = M('User')->where(array('id' => userid()))->getField('idcardauth');
        if (!$idcardauth) {
            $this->error('您还未通过实名认证，请先认证！', '/');
        }
        $eos = M('UserCoin')->where(array('userid' => userid()))->getField('eosd');
        if ($eos > 0) {
            $this->error('您已经领取过，如有疑问请联系客服！', '/');
        }
        $newcode = md5("eos" . $mobile);
        if ($codes == $newcode) {
            $bi = 1;
            if (userid() > 312312 && userid() < 315863) $bi = 2;
            $rs = M('UserCoin')->where(array('userid' => userid()))->save(array("eosd" => $bi));
            if ($rs) {

                $this->error('恭喜您，成功领取' . $bi . '枚EOS！', '/Finance/index');
            } else {
                $this->error('领取失败，请重试！', '/');
            }
        } else {
            $this->error('领取地址错误，请联系客服获取新地址！', '/');
        }
    }

    public function checkAuth()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $userid = userid();

        header("content-type: application/json");

        $user = M('User')->where(array('id' => $userid))->select();

        if ($user['idcardauth'] == 1 && !empty($user['idcard'])){
            return json_encode(array('status' => 'pass'));
        }
        else {
            return json_encode(array('status' => 'fail'));
        }
    }

    public function auth()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $userid = userid();

        header("content-type: application/json");

        $data = array();

        $url_token = "http://face.zlnnk.com:5000/face-notify-listener/token";
        $data['biz_no'] = $userid;//流水号，我方提供，传入用户ＩＤ

        $result = postData($url_token, $data);

        $res = json_decode($result, true)["token"];

        if (!empty($res)) {
            echo json_encode($res);
        }
    }

    public function uc()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $data = array();

        header("content-type: application/json");

        $user = M('User')->where(array('id' => userid()))->find();

        session('userIdCardNumber', $user['idcard']);
        session('email', $user['email']);
        session('idcardauth', $user['idcardauth']);
        session('nickname', $user['nickname']);
        if (!empty($user['idcard']) && $user['idcardauth'] == 1) {
            session('idcard_verify', 1);
        } else {
            session('idcard_verify', 0);
        }

        if (!empty($user['email'])) {
            session('email_verify', 1);
        } else {
            session('email_verify', 0);
        }

        if (!empty($user['moble'])) {
            session('moble_verify', 1);
        } else {
            session('moble_verify', 0);
        }

        if (!empty($user['paypassword'])) {
            session('paypassword_verify', 1);
        } else {
            session('paypassword_verify', 0);
        }

        $where['status'] = array('egt', 0);
        $where['userid'] = userid();
        $Model = M('UserLog');
        $count = $Model->where($where)->count();
//        $Page = new \Think\Page($count, 10);
//        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit(5)->select();
        $this->assign('list', $list);
//        $this->assign('page', $show);
        $this->assign('prompt_text', D('Text')->get_content('user_log'));
        $this->display();
    }

    public function changeNickName($new_nickname)
    {
        if (!userid()) {
            redirect('/Login');
        }

        header("content-type: application/json");

        if (mb_strlen($new_nickname, "utf-8") > 12 || mb_strlen($new_nickname, "utf-8") < 2) {
            echo json_encode(array('nick' => '', 'status' => 'error'), JSON_UNESCAPED_UNICODE);
        } else {
            M('User')->where(array('id' => userid()))->save(array('nickname' => $new_nickname));
            $user = M('User')->where(array('id' => userid()))->find();
            session('nickName', $user['nickname']);

            echo json_encode(array('nick' => $user['nickname'], 'status' => 'ok'), JSON_UNESCAPED_UNICODE);
        }
    }

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

        $this->assign('user', $user);
        $this->display();
    }

    public function namecard($card1 = '', $card3 = '', $card2 = '',$truename,$idcard)
    {
        if (!userid()) {
            redirect('/Login');
        }
        if (!$card1) $this->error('请上传身份证正面！');
        if (!$card2) $this->error('请上传身份证背面！');
        if (!$card3) $this->error('请上传手持身份证！');
        if (empty($truename)) {
            $this->error('真实姓名格式错误！');
        }

        $idcard = preg_replace('# #', '', $idcard);

        if (empty($idcard)) {
            $this->error('身份证号格式错误！');
        }

        $user = M('User')->where(array('idcard' => $idcard))->find();
        if ($user) {
            if($user['id'] != userid()){
                $this->error('您的身份证号码已经认证<br>认证账号为：' . $user["username"] . '！');
            }
        }else{
            $user = M('User')->where(array('id' => userid()))->find();
        }

        if($user['idcardauth'] == 1){
            $this->error('当前账户已认证');
        }

        $path = $card1 . "_" . $card2 . "_" . $card3;
        if (M('User')->where(array('id' => userid()))->save(array('truename' => $truename, 'idcard' => $idcard,'idcardimg1' => $path, 'idcardinfo' => ''))) {
            $this->success('成功！');
        } else {
            $this->error('失败！');
        }
        $this->success('操作成功，请等待审核！');
    }

    public function password()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $this->assign('prompt_text', D('Text')->get_content('user_password'));
        $this->display();
    }

    public function uppassword($oldpassword, $newpassword, $repassword, $moble_verify)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!session('real_moble')) {
            $this->error('验证码已失效！');
        }

        if ($moble_verify != session('real_moble')) {
            $this->error('手机验证码错误！');
        } else {
            session('real_moble', null);
        }

        if (!check($oldpassword, 'password')) {
            $this->error('旧登录密码格式错误！');
        }

        if (!check($newpassword, 'password')) {
            $this->error('新登录密码格式错误！');
        }

        if ($newpassword != $repassword) {
            $this->error('确认新密码错误！');
        }

        $password = M('User')->where(array('id' => userid()))->getField('password');

        if ($oldpassword != $password) {
            $this->error('旧登录密码错误！');
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('password' => $newpassword));

        if ($rs) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    public function password_reset($moble_verify){

    }

    public function uppassword_qq3479015851($oldpassword = "", $newpassword = "", $repassword = "",$moble_verify)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if(empty($moble_verify)){
            $this->error('请输入邮箱或短信验证码！');
        }

        if ($oldpassword == $newpassword) {
            $this->error('新修改的密码和原密码一样！');
        }
        if (!check($oldpassword, 'password')) {
            $this->error('旧登录密码格式错误！');
        }

        if (!check($newpassword, 'password')) {
            $this->error('新登录密码格式错误！');
        }

        if ($newpassword != $repassword) {
            $this->error('确认新密码错误！');
        }
        $this->_verify_count_check($moble_verify,session('findpwd_verify'));

        $password = M('User')->where(array('id' => userid()))->getField('password');

        if ($oldpassword != $password) {
            $this->error('旧登录密码错误！');
        }
        $paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if ($newpassword == $paypassword) {
            $this->error("新密码不能和交易密码一样");
        }


        $rs = M('User')->where(array('id' => userid()))->save(array('password' => $newpassword));

        if (!($rs === false)) {
            session('findpwd_verify',null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }


    public function paypassword()
    {
        if (!userid()) {
            redirect('/Login');
        }


        $user = M('User')->where(array('id' => userid()))->find();
        $this->assign('user', $user);

        $this->assign('prompt_text', D('Text')->get_content('user_paypassword'));
        $this->display();
    }


    public function uppaypassword_qq3479015851($oldpaypassword, $newpaypassword, $repaypassword,$verify_code)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if(empty($verify_code)){
            $this->error('验证码不能为空！');
        }

        $this->_verify_count_check($verify_code,session('findpaypwd_verify'));

        if (!check($oldpaypassword, 'password')) {
            $this->error('旧交易密码格式错误！');
        }

        if (!check($newpaypassword, 'password')) {
            $this->error('新交易密码格式错误！');
        }

        if ($newpaypassword != $repaypassword) {
            $this->error('确认新密码错误！');
        }

        $user = M('User')->where(array('id' => userid()))->find();

        if ($oldpaypassword != $user['paypassword']) {
            $this->error('旧交易密码错误！');
        }

        if ($newpaypassword == $user['password']) {
            $this->error('交易密码不能和登录密码相同！');
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('paypassword' => $newpaypassword));

        if (!($rs === false)) {
            session('findpaypwd_verify',null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }


    public function uppaypassword($oldpaypassword, $newpaypassword, $repaypassword, $moble_verify)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!session('real_moble')) {
            $this->error('验证码已失效！');
        }

        if ($moble_verify != session('real_moble')) {
            $this->error('手机验证码错误！');
        } else {
            session('real_moble', null);
        }

        if (!check($oldpaypassword, 'password')) {
            $this->error('旧交易密码格式错误！');
        }

        if (!check($newpaypassword, 'password')) {
            $this->error('新交易密码格式错误！');
        }

        if ($newpaypassword != $repaypassword) {
            $this->error('确认新密码错误！');
        }

        $user = M('User')->where(array('id' => userid()))->find();

        if ($oldpaypassword != $user['paypassword']) {
            $this->error('旧交易密码错误！');
        }

        if ($newpaypassword == $user['password']) {
            $this->error('交易密码不能和登录密码相同！');
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('paypassword' => $newpaypassword));

        if ($rs) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    public function ga()
    {
        if (empty($_POST)) {
            if (!userid()) {
                redirect('/Login');
            }

            $this->assign('prompt_text', D('Text')->get_content('user_ga'));
            $user = M('User')->where(array('id' => userid()))->find();
            $is_ga = ($user['ga'] ? 1 : 0);
            $this->assign('is_ga', $is_ga);

            if (!$is_ga) {
                $ga = new \Common\Ext\GoogleAuthenticator();
                $secret = $ga->createSecret();
                session('secret', $secret);
                $this->assign('Asecret', $secret);
                $qrCodeUrl = $ga->getQRCodeGoogleUrl($user['username'] . '%20-%20' . $_SERVER['HTTP_HOST'], $secret);
                $this->assign('qrCodeUrl', $qrCodeUrl);
                $this->display();
            } else {
                $arr = explode('|', $user['ga']);
                $this->assign('ga_login', $arr[1]);
                $this->assign('ga_transfer', $arr[2]);
                $this->display();
            }
        } else {
            if (!userid()) {
                $this->error('登录已经失效,请重新登录!');
            }

            $delete = '';
            $gacode = trim(I('ga'));
            $type = trim(I('type'));
            $ga_login = (I('ga_login') == false ? 0 : 1);
            $ga_transfer = (I('ga_transfer') == false ? 0 : 1);

            if (!$gacode) {
                $this->error('请输入验证码!');
            }

            if ($type == 'add') {
                $secret = session('secret');

                if (!$secret) {
                    $this->error('验证码已经失效,请刷新网页!');
                }
            } else if (($type == 'update') || ($type == 'delete')) {
                $user = M('User')->where('id = ' . userid())->find();

                if (!$user['ga']) {
                    $this->error('还未设置谷歌验证码!');
                }

                $arr = explode('|', $user['ga']);
                $secret = $arr[0];
                $delete = ($type == 'delete' ? 1 : 0);
            } else {
                $this->error('操作未定义');
            }

            $ga = new \Common\Ext\GoogleAuthenticator();

            if ($ga->verifyCode($secret, $gacode, 1)) {
                $ga_val = ($delete == '' ? $secret . '|' . $ga_login . '|' . $ga_transfer : '');
                M('User')->save(array('id' => userid(), 'ga' => $ga_val));
                $this->success('操作成功');
            } else {
                $this->error('验证失败');
            }
        }
    }

    public function email()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $user = M('User')->where(array('id' => userid()))->find();

        if (!empty($user['email'])) {
            session('email_verify', 1);
        } else {
            session('email_verify', 0);
        }

        $this->assign('user', $user);
        $this->display();
    }

    public function idcardauth(){
        if (!userid()) {
            redirect('/Login');
        }

        $user = M('User')->where(array('id' => userid()))->find();
        if (!empty($user['idcard']) && $user['idcardauth'] == 1) {
            session('idcard_verify', 1);
        } else {
            session('idcard_verify', 0);
        }


        $this->assign('user', $user);
        $this->display();
    }

    public function bindEmail()
    {
        if (IS_POST && userid()) {
            $input = I('post.');

            $_c = session('email#real_verify');
            $_t = session('email#real_verify#time');
            $_e = session('email#email');

            if ((time() - $_t) > 5 * 60)
                $this->error("验证码已过期，请重新获取");

            if (M('User')->where(array('email' => $input['email']))->find()) {
                $this->error('邮箱已被使用，请选择其他邮箱！');
            }

            if ($_c != $input['code'])
                $this->error("验证码错误");

            $res = M('User')->where(array('id' => userid()))->save(array('email'=>$_e, 'emailtime'=>time()));

            if (!check_arr($res))
                $this->error("绑定失败");
            else
                $this->success("绑定成功");
        }
        else {
            $this->error('非法访问！');
        }
    }

    public function moble()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $user = M('User')->where(array('id' => userid()))->find();

        //if ($user['moble']) {
        //$user['moble'] = substr_replace($user['moble'], '****', 3, 4);
        //}
        if (!empty($user['moble'])) {
            session('moble_verify', 1);
        } else {
            session('moble_verify', 0);
        }
        $this->assign('user', $user);
//        $this->assign('prompt_text', D('Text')->get_content('user_moble'));
        $this->display();
    }

    public function upmoble($moble, $moble_verify)
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }

        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (!check($moble_verify, 'd')) {
            $this->error('短信验证码格式错误！');
        }
        $this->_verify_count_check($moble_verify,session('real_verify'));

        if (M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码已存在！');
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('moble' => $moble, 'mobletime' => time()));

        if ($rs) {
            session('real_verify',null);
            $this->success('手机认证成功！');
        } else {
            $this->error('手机认证失败！');
        }
    }


    public function upmoble_qq3479015851($moble_new = "", $moble_verify_new = "")
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }
        $user = M('User')->where(array('id' => userid()))->find();
        if(!($user['moble'] == '' || $user['moble'] == null)){
            $this->error('禁止修改绑定手机，请联系客服处理!');
        }

        if (!check($moble_new, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (!check($moble_verify_new, 'd')) {
            $this->error('短信验证码格式错误！');
        }

        $this->_verify_count_check($moble_verify_new,session('real_verify'));
        $moble_new = $this->_replace_china_mobile($moble_new);
        if (M('User')->where(array('moble' => $moble_new))->find()) {
            $this->error('手机号码已存在！');
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('moble' => $moble_new, 'username' => $moble_new, 'mobletime' => time()));

        if (!($rs === false)) {
            session('real_verify',null);
            $this->success('手机绑定成功！');
        } else {
            $this->error('手机绑定失败！');
        }
    }


    public function tpwdsetting()
    {
        if (userid()) {
            $tpwdsetting = M('User')->where(array('id' => userid()))->getField('tpwdsetting');
            exit($tpwdsetting);
        }
    }

    public function uptpwdsetting($paypassword, $tpwdsetting)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (($tpwdsetting != 1) && ($tpwdsetting != 2) && ($tpwdsetting != 3)) {
            $this->error('选项错误！' . $tpwdsetting);
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if ($paypassword != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('tpwdsetting' => $tpwdsetting));

        if (!($rs === false)) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }

    public function upbank($name, $bank, $bankprov, $bankcity, $bankaddr, $bankcard, $paypassword)
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

        if (!check($bankprov, 'c')) {
            $this->error('开户省市格式错误！');
        }

        if (!check($bankcity, 'c')) {
            $this->error('开户省市格式错误2！');
        }

        if (!check($bankaddr, 'a')) {
            $this->error('开户行地址格式错误！');
        }

        if (!check($bankcard, 'd')) {
            $this->error('银行账号格式错误！');
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

        $userBank = M('UserBank')->where(array('userid' => userid()))->select();

        foreach ($userBank as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('请不要使用相同的备注名称！');
            }

            if ($v['bankcard'] == $bankcard) {
                $this->error('银行卡号已存在！');
            }
        }

        if (10 <= count($userBank)) {
            $this->error('每个用户最多只能添加10个银行卡账户！');
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

    public function qianbao($coin = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }

        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'cny')
        ))->select();

        if (!$coin) {
            $coin = $Coin[0]['name'];
        }

        $this->assign('xnb', $coin);

        $existsCoin = false;
        foreach ($Coin as $k => $v) {
            if($v['name'] == $coin){
                $existsCoin = true;
            }
            $coin_list[$v['name']] = $v;
        }
        if(!$existsCoin){
            redirect('/');
        }

        $this->assign('coin_list', $coin_list);
        $userQianbaoList = M('UserQianbao')->where(array('userid' => userid(), 'status' => 1, 'coinname' => $coin))->order('id desc')->select();
        $this->assign('userQianbaoList', $userQianbaoList);
        $this->assign('prompt_text', D('Text')->get_content('user_qianbao'));
        $this->display();
    }


    public function qianbao_list($coin = NULL)
    {
        if (!userid()) {
            redirect('/Login');
        }
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'cny')
        ))->select();
        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }
        $this->assign('coin_list', $coin_list);
        $this->display();
    }

    public function upqianbao($coin, $name, $addr, $paypassword)
    {
        if (!userid()) {
            redirect('/Login');
        }

        if (!check($name, 'a')) {
            $this->error('备注名称格式错误！');
        }

        if (!check($addr, 'dw')) {
            $this->error('钱包地址格式错误！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if ($paypassword != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('Coin')->where(array('name' => $coin))->find()) {
            $this->error('币种错误！');
        }

        $userQianbao = M('UserQianbao')->where(array('userid' => userid(), 'coinname' => $coin))->select();

        foreach ($userQianbao as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('请不要使用相同的钱包标识！');
            }

            if ($v['addr'] == $addr) {
                $this->error('钱包地址已存在！');
            }
        }

        if (10 <= count($userQianbao)) {
            $this->error('每个人最多只能添加10个地址！');
        }

        if (M('UserQianbao')->add(array('userid' => userid(), 'name' => $name, 'addr' => $addr, 'coinname' => $coin, 'addtime' => time(), 'status' => 1))) {
            $this->success('添加成功！');
        } else {
            $this->error('添加失败！');
        }
    }

    public function delqianbao($id, $paypassword)
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

        if (!M('UserQianbao')->where(array('userid' => userid(), 'id' => $id))->find()) {
            $this->error('非法访问！');
        } else if (M('UserQianbao')->where(array('userid' => userid(), 'id' => $id))->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function log()
    {
        if (!userid()) {
            redirect('/Login');
        }

        $where['status'] = array('egt', 0);
        $where['userid'] = userid();
        $Model = M('UserLog');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('prompt_text', D('Text')->get_content('user_log'));
        $this->display();
    }

    public function award_log()
    {
        if (!userid()) {
            redirect('/Login');
        }

//        $where['status'] = array('egt', 0);
        $where['uid'] = userid();
        $mo = M();
        $count = $mo->table('award_log')->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $mo->table('award_log as log')->join('award_activity_item as item on log.aiid = item.id')
            ->field('log.id as id, log.aiid as aiid, log.create_time as create_time, log.modify_time as modify_time, log.status as status,case when status=0 then \'未发放\' else \'已发放\' end as issued_status, item.name as itemname, case when log.modify_time is not null then log.modify_time else \'\' end as issued_time ')
            ->where($where)->order('create_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
//        $this->assign('prompt_text', D('Text')->get_content('award_log'));
        $this->display();
    }

    public function install()
    {
    }

}

?>
