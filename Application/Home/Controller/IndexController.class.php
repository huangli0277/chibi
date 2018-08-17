<?php
namespace Home\Controller;

class IndexController extends HomeController
{
    public function app()
    {
        $this->display();
    }

	public function index()
    {
        $indexAdver = (APP_DEBUG ? null : S('index_indexAdver'));

        if (!$indexAdver) {
            $indexAdver = M('Adver')->where(array('status' => 1))->order('id asc')->select();
            S('index_indexAdver', $indexAdver);
        }

        $this->assign('indexAdver', $indexAdver);//轮播图

        $information=[];
        $information[]=['type'=>'官方公告','list'=>(APP_DEBUG ? null : S('notice'))];//公告
        $information[]=['type'=>'行业资讯','list'=>(APP_DEBUG ? null : S('notice'))];//资讯
        $Model = M('Article');//文章表

        foreach ($information as $k => $v){
            if(!$v['list']) {
                $where=['type'=>$v['type'],'status'=>1];
                $information[$k]['list'] = $Model->where($where)->page(1, 2)->order('id desc')->select();
                foreach ($information[$k]['list'] as $kk => $vv){
                    $information[$k]['list'][$kk]['brief'] = mb_substr(preg_replace("/<[^>]+>/is", "", $vv['content']), 0, 150);
                }
            }
        }
        $this->assign('information', $information);//公告和资讯

        //设置热门文章
        $id = 19;
        $articletype = M('ArticleType')->where(array('id' => $id))->find();
        $where = array('type' => $articletype['name'],'status'=>1);
        $Model = M('Article');
        $hotlist = $Model->where($where)->order('hits desc')->limit(4)->select();
        foreach ($hotlist as $k => $v) {
            $hotlist[$k]['title'] = mb_substr($v['title'],0,14);
            //$hotlist[$k]['brief'] = mb_substr(preg_replace("/<[^>]+>/is", "", $v['content']), 0, 25);
        }
        $this->assign('hotlist', $hotlist);

        $indexLink = (APP_DEBUG ? null : S('index_indexLink'));

        if (!$indexLink) {
            $indexLink = M('Link')->where(array('status' => 1))->order('sort asc ,id desc')->select();
        }

        $is_login = false;
        if(userid()){
            $zj = 0;
            $CoinList = M('Coin')->where(array('status' => 1))->select();
            $UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
            $Market = M('Market')->where(array('status' => 1))->select();
            foreach ($Market as $k => $v) {
                $Market[$v['name']] = $v;
            }
            foreach ($CoinList as $k => $v) {


                if ($v['name'] == 'cny') {
                    $ky = round($UserCoin[$v['name']], 2) * 1;//可用数量
                    $dj = round($UserCoin[$v['name'] . 'd'], 2) * 1;//冻结数量
                    $zj = $zj + $ky + $dj;//计算预估总资产
                }else {
                    if($Market[C('market_type')[$v['name']]]['status'] != 1){
                        continue;
                    }
                    if ($Market[C('market_type')[$v['name']]]['new_price']) {
                        $jia = $Market[C('market_type')[$v['name']]]['new_price'];
                    }else {
                        $jia = 1;
                    }
                    $zj = round($zj + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;//计算预估总资产
                }
            }
            $is_login = true;
        }

        $marketsy = C('market');
        foreach ($marketsy as $key => $val) {
            $tmp[$key] = $val['id'];
        }
        array_multisort($tmp,SORT_ASC,$marketsy);

        foreach ($marketsy as $k => $v) {
            $data[$k]['xnb'] = strtoupper(explode('_', $v['name'])[0]);
            $data[$k]['rmb'] = strtoupper(explode('_', $v['name'])[1]);
            $data[$k]['url'] = str_replace("cny","cnyt",$v['name']);
            $data[$k]['title'] = str_replace("CNY","CNYT",$v['title']);
            $data[$k]['img'] = $v['xnbimg'];
            $data[$k]['new_price'] = $v['new_price'];
            $data[$k]['main_coin'] = $v['main_coin'];
            $data[$k]['volume'] = $v['volume'];
            $data[$k]['mname'] = $v['mname'];
            $data[$k]['volumes'] = round($v['volume']*$v['new_price'],2);
            //$data[$k]['change'] = $v['change']>0 "+".$v['change']:$v['change'];
            if($v['change']>0){
                $data[$k]['change'] =  "+".round($v['change'],2);
            }else{
                $data[$k]['change'] =  round($v['change'],2);
            }
            if($v['change']<0) $data[$k]['class']="red";
        }
        $this->assign('zj',$zj);
        $this->assign('is_login',$is_login);
        $this->assign('lists', $data);

        $this->display();
    }

	public function monesay($monesay = NULL)
	{
	}

	public function install()
	{
	}

    public function fragment()
    {
        $ajax = new AjaxController();
        $data  = $ajax->allcoin('');
        $this->assign('data', $data);
        $this->display('Index/d/fragment');
    }

    public function newPrice()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
        //var_dump(C('market'));
        $data = $this->allCoinPrice();
        //var_dump($data);
       // exit;
        $last_data = S('ajax_all_coin_last');
        $_result = array();
        if (empty($last_data)) {
            foreach (C('market') as $k => $v) {
                $_result[$v['id'] . '-' . strtoupper($v['xnb'])] =  $data[$k][1] . '-0.0';
            }
        } else {
            foreach (C('market') as $k => $v) {
                $_result[$v['id'] . '-' . strtoupper($v['xnb'])] =  $data[$k][1] . '-' . ($data[$k][1] - $last_data[$k][1]);
            }
        }

        S('ajax_all_coin_last', $data);

        $data = json_encode(
            array(
                'result' => $_result,
            )
        );
        exit($data);

        //exit('{"result":{"25-BTC":"4099.0-0.0","1-LTC":"26.43--0.22650056625141082","26-DZI":"1.72-0.0","6-DOGE":"0.00151-0.0"},"totalPage":5}');
    }


