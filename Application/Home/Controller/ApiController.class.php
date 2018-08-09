<?php
namespace Home\Controller;

class ApiController extends HomeController
{
	//
	public function marketlist(){
		$marketarr = C('market');
		//print_r($marketarr);
		$ik = 0;
		foreach($marketarr as $k=>$v){
			$ren = explode("_",$v['name']);
			$ret[$v['name']."t"]['name'] = $ren[0];
			$ret[$v['name']."t"]['name_cn'] = M('coin')->where(array('name' => $ren[0]))->getField("title");;
			$ret[$v['name']."t"]['market'] = $v['name']."t";
			$ret[$v['name']."t"]['price'] = $v['new_price'];
			$ret[$v['name']."t"]['buy_price'] = $v['buy_price'];
			$ret[$v['name']."t"]['sell_price'] = $v['sell_price'];
			$ret[$v['name']."t"]['change'] = $v['change'];
			$ret[$v['name']."t"]['max24hr'] = $v['max_price'];
			$ret[$v['name']."t"]['min24hr'] = $v['min_price'];
			$ret[$v['name']."t"]['volume'] = $v['volume'];
			$ik++;
		}
		//print_r($ret);
		echo json_encode($ret);
	}
	public function orderlist($market=NULL){
		if(!$market) return false;
		$market = substr($market,0,strlen($market)-1);
		$trade['buy'] = M()->query("select price,num,addtime from qq3479015851_trade where type=1 and market='".$market."' order by id desc limit 0,30");;
		$trade['sell'] = M()->query("select price,num,addtime from qq3479015851_trade where type=2 and market='".$market."' order by id desc limit 0,30");;
		//print_r($ret);
		$ret[$market."t"] = $trade;
		echo json_encode($ret);
	}
	public function Historylist($market=NULL){
		if(!$market) return false;
		$market = substr($market,0,strlen($market)-1);
		$trade = M()->query("select id as tradeid,price,num,addtime,type from qq3479015851_trade_log where market='".$market."' order by id desc limit 0,30");;
		//print_r($ret);
		foreach($trade as $k=>$v){
			$trades[$k] = $v;
			$trades[$k]['time'] = date("Y-m-d H:i:s",$v['addtime']);
			$trades[$k]['type'] = ($v['type']==1) ? "buy":"sell";
		}
		
		$ret[$market."t"] = $trades;
		echo json_encode($ret);
	}
}

?>
