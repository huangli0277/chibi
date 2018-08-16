<?php
namespace Home\Controller;

class VerifyController extends HomeController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function code()
	{
        ob_clean();
		$config['useNoise'] = false;
		$config['length'] = 4;
		$config['codeSet'] = '0123456789';
		$verify = new \Think\Verify($config);
		$verify->entry(1);
	}

	public function real($moble, $verify)
	{
		if (!userid()) {
			redirect('/Login');
		}

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
		$content = 'CC找密码，your code：' . $code;

		if (SendText($moble, $code, 'fog')) {
			$this->success('短信验证码已发送到你的手机，请查收');
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}
	
	
	public function real_qq3479015851($moble,$moble_new)
	{
		if (!userid()) {
			$this->error('请先登录！');
		}

		if (!check($moble, 'moble')) {
			$this->error('手机号码格式错误！');
		}
		
		if (!check($moble_new, 'moble')) {
			$this->error('新手机号码格式错误！');
		}

        $moble = $this->_replace_china_mobile($moble);
        $moble_new = $this->_replace_china_mobile($moble_new);
		if (M('User')->where(array('moble' => $moble_new))->find()) {
			$this->error('更换绑定的手机号码已经注册过账户！');
		}

		$code = rand(111111, 999999);
		session('real_verify', $code);
        session('verify#time', time());
		$content = 'CC换手机，your code：' . $code;

		if (SendText($moble, $code, 'chg')) {
			
			if(MOBILE_CODE ==0 ){
				$this->success('目前是演示模式,请输入'.$code);
			}else{
				$this->success('短信验证码已发送到你的手机，请查收');
			}
			
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}
	
	
	
	
	
	
	public function moble(){
		if (!userid()) {
			redirect('/Login');
		}

		if (session('real_moble')) {
			$this->success('短信验证码已发送到你的手机，请注意查收');
		}

		$moble = M('User')->where(array('id' => userid()))->getField('moble');
		if (!$moble) {
			$this->error('手机号码未绑定！',U('User/moble'));
		}

		$code = rand(111111, 999999);
		session('real_moble',$code);
		$content = '您正在进行手机操作，您的验证码是' . $code;

		if (SendText($moble, $code, 'chg')) {
			$this->success('短信验证码已发送到你的手机，请查收');
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}

	public function mytx()
	{
		if (!userid()) {
			$this->error('请先登录');
		}

		$moble = M('User')->where(array('id' => userid()))->getField('moble');

		if (!$moble) {
			$this->error('你的手机没有认证');
		}

		$code = rand(111111, 999999);
		session('mytx_verify', $code);
        session('verify#time', time());
		$content = 'CC提币，your code：' . $code;

		if (SendText($moble, $code, 'cur')) {
			
			if(MOBILE_CODE ==0 ){
				$this->success('目前是演示模式,请输入'.$code);
			}else{
				$this->success('短信验证码已发送到你的手机，请查收');
			}
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}

	public function sendMobileCode()
	{
		if (IS_POST) {
			$input = I('post.');

			if (!check_verify(strtoupper($input['verify']))) {
				$this->error('图形验证码错误!', 'mobile_verify');
			}

			if (!check($input['mobile'], 'moble')) {
				$this->error('手机号码格式错误！', 'mobile');
			}

			if (M('User')->where(array('moble' => $input['mobile']))->find()) {
				$this->error('手机号码已存在！');
			}

			if ((session('mobile#mobile') == $input['mobile']) && (time() < (session('mobile#real_verify#time') + 600))) {
				$code = session('mobile#real_verify');
				session('mobile#real_verify', $code);
			}
			else {
				$code = rand(111111, 999999);
				session('mobile#real_verify#time', time());
				session('mobile#mobile', $input['mobile']);
				session('mobile#real_verify', $code);
			}

			$content = '您正在进行手机操作，您的验证码是' . $code;

			if (1) {
				$this->success('短信验证码已发送到你的手机，请查收' . $code);
			}
			else {
				$this->error('短信验证码发送失败，请重新点击发送');
			}
		}
		else {
			$this->error('非法访问！');
		}
	}

    public function sendEmailCode()
	{
		if (IS_POST && userid()) {
			$input = I('post.');

			if (!check($input['email'], 'email')) {
				$this->error('邮箱格式错误！');
			}

			if (M('User')->where(array('email' => $input['email']))->find()) {
				$this->error('邮箱已被使用，请选择其他邮箱！');
			}

			$code = rand(111111, 999999);
			session('email#real_verify#time', time());
			session('email#email', $input['email']);
			session('email#real_verify', $code);

			$content = '您正在进行邮箱注册操作，您的验证码是' . $code;

			$data = array("to"=>$input['email'], "subject"=>"绑定邮箱验证码", "content"=>$content);
			if (SendEmail($data)) {
				$this->success('邮箱验证码已发送到你的邮箱，请前往查收');
			}
			else {
				$this->error('邮箱验证码发送失败，请重新点击发送');
			}
		}
		else {
			$this->error('非法访问！');
		}
	}

	/*
	 * 这个被弃用了
	 * */
	public function findpwd()
	{
		$this->error("fatal error");

		if (IS_POST) {
			$input = I('post.');

			if (!check_verify(strtoupper($input['verify']))) {
				$this->error('图形验证码错误!');
			}

			if (!check($input['username'], 'username')) {
				$this->error('用户名格式错误！');
			}
            $moble = $this->_replace_china_mobile($input['moble']);
			if (!check($moble, 'moble')) {
				$this->error('手机号码格式错误！');
			}

			$user = M('User')->where(array('username' => $input['username']))->find();

			if (!$user) {
				$this->error('用户名不存在！');
			}

			if ($user['moble'] != $moble) {
				$this->error('用户名或手机号码错误！');
			}

			$code = rand(111111, 999999);
			session('findpwd_verify', $code);
            session('verify#time', time());
			$content = 'CC找密码，your code：' . $code;

			if (SendText($moble, $code, 'fog')) {
				$this->success('短信验证码已发送到你的手机，请查收');
			}
			else {
				$this->error('短信验证码发送失败，请重新点击发送');
			}
		}
	}
	
	
	
	
	public function moble_findpwd()
	{
		if (IS_POST) {
			$input = I('post.');

			if (!check_verify(strtoupper($input['verify']))) {
				$this->error('图形验证码错误!');
			}

            $moble = $this->_replace_china_mobile($input['moble']);
			if (!check($moble, 'moble')) {
				$this->error('手机号码格式错误！');
			}

			$user = M('User')->where(array('moble' => $moble))->find();

			if (!$user) {
				$this->error('手机号码不存在！');
			}

			$code = rand(111111, 999999);
			session('findpwd_verify', $code);
            session('verify#time', time());
            //手机号码和Code做MD5写入Session
            //防止发送短信后篡改用户手机，重置他人密码
            session('modify_password_validation', md5($moble));
            $content = 'CC找密码，your code：' . $code;

			if (SendText($moble, $code, 'fog')) {

				if(MOBILE_CODE ==0 ){
					$this->success('目前是演示模式,请输入'.$code);
				}else{
					$this->success('短信验证码已发送到你的手机，请查收');
				}
				
				
				
				
			}
			else {
				$this->error('短信验证码发送失败，请重新点击发送2');
				//$this->error('短信验证码发送失败，请重新点击发送');
			}
		}
	}


    public function email_findpwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check_verify(strtoupper($input['verify']))) {
                $this->error('图形验证码错误!');
            }

            if (!check($input['email'], 'email')) {
                $this->error('邮箱格式错误！');
            }

            $user = M('User')->where(array('email' => $input['email']))->find();

            if (!$user) {
                $this->error('邮箱账号不存在！');
            }

            $code = rand(111111, 999999);
            session('findpwd_verify', $code);
            session('verify#time', time());
            //邮箱码和Code做MD5写入Session
            session('modify_password_validation', md5($input['email']));
            $content = 'CC找密码，your code：' . $code;
            $data = array("to"=>$input['email'], "subject"=>"找回密码验证码", "content"=>$content);
            if (SendEmail($data)) {
                if(MOBILE_CODE ==0 ){
                    $this->success('目前是演示模式,请输入'.$code);
                }else{
                    $this->success('邮箱验证码已发送到你的邮箱，请前往查收');
                }
            }
            else {
                $this->error('邮箱验证码发送失败，请重新点击发送');
            }
        }
    }
	
	
	
	

	public function findpaypwd()
	{
		$input = I('post.');

		if (!check_verify(strtoupper($input['verify']))) {
			$this->error('图形验证码错误!');
		}

		if (!check($input['username'], 'username')) {
			$this->error('用户名格式错误！');
		}

        $moble = $this->_replace_china_mobile($input['moble']);
		if (!check($moble, 'moble')) {
			$this->error('手机号码格式错误！');
		}

		$user = M('User')->where(array('username' => $input['username']))->find();

		if (!$user) {
			$this->error('用户名不存在！');
		}

		if ($user['moble'] != $moble) {
			$this->error('用户名或手机号码错误！');
		}

		$code = rand(111111, 999999);
		session('findpaypwd_verify', $code);
        session('verify#time', time());
		$content = 'CC找密码，your code：' . $code;

		if (SendText($moble, $code, 'fog')) {
			$this->success('短信验证码已发送到你的手机，请查收');
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}

	public function myzc()
	{
		if (!userid()) {
			$this->error('您没有登录请先登录!');
		}

		$moble = M('User')->where(array('id' => userid()))->getField('moble');

		if (!$moble) {
			$this->error('你的手机没有认证');
		}

		$code = rand(111111, 999999);
		session('myzc_verify', $code);
        session('verify#time', time());
		$content = 'CC提币，your code：' . $code;

		if (SendText($moble, $code, 'cur')) {
			if(MOBILE_CODE ==0 ){
				$this->success('目前是演示模式,请输入'.$code);
			}else{
				$this->success('短信验证码已发送到你的手机，请查收');
			}
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}
	
	
	public function myzr()
	{
		if (!userid()) {
			$this->error('您没有登录请先登录!');
		}

		$moble = M('User')->where(array('id' => userid()))->getField('moble');

		if (!$moble) {
			$this->error('你的手机没有认证');
		}

		$code = rand(111111, 999999);
		session('myzr_verify', $code);
		$content = 'CC提币，your code：' . $code;

		if (SendText($moble, $code, 'cur')) {
			if(MOBILE_CODE ==0 ){
				$this->success('目前是演示模式,请输入'.$code);
			}else{
				$this->success('短信验证码已发送到你的手机，请查收');
			}
		}
		else {
			$this->error('短信验证码发送失败，请重新点击发送');
		}
	}
	
	
}

?>
