<?php
namespace Home\Controller;

class TradeController extends HomeController
{

    public function index($market = NULL)
    {
        $showPW = 1;
        $market = str_replace("cnyt", "cny", $market);
        $markety = $market;
        $names = array_column(C('market'), 'name');
        if(!in_array($markety,$names)){
            $this->error('请选择正确币种');
        }
        if (userid()) {
            $user = M('User')->where(array('id' => userid()))->find();

            if ($user['tpwdsetting'] == 3) {
                $showPW = 0;
            }

            if ($user['tpwdsetting'] == 1) {
                if (session(userid() . 'tpwdsetting')) {
                    $showPW = 2;
                }
            }

        }
        //check_server();

        if (!$market) {
            $market = C('market_mr');
        }


        $market_time_qq3479015851 = C('market')[$market]['begintrade'] . "-" . C('market')[$market]['endtrade'];

        $maktNames = explode('_', $market);
        $xnb = $maktNames[0];
        $rmb = $maktNames[1];
        $zr_jz = C('coin')[$xnb]['zr_jz'];
        $zc_jz = C('coin')[$xnb]['zc_jz'];

        $this->assign('market_time', $market_time_qq3479015851);
        $this->assign('showPW', $showPW);
        $this->assign('market', $market);
        $this->assign('zr_jz', $zr_jz);
        $this->assign('zc_jz', $zc_jz);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->buildHtml('index', './trade/index/market/' . $markety . 't/', '');//生成全静态
        $this->display();
    }

    /**
     * 手机端交易页面
     * @param null $market
     */
    public function mindex($market = NULL)
    {
        if (!userid()) {
        }
        $market = str_replace("cnyt", "cny", $market);
        check_server();
        if (!$market) {
            $market = C('market_mr');
        }
        $names = array_column(C('market'), 'name');
        if(!in_array($market,$names)){
            $this->error('请选择正确币种');
        }

        //---x修改--s17/2/72/9/55
        //查询全部币种的信息
        $dataall = array();
        $k = 0;
        $dq_title = '';
        foreach (C('market') as $i => $v) {
            $zhmoney = 0;
            //$dataall[$k][0] = $v['title'];
            $dataall[$k][0] = $v['mname'];
            $dataall[$k][1] = round($v['new_price'], $v['round']);
            $dataall[$k][2] = round($v['buy_price'], $v['round']);
            $dataall[$k][3] = round($v['sell_price'], $v['round']);
            $dataall[$k][4] = round($v['volume'] * $v['new_price'], 2) * 1;
            $dataall[$k][5] = '';
            $dataall[$k][6] = round($v['volume'], 2) * 1;
            $dataall[$k][7] = round($v['change'], 2);
            $dataall[$k][8] = str_replace("cny", "cnyt", $v['name']);
            $dataall[$k]['main_coin'] = $v['main_coin'];
            $dataall[$k]['url'] = str_replace("cny", "cnyt", $v['name']);
            if ($v['name'] == $market) {
                $dq_title = str_replace("CNY", "CNYT", $v['title']);
            }
            $dataall[$k][9] = '/Upload/coin/' . $v['xnbimg'];
            $dataall[$k][10] = '';
            $k++;
        }

        $this->assign('dataall', $dataall);
        $this->assign('dq_title', $dq_title);
        //----x修改e
        //----x修改e
        //----17/2/27添加折合总资产s
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
                if ($Market[C('market_type')[$v['name']]]['new_price']) {
                    $jia = $Market[C('market_type')[$v['name']]]['new_price'];
                } else {
                    $jia = 1;
                }
                $coinList[$v['name']] = array('name' => $v['name'], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                $cny['zj'] = round($cny['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
            }
        }
        $this->assign('cny', $cny);
        //----17/2/27添加折合总资产s
        //----17/2/28交易密码
        $rs = M('User')->field('tpwdsetting')->where(array('id' => userid()))->find();

        if ($rs['tpwdsetting'] == 3) {
            $this->assign('tpwdok', $_COOKIE["tpwdsetting_" . userid()]);
        }
        $sxxb = explode('_', $market)[0];
        foreach ($CoinList as $sk => $sv) {
            if ($sv['name'] == $sxxb) {
                $stmp = $sv['js_lt'];
            }
        }

        $this->assign('stmp', $stmp);
        $this->assign('market', $market);
        $this->assign('xnb', explode('_', $market)[0]);
        $this->assign('rmb', explode('_', $market)[1]);
        $this->display();
    }

    public function ordinary($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }

        $this->assign('market', $market);
        $this->display();
    }