    protected function allCoinPrice()
    {
        $data = (APP_DEBUG ? null : S('allcoin'));

        // 市场交易记录
        $marketLogs = array();
        foreach (C('market') as $k => $v) {
            $tradeLog = M('TradeLog')->where(array('status' => 1, 'market' => $k))->order('id desc')->limit(50)->select();
            $_data = array();
            foreach ($tradeLog as $_k => $v) {
                $_data['tradelog'][$_k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $_data['tradelog'][$_k]['type'] = $v['type'];
                $_data['tradelog'][$_k]['price'] = $v['price'] * 1;
                $_data['tradelog'][$_k]['num'] = round($v['num'], 6);
                $_data['tradelog'][$_k]['mum'] = round($v['mum'], 2);
            }
            $marketLogs[$k] = $_data;
        }

        $themarketLogs = array();
        if ($marketLogs) {
            $last24 = time() - 86400;
            $_date = date('m-d H:i:s', $last24);
            foreach (C('market') as $k => $v) {
                $tradeLog = isset($marketLogs[$k]['tradelog']) ? $marketLogs[$k]['tradelog'] : null;
                if ($tradeLog) {
                    $sum = 0;
                    foreach ($tradeLog as $_k => $_v) {
                        if ($_v['addtime'] < $_date) {
                            continue;
                        }
                        $sum += $_v['mum'];
                    }
                    $themarketLogs[$k] = $sum;
                }
            }
        }

        foreach (C('market') as $k => $v) {
            $data[$k][0] = $v['title'];
            $data[$k][1] = round($v['new_price'], $v['round']);
            $data[$k][2] = round($v['buy_price'], $v['round']);
            $data[$k][3] = round($v['sell_price'], $v['round']);
            $data[$k][4] = isset($themarketLogs[$k]) ? $themarketLogs[$k] : 0;//round($v['volume'] * $v['new_price'], 2) * 1;
            $data[$k][5] = '';
            $data[$k][6] = round($v['volume'], 2) * 1;
            $data[$k][7] = round($v['change'], 2);
            $data[$k][8] = $v['name'];
            $data[$k][9] = $v['xnbimg'];
            $data[$k][10] = '';
        }

        return $data;
    }

}

?>
