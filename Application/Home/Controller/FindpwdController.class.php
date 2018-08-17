<?php
namespace Home\Controller;

class FindpwdController extends HomeController
{

    public function check_moble($moble = 0)
    {

        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        $moble = $this->_replace_china_mobile($moble);

        if (M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码已存在！');
        }

        $this->success('');

    }


    public function check_pwdmoble($moble = 0)
    {

        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (!M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码不存在！');
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

        if (M('User')->where(array('moble' => $moble))->find()) {
            $this->error('手机号码已存在！');
        }

        $code = rand(111111, 999999);
        session('real_verify', $code);
        session('verify#time', time());
        $content = 'CC，您正在找回密码，验证码是：' . $code;

        if (SendText($moble, $code, 'fog')) {
            if (MOBILE_CODE == 0) {
                $this->success('目前是演示模式,请输入' . $code);
            } else {
                $this->success('验证码已发送');
            }
        } else {
            $this->error('验证码发送失败,请重发');
        }
    }


    public function paypassword()
    {
        if (!session('reguserId')) {
            redirect('/Login');
        }
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


    public function findpwd()
    {
        if (!session('userId')) {
            redirect('/Login');
        }
        if (IS_POST) {
            $input = I('post.');
            //判断参数前校验手机号码是否被篡改
            //修改支付密码
            $type = $input['type'];

            $user = M('User')->where(array('id' => userid()))->find();
            if (!$user) {
                $this->error('用户不存在');
            }

            $moble = $user['moble'];
            $email = $user['email'];
            $realHash = session('modify_password_validation');
            if($type == 'sms'){
                $fakeHash = md5($moble);
                if ($fakeHash != $realHash)
                    $this->error('手机号码错误');

                if (!check($moble, 'moble')) {
                    $this->error('手机号码格式错误！');
                }
                if (!check($input['moble_verify'], 'd')) {
                    $this->error('短信验证码格式错误！');
                }
            }else if($type == 'email'){
                $fakeHash = md5($email);
                if ($fakeHash != $realHash)
                    $this->error('邮箱错误！');

                if (!check($email, 'email')) {
                    $this->error('邮箱格式错误！');
                }
                if (!check($input['moble_verify'], 'd')) {
                    $this->error('邮箱验证码格式错误！');
                }
            }else{
                $this->error('交易方式错误！');
            }

            $this->_verify_count_check($input['moble_verify'],session('findpwd_verify'));
            if($type == 'sms'){
                session("findpaypwdmoble", $moble);
            }else if($type == 'email'){
                session("findpaypwdemail", $email);
            }
            session('findpwd_verify',null);
            $this->success('验证成功');

        } else {
            $this->display();
        }
    }


    public function findpwdconfirm()
    {
        if (empty(session('findpaypwdmoble')) && empty(session('findpaypwdemail'))) {
            redirect('/');
        }

        $this->display();
    }

    public function password_up($password = "", $repassword = "")
    {
        if (empty(session('findpaypwdmoble')) && empty(session('findpaypwdemail'))) {
            $this->error('请返回第一步重新操作！');
        }

        if (!check($password, 'password')) {
            $this->error('新交易密码格式错误！');
        }

        if (!check($repassword, 'password')) {
            $this->error('确认密码格式错误！');
        }


        if ($password != $repassword) {
            $this->error('确认新密码错误！');
        }

        if(!empty(session('findpaypwdmoble'))){
            $moble = $this->_replace_china_mobile(session('findpaypwdmoble'));
            $user = M('User')->where(array('moble' => $moble))->find();
        }else if(!empty(session('findpaypwdemail'))){
            $user = M('User')->where(array('email' => session('findpaypwdemail')))->find();
        }

        if (!$user) {
            $this->error('用户不存在!');
        }

        if ($user['password'] == $password) {
            $this->error('交易密码不能和登录密码一样');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        //$mo->execute('lock tables qq3479015851_user write , qq3479015851_user_log write ');
        $rs = $mo->table('qq3479015851_user')->where(array('id' => $user['id']))->save(array('paypassword' => $password));

        if (!($rs === false)) {
            $mo->execute('commit');
            //$mo->execute('unlock tables');
            session('findpaypwdmoble',null);
            session('findpaypwdemail',null);
            $this->success('操作成功');
        } else {
            $mo->execute('rollback');
            $this->error('操作失败');
        }

    }

    public function findpwdinfo()
    {
        if (empty(session('findpaypwdmoble')) && empty(session('findpaypwdemail'))) {
            redirect('/');
        }
        session('findpaypwdmoble', "");
        session('findpaypwdemail', "");
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
                $this->error('确认交易密码错误！');
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
                $this->success('操作成功');
            } else {
                $mo->execute('rollback');
                $this->error('操作失败' . $mo->table('qq3479015851_user')->getLastSql());
            }
        } else {
            $this->display();
        }
    }

}

?>
