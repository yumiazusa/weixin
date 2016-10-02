<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Index\Controller;
use OT\DataDictionary;
use Com\Wechat;
use Com\WechatAuth;

class IndexController extends HomeController {

	//系统首页
    public function index(){
        $type=I('get.type');
    	$img=M('mainimg')->find();
    	$this->assign('img',$img);
    	$db=M('newscategory');
        if($type && $type == 'chan'){
            $map['id']=array('gt',6);
        }
		$data=$db->order('status ASC')->where($map)->select();
		$this->assign('data',$data);
        $this->display();
    }

    public function category(){
    	$db=M('newscategory');
		$list=$db->order('status ASC')->select();
		$this->assign('list',$list);

		$newdb=M('news');
		if(isset($_GET['category'])){
				$where=array('category'=>$_GET['category']);
			   }
		$getPageCounts = $newdb->where($where)->count();
        $pageSize = 15;
        $page = new \Think\Page($getPageCounts, $pageSize, $where);
        $data = $newdb->where($where)->order('create_time DESC')->limit($page->firstRow, $page->listRows)
                       ->select();
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');

		$pageShow = $page->show();
        $this->assign('category',$_GET['category']);
        $this->assign('page', $pageShow);
		$this->assign('data',$data);


    	$this->display();
    }

}