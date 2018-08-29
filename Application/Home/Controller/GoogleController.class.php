<?php
/**
 * Created by PhpStorm.
 * User: BBB
 * Date: 2018/8/29
 * Time: 14:35
 */

namespace Home\Controller;



class GoogleController extends HomeController
{
    public function index(){
        if (!userid()) {
            redirect('/Login');
        }
        $this->display();
    }

}