<?php

namespace Home\Controller;

class ChartController extends HomeController
{
    public function specialty()
    {
        // TODO: SEPARATE
        $input = I('get.');
        $market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
        $this->assign('market', $market);
        $this->display();
    }

    public function getMarketOrdinaryJson()
    {
        // TODO: SEPARATE
        $input = I('get.');
        $market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);

        if (in_array($input['time'], $timearr)) {
            $time = $input['time'];
        }
        else {
            $time = 5;
        }

        $timeaa = (APP_DEBUG ? null : S('ChartgetMarketOrdinaryJsontime' . $market . $time));

        if (($timeaa + 60) < time()) {
            S('ChartgetMarketOrdinaryJson' . $market . $time, null);
            S('ChartgetMarketOrdinaryJsontime' . $market . $time, time());
        }

        $tradeJson = (APP_DEBUG ? null : S('ChartgetMarketOrdinaryJson' . $market . $time));

        if (!$tradeJson) {
            $tradeJson = M('TradeJson')->where(array(
                'market' => $market,
                'type'   => $time,
                'data'   => array('neq', '')
            ))->order('id desc')->limit(100)->select();
            S('ChartgetMarketOrdinaryJson' . $market . $time, $tradeJson);
        }

        krsort($tradeJson);

        foreach ($tradeJson as $k => $v) {
            $json_data[] = json_decode($v['data'], true);
        }

        exit(json_encode($json_data));
    }

    public function getMarketSpecialtyJson()
    {
        // TODO: SEPARATE
        $input = I('get.');
        $market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));
        $timearr = array(5, 15, 30, 60, 360, 1440);

        if (in_array($input['step'] / 60, $timearr)) {
            $time = $input['step'] / 60;
        } else {
            $time = 5;
        }

        $timeaa = (APP_DEBUG ? null : S('ChartgetMarketSpecialtyJsontime' . $market . $time));

        if (($timeaa + 60) < time()) {
            S('ChartgetMarketSpecialtyJson' . $market . $time, null);
            S('ChartgetMarketSpecialtyJsontime' . $market . $time, time());
        }

        $tradeJson = (APP_DEBUG ? null : S('ChartgetMarketSpecialtyJson' . $market . $time));

        if (!$tradeJson) {
            $tradeJson = M('TradeJson')->where(array(
                'market' => $market,
                'type' => $time,
                'data' => array('neq', '')
            ))->order('id asc')->select();
            S('ChartgetMarketSpecialtyJson' . $market . $time, $tradeJson);
        }

        $json_data = $data = array();
        foreach ($tradeJson as $k => $v) {
            $json_data[] = json_decode($v['data'], true);
        }

        foreach ($json_data as $k => $v) {
            $data[$k][0] = $v[0];
            $data[$k][1] = 0;
            $data[$k][2] = 0;
            $data[$k][3] = $v[2];
            $data[$k][4] = $v[5];
            $data[$k][5] = $v[3];
            $data[$k][6] = $v[4];
            $data[$k][7] = $v[1];
        }

        exit(json_encode($data));
    }
}

?>