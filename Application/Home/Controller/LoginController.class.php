<?php

namespace Home\Controller;

class LoginController extends HomeController
{
    public function index()
    {
        if(userid()){
            if(C('DEFAULT_V_LAYER') == 'Mview'){
                redirect('/Finance');
            }else{
                redirect('/User');
            }
        }else{
            $this->display();
        }
    }

    public function getuid()
    {
        $arr["userid"] = userid();
        $arr["moble"] = username(userid());
        $arr["nickName"] = session("nickName");
        echo json_encode($arr);
    }

    public function register()
    {
        if(userid()){
            if(C('DEFAULT_V_LAYER') == 'Mview'){
                redirect('/Finance');
            }else{
                redirect('/User');
            }
        }else{
            if($_GET['invit']){
                session('invit',$_GET['invit']);
            }
            $this->assign('invitCode', $_GET['invit']);//显示邀请码
            $this->display();
        }
    }

    public function webreg()
    {
        $this->display();
    }


    public function upregister($username = '', $password = '', $repassword = '', $verify = '', $invit = '', $moble = '', $moble_verify = '')
    {
        if(userid()){
            redirect('/User');
        }
        if (M_ONLY == 0) {
//            if (!check_verify(strtoupper($verify))) {
//                $this->error('图形验证码错误!');
//            }

            if (!check($moble, 'moble')) {
                $this->error('手机号码格式错误！');
            }

            if (!check($password, 'password')) {
                $this->error('登录密码格式错误！');
            }

            if ($password != $repassword) {
                $this->error('确认登录密码错误！');
            }
        } else {

            if (!check($password, 'password')) {
                $this->error('登录密码格式错误！');
            }
        }
        $moble = $this->_replace_china_mobile($moble);
        $username = $moble;

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

        if(session('register_mobile_validation') != md5($moble)){
            $this->error('手机号码错误');
        }

        if (M('User')->where(array('username' => $username))->find()) {
            $this->error('用户名已存在');
        }

        if (!$invit) {
            $invit = session('invit');
        }

        $invituser = M('User')->where(array('invit' => $invit))->find();

        if (!$invituser) {
            $invituser = M('User')->where(array('id' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('username' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('moble' => $invit))->find();
        }

        if ($invituser) {
            $invit_1 = $invituser['id'];
            $invit_2 = $invituser['invit_1'];
            $invit_3 = $invituser['invit_2'];
        } else {
            $invit_1 = 0;
            $invit_2 = 0;
            $invit_3 = 0;
        }

        for (; true;) {
            $tradeno = tradenoa();

            if (!M('User')->where(array('invit' => $tradeno))->find()) {
                break;
            }
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_coin write ');
        $rs = array();
        $rs[] = $mo->table('qq3479015851_user')->add(array('username' => $username, 'moble' => $moble, 'mobletime' => time(), 'password' => $password, 'invit' => $tradeno, 'tpwdsetting' => 1, 'invit_1' => $invit_1, 'invit_2' => $invit_2, 'invit_3' => $invit_3, 'addip' => get_client_ip(), 'addr' => get_city_ip(), 'addtime' => time(), 'endtime' => time(), 'status' => 1));
        $rs[] = $mo->table('qq3479015851_user_coin')->add(array('userid' => $rs[0], 'doge' => 100));
        //推荐赠送cnut+doge，冻结
//		$istj = M()->query("SELECT count(id) as coun FROM  `qq3479015851_user` where invit_1 = '".$invit_1."' ");
//		$tj_num = $istj[0]['coun'];
//		if($tj_num){
//			$pri = 100;
//			M()->execute("UPDATE `qq3479015851_user_coin` SET  `cnutd` =  cnutd+$pri,`doged` =  doged+$pri  WHERE userid ='".$invit_1."';");
//			M()->execute("UPDATE `qq3479015851_user` SET  `isnrt` =  $tj_num WHERE id ='".$invit_1."';");
//
//			
//		}


        if (check_arr($rs)) {
            $mo->execute('commit');
            //$mo->execute('unlock tables');
            session('reguserId', $rs[0]);
            session('real_verify',null);
            $this->success('注册成功！');
        } else {
            $mo->execute('rollback');
            session('real_verify', null);
            $this->error('注册失败！');
        }
    }


    public function check_moble($moble = 0)
    {
        $moble = $this->_replace_china_mobile($moble);
        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码已存在！');
        }

        $this->success('');

    }

    public function check_mail($email = ''){
        if (!check($email, 'email')) {
            $this->error('邮箱格式错误！');
        }

        if (M('User')->where(array('email' => $email))->find()) {
            $this->error('邮箱已存在！');
        }
        $this->success('');
    }

    /**
     * 邮箱发送验证码
     * @param $email
     * @param $verifyEmail
     */
    public function real_mail($email, $verifyEmail)
    {

        if (!check_verify(strtoupper($verifyEmail))) {
            $this->error('图形验证码错误!');
        }

        if (!check($email, 'email')) {
            $this->error('邮箱格式错误！');
        }

        if (M('User')->where(array('email' => $email))->find()) {
            $this->error('邮箱已存在！');
        }

        $code = rand(111111, 999999);
        session('real_email_verify', $code);
        session('verify#time', time());
        $content = 'CC注册，your code：' . $code;
        SendEmail(array('to'=>$email, 'subject'=>'CC注册', 'content'=>$content));//发送邮件
        session('register_email_validation', md5($email));
        if (MOBILE_CODE == 0) {
            $this->success('目前是演示模式,请输入' . $code);
        } else {
            $this->success('验证码已发送');
        }
    }


    public function upregisterEmail($username = '', $password = '', $repassword = '', $verify = '', $invit = '', $email = '', $emailVerify = '')
    {
        if (M_ONLY == 0) {
            if (!check($password, 'password')) {
                $this->error('登录密码格式错误！');
            }
        } else {

            if (!check($password, 'password')) {
                $this->error('登录密码格式错误！');
            }
        }

        if (!check($email, 'email')) {
            $this->error('邮件格式错误！');
        }
        $username = $email;
        if (!check($emailVerify, 'd')) {
            $this->error('邮箱验证码格式错误！');
        }

        if(session('register_email_validation') != md5($email)){
            $this->error('邮箱错误!');
        }
        $this->_verify_email_count_check($emailVerify,session('real_email_verify'));

        if (M('User')->where(array('email' => $email))->find()) {
            $this->error('邮箱已存在！');
        }

        if (M('User')->where(array('username' => $username))->find()) {
            $this->error('用户名已存在');
        }

        if (!$invit) {
            $invit = session('invit');
        }

        $invituser = M('User')->where(array('invit' => $invit))->find();

        if (!$invituser) {
            $invituser = M('User')->where(array('id' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('username' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('moble' => $invit))->find();
        }

        if ($invituser) {
            $invit_1 = $invituser['id'];
            $invit_2 = $invituser['invit_1'];
            $invit_3 = $invituser['invit_2'];
        } else {
            $invit_1 = 0;
            $invit_2 = 0;
            $invit_3 = 0;
        }

        for (; true;) {
            $tradeno = tradenoa();

            if (!M('User')->where(array('invit' => $tradeno))->find()) {
                break;
            }
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_coin write ');
        $rs = array();
        $moble = '';
        $rs[] = $mo->table('qq3479015851_user')->add(array('username' => $username,'email' => $email, 'moble' => $moble, 'mobletime' => time(), 'password' => $password, 'invit' => $tradeno, 'tpwdsetting' => 1, 'invit_1' => $invit_1, 'invit_2' => $invit_2, 'invit_3' => $invit_3, 'addip' => get_client_ip(), 'addr' => get_city_ip(), 'addtime' => time(), 'endtime' => time(), 'status' => 1));
        $rs[] = $mo->table('qq3479015851_user_coin')->add(array('userid' => $rs[0], 'doge' => 100));
        //推荐赠送cnut+doge，冻结
//		$istj = M()->query("SELECT count(id) as coun FROM  `qq3479015851_user` where invit_1 = '".$invit_1."' ");
//		$tj_num = $istj[0]['coun'];
//		if($tj_num){
//			$pri = 100;
//			M()->execute("UPDATE `qq3479015851_user_coin` SET  `cnutd` =  cnutd+$pri,`doged` =  doged+$pri  WHERE userid ='".$invit_1."';");
//			M()->execute("UPDATE `qq3479015851_user` SET  `isnrt` =  $tj_num WHERE id ='".$invit_1."';");
//
//
//		}


        if (check_arr($rs)) {
            $mo->execute('commit');
            //$mo->execute('unlock tables');
            session('reguserId', $rs[0]);
            session('real_email_verify', null);
            $this->success('注册成功！');
        } else {
            $mo->execute('rollback');
            session('real_email_verify', null);
            $this->error('注册失败！'.$rs);
        }
    }


    public function check_pwdmoble($moble = 0)
    {
        $moble = $this->_replace_china_mobile($moble);
        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (!M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码不存在！');
        }

        $this->success('');

    }
    public function check_pwdemail($email = '')
    {
        if (!check($email, 'email')) {
            $this->error('邮箱格式错误！');
        }
        if (!M('User')->where(array('email' => $email))->find()) {
            $this->error('邮箱不存在！');
        }
        $this->success('');

    }

    public function real($moble, $verify)
    {

        if (!check_verify(strtoupper($verify))) {
            $this->error('图形验证码错误!');
        }

        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }
        $moble = $this->_replace_china_mobile($moble);
        if (M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码已存在！');
        }

        $code = rand(111111, 999999);
        session('real_verify', $code);
        session('verify#time', time());
        $content = 'CC注册，your code：' . $code;
        //$sen = send_moble($moble, $content);
        if (SendText($moble, $code, "reg")) {
            session('register_mobile_validation', md5($moble));
            if (MOBILE_CODE == 0) {
                $this->success('目前是演示模式,请输入' . $code);
            } else {
                $this->success('验证码已发送');
            }
        } else {
            //echo '-----------';
            //var_dump($aaa);
            //die;
            $this->error('验证码发送失败,请重发');
        }
    }


    public function register2()
    {
        if (!session('reguserId')) {
            redirect('/Login');
        }
        $this->display();
    }


    public function paypassword()
    {
        if (!session('reguserId')) {
            redirect('/Login');
        }
        $this->display();
    }


    public function upregister2($paypassword, $repaypassword)
    {
        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if ($paypassword != $repaypassword) {
            $this->error('确认密码错误！');
        }

        if (!session('reguserId')) {
            $this->error('非法访问！');
        }

        if (M('User')->where(array('id' => session('reguserId'), 'password' => $paypassword))->find()) {
            $this->error('交易密码不能和登录密码一样！');
        }

        if (M('User')->where(array('id' => session('reguserId')))->save(array('paypassword' => $paypassword))) {
            $this->success('成功！');
        } else {
            $this->error('失败！');
        }
    }

    public function register3()
    {
        if (!session('reguserId')) {
            redirect('/Login');
        }
        $this->display();
    }

    public function truename()
    {
        if (!session('reguserId')) {
            redirect('/Login');
        }
        $this->display();
    }


    public function upregister3($truename, $idcard)
    {
        if (!check($truename, 'truename')) {
            $this->error('真实姓名格式错误！');
        }
        $idcard = preg_replace('# #', '', $idcard);
        if (!check($idcard, 'idcard')) {
            $this->error('身份证号格式错误！');
        }

        if (!session('reguserId')) {
            $this->error('非法访问！');
        }
        $user = M('User')->where(array('idcard' => $idcard))->find();
        if ($user) {
            $this->error('您的身份证号码已经认证<br>认证账号为：' . $user["username"] . '！');
        }
        if (M('User')->where(array('id' => session('reguserId')))->save(array('truename' => $truename, 'idcard' => $idcard))) {
            $this->success('成功！');
        } else {
            $this->error('失败！');
        }
    }

    public function register4()
    {

        if (!session('reguserId')) {
            redirect('/Login');
        }

        $user = M('User')->where(array('id' => session('reguserId')))->find();


        if (!$user) {
            $this->error('请先注册');
        }
        if ($user['regaward'] == 0) {
            if (C('reg_award') == 1 && C('reg_award_num') > 0) {
                M('UserCoin')->where(array('userid' => session('reguserId')))->setInc(C('reg_award_coin'), C('reg_award_num'));
                M('User')->where(array('id' => session('reguserId')))->save(array('regaward' => 1));
            }
        }

        session('userId', $user['id']);
        session('userName', $user['username']);
        $this->assign('user', $user);
        $this->display();
    }


    public function info()
    {

        if (!session('reguserId')) {
            redirect('/Login');
        }

        $user = M('User')->where(array('id' => session('reguserId')))->find();


        if (!$user) {
            $this->error('请先注册');
        }
        if ($user['regaward'] == 0) {
            if (C('reg_award') == 1 && C('reg_award_num') > 0) {
                M('UserCoin')->where(array('id' => session('reguserId')))->setInc(C('reg_award_coin'), C('reg_award_num'));
                M('User')->where(array('id' => session('reguserId')))->save(array('regaward' => 1));
            }
        }

        session('userId', $user['id']);
        session('userName', $user['username']);
        $this->assign('user', $user);
        $this->display();
    }


    public function chkUser($username)
    {
        if (!check($username, 'username')) {
            $this->error('用户名格式错误！');
        }

        if (M('User')->where(array('username' => $username))->find()) {
            $this->error('用户名已存在');
        }

        $this->success('');
    }

    public function submit($username = "", $password = "", $moble = "", $verify = NULL)
    {
        if(userid()){
            redirect('/User');
        }
        if (C('login_verify')) {
            if (!check_verify(strtoupper($verify))) {
                $this->error('图形验证码错误!');
            }
        }

        if (M_ONLY == 0) {
            if (check($username, 'email')) {
                $user = M('User')->where(array('email' => $username))->find();
                $remark = '通过邮箱登录';
            }

            if (!$user && check($username, 'moble')) {
                $u =  $this->_replace_china_mobile($username);
                $user = M('User')->where(array('moble' => $u))->find();
                $remark = '通过手机号登录';
            }

            if (!$user) {
                $user = M('User')->where(array('username' => $username))->find();
                $remark = '通过用户名登录';
            }
        } else {
            if (check($moble, 'moble')) {
                $moble = $this->_replace_china_mobile($moble);
                $user = M('User')->where(array('moble' => $moble))->find();
                $remark = '通过手机号登录';
            }
        }

        if (!$user) {
            $this->error('用户不存在！');
        }

        if (!check($password, 'password')) {
            $this->error('登录密码格式错误！');
        }

        if (strtoupper($password) != strtoupper($user['password'])) {
            $this->error('登录密码错误！');
        }

        if ($user['status'] != 1) {
            $this->error('你的账号已冻结请联系管理员！');
        }


        $ip = get_client_ip();
        $logintime = time();
        $token_user = md5($user['id'] . $logintime);
        session('token_user', $token_user);

        $mo = M();
        //$mo->execute('set autocommit=0');
        //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_log write ');
        $rs = array();
        $rs[] = $mo->table('qq3479015851_user')->where(array('id' => $user['id']))->setInc('logins', 1);

        $rs[] = $mo->table('qq3479015851_user')->where(array('id' => $user['id']))->save(array('token' => $token_user));

        $rs[] = $mo->table('qq3479015851_user_log')->add(array('userid' => $user['id'], 'type' => '登录', 'remark' => $remark, 'addtime' => $logintime, 'addip' => $ip, 'addr' => get_city_ip(), 'status' => 1));

        if (check_arr($rs)) {
            //$mo->execute('commit');
            //$mo->execute('unlock tables');

            if (!$user['invit']) {
                for (; true;) {
                    $tradeno = tradenoa();
                    if (!M('User')->where(array('invit' => $tradeno))->find()) {
                        break;
                    }
                }

                M('User')->where(array('id' => $user['id']))->setField('invit', $tradeno);
            }

            session('userId', $user['id']);
            session('userName', $user['username']);
            session('nickName', $user['nickname']);


            if (!$user['paypassword']) {
                session('regpaypassword', $rs[0]);
                session('reguserId', $user['id']);
            }

            if (!$user['truename']) {
                session('regtruename', $rs[0]);
                session('reguserId', $user['id']);
            }
            session('qq3479015851_already', 0);

//            $this->success('登录成功！');
            $this->success(L('WELCOME'));
        } else {
            session('qq3479015851_already', 0);
            //$mo->execute('rollback');
            $this->error('登录失败！');
        }
    }
/*
    function calculateAward($user)
    {
        // 登录验证通过之后，获取用户可用于抽奖的次数
        // 1. 获取当前可用的抽奖活动信息
        $mo = M();
        $award_activity = $mo->table('award_activity')->where(array('is_available' => '1'))->find();
        $award_map['aid'] = array('eq', $award_activity['id']);
        $award_activity_item = $mo->table('award_activity_item')->where($award_map)->select();
        session('award_activity', $award_activity);
        session('award_activity_item', $award_activity_item);

        $userid = $user['id'];

        // 2. 遍历抽奖预置条件获取可抽奖次数
        $positiveCount = $this->calculateUserAwardAmount($userid, 0);
        // 2.1 正向获取抽奖次数
        $negativeCount = $this->calculateUserAwardAmount($userid, 1);
        // 2.2 反向扣除已用次数

        // 3. 写入Session
        session('award_available_count', $positiveCount - $negativeCount);
    }

    function calculateUserAwardAmount($userid, $type)
    {
        $awardCount = 0;

        if ($type == 0) {
            $m = M();
            //注册并实名，获得抽奖券一个
            $userInfo = $m->table('qq3479015851_user')->where(array('id' => $userid, 'idcardauth' => 1))->find();
            if ($userInfo != null)
                $awardCount += 1;

            //推荐每满多少个人并完成实名，获得抽奖券一个
            $a = $m->table('qq3479015851_user')->where(array('invit_1' => $userid, 'idcardauth' => 1))->count();
            $awardCount += intval(floor($a / 5));

            //交易每满多少金额获得抽奖券一个
            $b = $m->table('qq3479015851_trade')->where(array('userid' => $userid))->sum('mum');
            $awardCount += intval(floor($b / 500));

            //单次充值每达到多少送一个抽奖券
            $chargeMap['uid'] = $userid;
            $chargeMap['num'] = array('egt', 500);
            $chargeResult = $m->table('a_ctc')->where($chargeMap)->select();
            foreach ($chargeResult as $value) {
                $awardCount += intval(floor($value['num'] / 500));
            }
            return $awardCount;
        } else {
            $m = M();
            $awardId = session('award_activity')['id'];

            //查找抽奖记录
            $awardLogCount = $m->table('award_log')->where(array('aid' => $awardId, 'uid' => $userid))->count();
            return $awardLogCount;
        }
    }*/

    public function loginout()
    {
        session(null);
        redirect('/');
    }

    public function findpwd()
    {
        if(userid()){
            redirect('/');
            return;
        }
        if (IS_POST) {
            $input = I('post.');
            $moble = $input['moble'];
            $moble = $this->_replace_china_mobile($moble);
            //判断参数前校验手机号码是否被篡改
            //修改登录密码
            $fakeHash = md5($moble);
            $realHash = session('modify_password_validation');
            if ($fakeHash != $realHash)
                $this->error('手机号码错误');

//            if (M_ONLY == 0) {
//                if (!check_verify(strtoupper($input['verify']))) {
//                    $this->error('图形验证码错误!');
//                }
//
//                if (!check($input['username'], 'username')) {
//                    $this->error('用户名格式错误！');
//                }
//
//                if (!check($input['moble'], 'moble')) {
//                    $this->error('手机号码格式错误！');
//                }
//
//                if (!check($input['moble_verify'], 'd')) {
//                    $this->error('短信验证码格式错误！');
//                }
//
//                if ($input['moble_verify'] != session('findpwd_verify')) {
//                    $this->error('短信验证码错误！');
//                }
//
//                $user = M('User')->where(array('username' => $input['username']))->find();
//
//
//                if (!$user) {
//                    $this->error('用户名不存在！');
//                }
//
//                if ($user['moble'] != $input['moble']) {
//                    $this->error('用户名或手机号码错误！');
//                }
//
//                if (!check($input['password'], 'password')) {
//                    $this->error('新登录密码格式错误！');
//                }
//
//
//                if ($input['password'] != $input['repassword']) {
//                    $this->error('确认密码错误！');
//                }
//
//
//                $mo = M();
//                $mo->execute('set autocommit=0');
//                //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_log write ');
//                $rs = array();
//                $rs[] = $mo->table('qq3479015851_user')->where(array('id' => $user['id']))->save(array('password' => md5($input['password'])));
//
//                if (check_arr($rs)) {
//                    $mo->execute('commit');
//                    //$mo->execute('unlock tables');
//                    $this->success('修改成功');
//                } else {
//                    $mo->execute('rollback');
//                    $this->error('修改失败');
//                }
//
//
//            } else {
//                if (!check($input['moble'], 'moble')) {
//                    $this->error('手机号码格式错误！');
//                }
//
//                $user = M('User')->where(array('moble' => $input['moble']))->find();
//
//                if (!$user) {
//                    $this->error('不存在该手机号码');
//                }
//
//                if (!check($input['moble_verify'], 'd')) {
//                    $this->error('短信验证码格式错误！');
//                }
//
//                if ($input['moble_verify'] != session('findpwd_verify')) {
//                    $this->error('短信验证码错误！');
//                }
//                session("findpwdmoble", $user['moble']);
//                $this->success('验证成功');
//            }


            if(check($moble, 'email')){//通过邮箱登陆

            }
            else if (check($moble, 'moble')){//通过手机登录
                $user = M('User')->where(array('moble' => $moble))->find();

                if (!$user) {
                    $this->error('不存在该手机号码');
                }

                if (!check($input['moble_verify'], 'd')) {
                    $this->error('短信验证码格式错误！');
                }

                $this->_verify_count_check($input['moble_verify'],session('findpwd_verify'));
                session("findpwdmoble", $moble);
                session('findpwd_verify',null);
                $this->success('验证成功');
            }else{
                $this->error('账户类型不支持');
            }

        } else {
            $this->display();
        }
    }

    public function findpwd_email(){
        if (IS_POST) {
            $input = I('post.');

            //判断参数前校验邮箱号码是否被篡改
            //修改登录密码
            $fakeHash = md5($input['email']);
            $realHash = session('modify_password_validation');
            if ($fakeHash != $realHash)
                $this->error('邮箱错误');

            if (check($input['email'], 'email')){//通过邮箱登录
                $user = M('User')->where(array('email' => $input['email']))->find();

                if (!$user) {
                    $this->error('不存在该邮箱信息');
                }

                if (!check($input['email_verify'], 'd')) {
                    $this->error('邮箱验证码格式错误！');
                }

                $this->_verify_email_count_check($input['email_verify'],session('findpwd_verify'));
                session("findpwdemail", $user['email']);
                $this->success('验证成功');
            }else{
                $this->error('账户类型不支持');
            }

        } else {
            $this->display();
        }
    }


    public function findpwdconfirm()
    {

        if (empty(session('findpwdmoble')) && empty(session('findpwdemail'))) {
            session(null);
            redirect('/');
        }

        $this->display();
    }

    public function password_up($password = "")
    {
        $account_type = 'moble';
        $user_account = session('findpwdmoble');
        if(empty($user_account)){
            $user_account = session('findpwdemail');
            $account_type = 'email';
        }
        if (empty($user_account)) {
            $this->error('请返回第一步重新操作！');
        }

        if (!check($password, 'password')) {
            $this->error('新登录密码格式错误！');
        }

        if($account_type === 'moble'){
            $user = M('User')->where(array('moble' => session('findpwdmoble')))->find();

            if (!$user) {
                $this->error('不存在该手机号码');
            }

            if ($user['paypassword'] == $password) {
                $this->error("登录密码不能和交易密码一样");
            }


            $mo = M();
            $mo->execute('set autocommit=0');
            //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_log write ');
            $rs = array();
            $rs[] = $mo->table('qq3479015851_user')->where(array('moble' => $user['moble']))->save(array('password' => $password));

            if (check_arr($rs)) {
                $mo->execute('commit');
                //$mo->execute('unlock tables');
                $this->success('操作成功');
            } else {
                $mo->execute('rollback');
                $this->error('操作失败');
            }
        }else if($account_type === 'email'){
            $user = M('User')->where(array('email' => $user_account))->find();

            if (!$user) {
                $this->error('不存在该邮箱账号');
            }

            if ($user['paypassword'] == $password) {
                $this->error("登录密码不能和交易密码一样");
            }


            $mo = M();
            $mo->execute('set autocommit=0');
            //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_log write ');
            $rs = array();
            $rs[] = $mo->table('qq3479015851_user')->where(array('email' => $user['email']))->save(array('password' => $password));

            if (check_arr($rs)) {
                $mo->execute('commit');
                //$mo->execute('unlock tables');
                $this->success('操作成功');
            } else {
                $mo->execute('rollback');
                $this->error('操作失败');
            }
        }else{
            $this->error('不支持的类型');
        }
    }

    public function findpwdinfo()
    {
        if (empty(session('findpwdmoble')) && empty(session('findpwdemail'))) {
            session(null);
            redirect('/');
        }
        session(null);
        $this->display();
    }


    public function findpaypwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check($input['username'], 'username')) {
                $this->error('用户名格式错误！');
            }

            $moble = $this->_replace_china_mobile($input['moble']);
            if (!check($moble, 'moble')) {
                $this->error('手机号码格式错误！');
            }

            if (!check($input['moble_verify'], 'd')) {
                $this->error('短信验证码格式错误！');
            }

            $this->_verify_count_check($input['moble_verify'],session('findpaypwd_verify'));

            $user = M('User')->where(array('username' => $input['username']))->find();

            if (!$user) {
                $this->error('用户名不存在！');
            }

            if ($user['moble'] != $moble) {
                $this->error('用户名或手机号码错误！');
            }

            if (!check($input['password'], 'password')) {
                $this->error('新交易密码格式错误！');
            }

            if ($input['password'] != $input['repassword']) {
                $this->error('确认密码错误！');
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_log write ');
            $rs = array();
            $rs[] = $mo->table('qq3479015851_user')->where(array('id' => $user['id']))->save(array('paypassword' => $input['password']));

            if (check_arr($rs)) {
                $mo->execute('commit');
                //$mo->execute('unlock tables');
                session('findpaypwd_verify',null);
                $this->success('修改成功');
            } else {
                $mo->execute('rollback');
                $this->error('修改失败' . $mo->table('qq3479015851_user')->getLastSql());
            }
        } else {
            $this->display();
        }
    }


}

?>