    public function profession($market = NULL)
    {
        $showPW = 1;
        $market = str_replace("cnyt", "cny", $market);
        $markety = $market;
        if (userid()) {
            $user = M('User')->where(array('id' => userid()))->find();

            if ($user['tpwdsetting'] == 3) {
                $showPW = 0;
            }

            if ($user['tpwdsetting'] == 1) {
                if (session(userid() . 'tpwdsetting')) {
                    $showPW = 2;
                }
            }
        }
        //check_server();

        if (!$market) {
            $market = C('market_mr');
        }


        $market_time_qq3479015851 = C('market')[$market]['begintrade'] . "-" . C('market')[$market]['endtrade'];

        $maktNames = explode('_', $market);
        $xnb = $maktNames[0];
        $rmb = $maktNames[1];
        $zr_jz = C('coin')[$xnb]['zr_jz'];
        $zc_jz = C('coin')[$xnb]['zc_jz'];

        $this->assign('market_time', $market_time_qq3479015851);
        $this->assign('showPW', $showPW);
        $this->assign('market', $market);
        $this->assign('zr_jz', $zr_jz);
        $this->assign('zc_jz', $zc_jz);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->buildHtml('profession', './trade/profession/market/' . $markety . 't/', '');//生成全静态
        $this->display();
    }

    public function info($market = NULL)
    {
        if (!userid()) {
        }
        $market = str_replace("cnyt", "cny", $market);
        check_server();

        if (!$market) {
            $market = C('market_mr');
        }

        $this->assign('market', $market);
        $this->assign('xnb', explode('_', $market)[0]);
        $this->assign('rmb', explode('_', $market)[1]);
        //$this->buildHtml($market,'./Trade/info/market/','');//生成全静态
        $this->display();
    }

