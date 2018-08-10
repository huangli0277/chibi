<?php
namespace Home\Controller;

class ArticleController extends HomeController
{
	public function index($id = 19)
	{
		if (empty($id)) {
			redirect(U('Article/detail'));
		}

		if (!check($id, 'd')) {
			redirect(U('Article/detail'));
		}
        $this->assign('type', $id == 20?'资讯':'公告');
		$Articletype = M('ArticleType')->where(array('id' => $id))->find();
		$ArticleTypeList = M('ArticleType')->where(array('status' => 1, 'index' => 1, 'shang' => $Articletype['shang']))->order('sort asc ,id asc')->select();
		
		$Articleaa = M('Article')->where(array('status'=>1,'id' => $ArticleTypeList[0]['id']))->find();
		$this->assign('shang', $Articletype);

		foreach ($ArticleTypeList as $k => $v) {
			$ArticleTypeLista[$v['name']] = $v;
		}

		$this->assign('ArticleTypeList', $ArticleTypeLista);
		$this->assign('data', $Articleaa);
		$where = array('type' => $Articletype['name'],'status'=>1);
		$Model = M('Article');
		$count = $Model->where($where)->count();
		$Page = new \Think\Page($count, 10);
		$show = $Page->show();
		$list = $Model->where($where)->order('addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$hotlist = $Model->where($where)->order('hits desc')->limit(6)->select();

        foreach ($list as $k => $v) {
            $list[$k]['brief'] = mb_substr(preg_replace("/<[^>]+>/is", "", $v['content']), 0, 150);
            if (empty($v['img']))
                $list[$k]['img'] = "app-code.png";
		}

        foreach ($hotlist as $k => $v) {
            $hotlist[$k]['brief'] = mb_substr(preg_replace("/<[^>]+>/is", "", $v['content']), 0, 150);
        }

        $this->assign('list', $list);
        $this->assign('hotlist', $hotlist);
		$this->assign('id', $id);
		$this->assign('page', $show);
		$this->display();
	}

	
	public function upgrade(){
		$this->display();
	}
	
	public function detail($id = NULL)
	{   
		if (empty($id)) {
			$id = 1;
		}

		if (!check($id, 'd')) {
			$id = 1;
		}

		$data = M('Article')->where(array('id' => $id))->find();
		$ArticleType = M('ArticleType')->where(array('status' => 1, 'index' => 1))->order('sort asc ,id desc')->select();

		foreach ($ArticleType as $k => $v) {
			$ArticleTypeList[$v['name']] = $v;
		}

        $where = array('status'=>1);
        $Model = M('Article');
        $hotlist = $Model->where($where)->order('hits desc')->limit(6)->select();
        foreach ($hotlist as $k => $v) {
            $hotlist[$k]['brief'] = mb_substr(preg_replace("/<[^>]+>/is", "", $v['content']), 0, 150);
        }
        $this->assign('hotlist', $hotlist);

		//保存点击数加一
        $rs = M('Article')->where(array('id' => $id))->setInc('hits');

		$this->assign('ArticleTypeList', $ArticleTypeList);
		$this->assign('data', $data);
		$this->assign('type', $data['type']);
		$this->display();
	}

    public function mdetail($id = NULL)
    {
        if (empty($id)) {
            $id = 1;
        }

        if (!check($id, 'd')) {
            $id = 1;
        }

        $data = M('Article')->where(array('id' => $id))->find();
        $ArticleType = M('ArticleType')->where(array('status' => 1, 'index' => 1))->order('sort asc ,id desc')->select();

        foreach ($ArticleType as $k => $v) {
            $ArticleTypeList[$v['name']] = $v;
        }

        $this->assign('ArticleTypeList', $ArticleTypeList);
        $this->assign('data', $data);
        $this->assign('type', $data['type']);
        $this->display();
    }

	public function type($id = NULL)
	{
		if (empty($id)) {
			$id = 1;
		}

		if (!check($id, 'd')) {
			$id = 1;
		}

		$Article = M('ArticleType')->where(array('id' => $id))->find();
		

		if ($Article['shang']) {
			$shang = M('ArticleType')->where(array('name' => $Article['shang']))->find();
			$ArticleType = M('ArticleType')->where(array('status' => 1, 'shang' => $Article['shang']))->order('sort asc ,id desc')->select();
			$Articleaa = $Article;
		}
		else {
			$shang = M('ArticleType')->where(array('id' => $id))->find();
			$ArticleType = M('ArticleType')->where(array('status' => 1, 'shang' => $Article['name']))->order('sort asc ,id desc')->select();
			$Articleaa = M('ArticleType')->where(array('id' => $ArticleType[0]['id']))->find();
		}

		$this->assign('shang', $shang);
		
		
		foreach ($ArticleType as $k => $v) {
			$ArticleTypeList[$v['name']] = $v;
		}


		
		$this->assign('ArticleTypeList', $ArticleTypeList);
		$this->assign('data', $Articleaa);
		$this->display();
	}

}

?>