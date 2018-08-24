<?php
/**
 * Created by PhpStorm.
 * User: BBB
 * Date: 2018/8/14
 * Time: 11:40
 */

namespace Home\Controller;


class TaskController extends HomeController
{

    public function index(){
        $this->display();
    }
    public function lists($type=NULL)
    {
        $userid=userid();
        if (!userid()) {
            redirect('/#login');
        }
        //if($userid !=111908) $this->error('暂未开启，敬请期待');
        $where['userid'] = $userid;
        $where['start'] = 2;
        $allname = "发放类型";
        if($type=="myyq"){
            $name = "C计划-邀请奖励";
            $list = M('FenhongMyyq')->where($where)->order('id desc')->limit(0 , 40)->select();
            foreach($list as $k=>$v){
                $list[$k]['name'] = "邀请奖励";
                $list[$k]['num'] = round($v['num'],5);
                $list[$k]['tps'] = $v['uid'];

            }
        }
        if($type=="mywk"){
            $name = "C计划-挖矿解冻";
            $list = M()->query("SELECT * FROM  `b_wakuang` where userid = '$userid' ");
            foreach($list as $k=>$v){
                $list[$k]['name'] = $v['cnut'];
                $list[$k]['addtime'] = $v['jdtime'];
                $list[$k]['num'] = round($v['cnut']/100,4);
                $list[$k]['tps'] = round($v['cnut']/100*$v['yci'],2);

            }
        }
        if($type=="myfh"){
            $name = "C计划-持币分红";
            $list = M('FenhongMyfh')->where($where)->order('id desc')->limit(0 , 40)->select();
            foreach($list as $k=>$v){
                $list[$k]['name'] = "持币分红";
                $list[$k]['num'] = round($v['num'],5);
                $list[$k]['tps'] = $v['allnum'] > 0 ? "自己":"推荐";

            }
        }
        if($type=="myjy"){
            $name = "C计划-交易分红";
            //$count = M('FenhongMyjy')->where($where)->count();
            //$Page = new \Think\Page($count, 40);
            //$show = $Page->show();
            //$list = M('FenhongMyjy')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $list = M('FenhongMyjy')->where($where)->order('id desc')->limit(0 , 40)->select();
            foreach($list as $k=>$v){
                $list[$k]['name'] = "交易分红";
                $list[$k]['num'] = round($v['num'],5);
                $list[$k]['tps'] = $v['allnum'] > 0 ? "自己":"推荐";
            }

        }

        $this->assign('type', $type);
        $this->assign('name', $name);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function settings(){    
        $this->display();
    }

}