    public function specialty($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }
        $this->assign('market', $market);
        $this->buildHtml('index', './trade/specialty/market/' . $market . '/', '');//生成全静态
        $this->display();
    }

    public function upTrade($paypassword = NULL, $market = NULL, $price, $num, $type)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }
        $userid = userid();
        $times = time() - 10;
        //$isck = M()->query("SELECT * FROM  `a_market` where userid = '$userid' and addtime > $times order by addtime desc  LIMIT 1");
        $isck = M()->table('a_market')->where(array('userid' => $userid, 'addtime' => array('gt', $times)))->order('addtime desc')->limit(1)->select();
        if ($isck) $this->error('请不要重复提交订单！');

        if (!C('market')[$market]['trade']) {
            $this->error('当前市场禁止交易，交易时间周一至周六10：00-22：00！');
        }
        if (C('market')[$market]['begintrade']) {
            $begintrade = C('market')[$market]['begintrade'];
        } else {
            $begintrade = "00:00:00";
        }

        if (C('market')[$market]['endtrade']) {
            $endtrade = C('market')[$market]['endtrade'];
        } else {
            $endtrade = "23:59:59";
        }


        $trade_begin_time = strtotime(date("Y-m-d") . " " . $begintrade);
        $trade_end_time = strtotime(date("Y-m-d") . " " . $endtrade);
        $cur_time = time();

        if ($cur_time < $trade_begin_time || $cur_time > $trade_end_time) {
            $this->error('当前市场禁止交易,交易时间为每日' . $begintrade . '-' . $endtrade);
        }


        if (!check($price, 'double')) {
            $this->error('交易价格格式错误');
        }

        if (!check($num, 'double')) {
            $this->error('交易数量格式错误');
        }

        if (($type != 1) && ($type != 2)) {
            $this->error('交易类型格式错误');
        }
        $user = M('User')->where(array('id' => userid()))->find();

        //每笔交易都不会输入交易密码
        if ($user['tpwdsetting'] == 3) {
        }
        //每笔交易都需要输入交易密码
        if ($user['tpwdsetting'] == 2) {
            if ($paypassword != $user['paypassword']) {
                $this->error('交易密码错误！');
            }
        }

        //每次登录只输入一次交易密码
        if ($user['tpwdsetting'] == 1) {
            if (!session(userid() . 'tpwdsetting')) {
                if ($paypassword != $user['paypassword']) {
                    $this->error('交易密码错误！');
                } else {
                    session(userid() . 'tpwdsetting', 1);
                }
            }
        }


        if (!C('market')[$market]) {
            $this->error('交易市场错误');
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }
        // TODO: SEPARATE

        $price = round(floatval($price), C('market')[$market]['round']);

        if (!$price) {
            $this->error('交易价格错误' . $price);
        }

        $num = round($num, 9 - C('market')[$market]['round']);

        if (!check($num, 'double')) {
            $this->error('交易数量错误');
        }
        //买
        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } //卖
        else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            $this->error('交易类型错误');
        }

        if ($max_price < $price) {
            $this->error('交易价格超过最大限制！');
        }

        if ($price < $min_price) {
            $this->error('交易价格超过最小限制！');
        }

        $hou_price = C('market')[$market]['hou_price'];
        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                // TODO: SEPARATE
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);

                if ($zhang_price < $price) {
                    $this->error('交易价格超过今日涨幅限制！');
                }
            }

            if (C('market')[$market]['die']) {
                // TODO: SEPARATE
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);

                if ($price < $die_price) {
                    $this->error('交易价格超过今日跌幅限制！');
                }
            }
        }
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            $trade_fee = C('market')[$market]['fee_buy'];

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$rmb] < $mum) {
                $this->error(C('coin')[$rmb]['title'] . '余额不足！');
            }
        } else if ($type == 2) {
            $trade_fee = C('market')[$market]['fee_sell'];

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . '余额不足！');
            }
        } else {
            $this->error('交易类型错误');
        }

        if (C('coin')[$xnb]['fee_bili']) {
            if ($type == 2) {
                // TODO: SEPARATE
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], C('market')[$market]['round']);

                if ($bili_user) {
                    // TODO: SEPARATE
                    $bili_keyi = round(($bili_user / 100) * C('coin')[$xnb]['fee_bili'], C('market')[$market]['round']);

                    if ($bili_keyi) {
                        //$bili_zheng = M()->query('select id,price,sum(num-deal)as nums from qq3479015851_trade where userid=' . userid() . ' and status=0 and type=2 and market like \'%' . $xnb . '%\' ;');
                        $bili_zheng = M()->table('qq3479015851_trade')
                            ->where(array('userid' => userid(), 'status' => 0, 'type' => 2, 'market' => array('like', '%'.$xnb.'%')))
                            ->field('id,price,sum(num-deal)as nums')->select();

                        if (!$bili_zheng[0]['nums']) {
                            $bili_zheng[0]['nums'] = 0;
                        }

                        $bili_kegua = $bili_keyi - $bili_zheng[0]['nums'];

                        if ($bili_kegua < 0) {
                            $bili_kegua = 0;
                        }

                        if ($bili_kegua < $num) {
                            $this->error('您的挂单总数量超过系统限制，您当前持有' . C('coin')[$xnb]['title'] . $bili_user . '个，已经挂单' . $bili_zheng[0]['nums'] . '个，还可以挂单' . $bili_kegua . '个', '', 5);
                        }
                    } else {
                        $this->error('可交易量错误');
                    }
                }
            }
        }

        if (C('coin')[$xnb]['fee_meitian']) {
            if ($type == 2) {
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], 8);

                if ($bili_user < 0) {
                    $this->error('可交易量错误');
                }

                $kemai_bili = ($bili_user / 100) * C('coin')[$xnb]['fee_meitian'];

                if ($kemai_bili < 0) {
                    $this->error('您今日只能再卖' . C('coin')[$xnb]['title'] . 0 . '个', '', 5);
                }

                $kaishi_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $jintian_sell = M('Trade')->where(array(
                    'userid' => userid(),
                    'addtime' => array('egt', $kaishi_time),
                    'type' => 2,
                    'status' => array('neq', 2),
                    'market' => array('like', '%' . $xnb . '%')
                ))->sum('num');

                if ($jintian_sell) {
                    $kemai = $kemai_bili - $jintian_sell;
                } else {
                    $kemai = $kemai_bili;
                }

                if ($kemai < $num) {
                    if ($kemai < 0) {
                        $kemai = 0;
                    }

                    $this->error('您的挂单总数量超过系统限制，您今日只能再卖' . C('coin')[$xnb]['title'] . $kemai . '个', '', 5);
                }
            }
        }

        if (C('market')[$market]['trade_min']) {
            if ($mum < C('market')[$market]['trade_min']) {
                $this->error('交易总额不能小于' . C('market')[$market]['trade_min']);
            }
        }

        if (C('market')[$market]['trade_max']) {
            if (C('market')[$market]['trade_max'] < $mum) {
                $this->error('交易总额不能大于' . C('market')[$market]['trade_max']);
            }
        }

        if (!$rmb) {
            $this->error('数据错误1');
        }

        if (!$xnb) {
            $this->error('数据错误2');
        }

        if (!$market) {
            $this->error('数据错误3');
        }

        if (!$price) {
            $this->error('数据错误4');
        }

        if (!$num) {
            $this->error('数据错误5');
        }

        if (!$mum) {
            $this->error('数据错误6');
        }

        if (!$type) {
            $this->error('数据错误7');
        }
        if ($type == 1) {
            $user_coin = M()->table('qq3479015851_user_coin')->where(array('userid' => userid()))->find();
            if ($user_coin[$rmb] < $mum) {
                $this->error(C('coin')[$rmb]['title'] . '余额不足！');
            }
        } else if ($type == 2) {
            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . '余额不足2！');
            }
        } else {
            $this->error('交易类型错误');
        }
        $rs = M()->table('a_market')->add(array('type' => $type, 'addtime' => time(), 'market' => $market, 'num' => $num, 'mum' => $mum, 'pri' => $price, 'fee' => $fee, 'userid' => userid()));
        $this->success('交易成功！');
    }

    public function chexiao($id)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($id, 'd')) {
            $this->error('请选择要撤销的委托！');
        }

        $trade = M('Trade')->where(array('id' => $id))->find();

        if (!$trade) {
            $this->error('撤销委托参数错误！');
        }

        if ($trade['userid'] != userid()) {
            $this->error('参数非法！');
        }
        //新撤销
        $time = time();
        $userid = userid();
//        $is_c = M()->query("select aid from a_auto where tid='$id' ;");
        $is_c = M()->table('a_auto')->field('aid')->where(array('tid' => $id))->select();
        if ($is_c) {
            $this->error('正在撤销中！');
        }
//        $is_t = M()->query("select id from qq3479015851_trade where id='$id' and status = 0 and userid = '$userid' ;");
        $is_t = M()->table('qq3479015851_trade')->where(array('id' => $id, 'status' => 0, 'userid' => $userid))->field('id')->select();
        if ($is_t) {
            $rs = M()->table('a_auto')->add(array('type' => 1, 'addtime' => $time, 'tid' => $id, 'uid' => $userid));
        } else {
            $this->error('已经撤销成功！');
        }
        if ($rs) {
            $this->success("操作成功，已进入撤销流程！");
        } else {
            $this->error('撤销委托错误！');
        }
    }
}

?>
