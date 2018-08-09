<?php
/**
 * Created by PhpStorm.
 * User: BBB
 * Date: 2018/8/4
 * Time: 9:46
 */

namespace Home\Controller;


class MarketController extends HomeController
{
    public function index(){
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
            $data[$k]['name'] = $v['name'];
            $data[$k]['volumes'] = round($v['volume']*$v['new_price'],2);
            //$data[$k]['change'] = $v['change']>0 "+".$v['change']:$v['change'];
            if($v['change']>0){
                $data[$k]['change'] =  "+".round($v['change'],2);
            }else{
                $data[$k]['change'] =  round($v['change'],2);
            }
            if($v['change']<0) $data[$k]['class']="red";
        }
        $this->assign('lists', $data);

        $this->display();
    }

}