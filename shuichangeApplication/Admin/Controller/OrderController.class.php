<?php

namespace Admin\Controller;
use Com\Wechat;
use Com\WechatAuth;

class OrderController extends AdminController {

	public function menuList(){
		$data=$this->get_data();
		$this->assign('data',$data);
		$this->display('menuList');
	}

	public function menuAdd(){
		$pid=M ( 'menu_category' )->field('name,id')->where('pid= 0')->order ( 'sort asc' )->select();
		$this->assign('pid',$pid);
		$this->display('menuAdd');
	}

		public function doMenuAdd(){
		$data=I('post.');
		$Model=M('menu_category');
        		if ($Model->create($data)){
           	 if($data['name'] ==''){
           	 	$this->error('菜单名不能为空');
           	 	exit;
           	 }
            	$res = $Model->add($data);
           	 if ($res){
                    		$this->success('菜单添加成功!',U('menuList'));
                   		 exit;
               		 }
            	}
      		  $this->error('菜单添加失败');
	}

	public function menuEdit(){
		$Model=M('menu_category');
		$pid=$Model->field('name,id')->where('pid= 0')->order ( 'sort asc' )->select();
		$data=$Model->where('id ='.I('get.id'))->find();
		$this->assign(array(
			'pid'=>$pid,
			'data'=>$data,
			));
		$this->display('menuEdit');
	}

	public function doMenuEdit(){
		$data=I('post.');
		$Model=M('menu_category');
        		if ($Model->create($data)){
           	 if($data['name'] ==''){
           	 	$this->error('菜单名不能为空');
           	 	exit;
           	 }
            	$res = $Model->save($data);
           	 if ($res){
                    		$this->success('菜单修改成功!',U('menuList'));
                   		 exit;
               		 }
            	}
      		  $this->error('菜单修改失败');
	}

	public function menuDel(){
		$id =I('get.id');
       		if (empty($id)) {
            		$this->error('请选择要删除的菜单!');
       		}
        		$Model=M('menu_category');
        		$pid=$Model->field('pid')->where('id ='.$id )->find();
        		if($pid['pid']==0){
        			$idarr=$Model->field('id')->where('pid ='.$id)->select();
        			foreach($idarr as $k=>$v){
        				$ids[$k]=$v['id'];
        			}
        			if($ids){
        			array_push($ids,$id);
        			$map=array('id' => array('in', $ids) );
        		}else{
        			$map=array('id'=>$id);
        			}
        		}else{
        			$map=array('id'=>$id);
        			}
       		if($Model->where($map)->delete()){
            		$this->success('删除成功');
       	 	} else {
            		$this->error('删除失败！');
        		}
	}


	private function get_data($map=null){
		$map=array('status'=>1);
		$list = M ('menu_category')->where ( $map )->order ( 'pid asc, sort asc' )->select ();
		// 取一级菜单
		foreach ( $list as $k => $vo ) {
			if ($vo ['pid'] != 0)
				continue;
			$one_arr [$vo ['id']] = $vo;
			unset ( $list [$k] );
		}
		foreach ( $one_arr as $p ) {
			$data [] = $p;
			$two_arr = array ();
			foreach ( $list as $key => $l ) {
				if ($l ['pid'] != $p ['id'])
					continue;

				$l ['name'] = '├──' . $l ['name'];
				$two_arr [] = $l;
				unset ( $list [$key] );
			}

			$data = array_merge ( $data, $two_arr );
		}
			return $data;
	}


	public function orders(){

			$db=M('order');
			$orderCounts = $db->count();
        	$pageSize = 20;
        	$page = new \Think\Page($orderCounts, $pageSize, $where);
        	$data = $db->order('ordertime DESC')->limit($page->firstRow, $page->listRows)->select();

            $menu=M('menu_category');
			foreach($data as $k=>$v){
			$v['orderinfo']=explode(',',$v['orderinfo']);
			$map['id']=array('in',$v['orderinfo']);
			$data[$k]['orderinfo']=$menu->field('name')->where($map)->select();
			foreach($data[$k]['orderinfo'] as $k1=>$v1){
				$data[$k]['orderinfo'][$k1]=$v1['name'];
				}
				$data[$k]['orderinfo']=implode(',',$data[$k]['orderinfo']);
				}

            $pageShow = $page->show();
        	$this->assign('page', $pageShow);
			$this->assign('data',$data);
			$this->display('order');
	}

	public function orderAdd(){
		$menuList=M('menu_category')->where(array('pid > 0','status'=>1))->order('pid')->select();
		$this->assign('list',$menuList);
		$this->display('orderAdd');
	}

	public function doOrderAdd(){
		$data=I('post.');
		$Model=M('order');
		$time=$data['ordertime'];
		if($time){
			$time=strtotime($time);
			}else{
			$time=date('Y-m-d').' 17:00';
			$time=strtotime($time);
			}
		$data['ordertime']=$time;
		$data['createtime']=time();

		$menu=M('menu_category');
		$count=$Model->count();
		$count=$count+1;
		$data['ordersn']='nowork'.$data['createtime'];
		$data['ordersn']=$data['ordersn'].$count;
		$price=0;
		$orderlist='';
		foreach($data['orderinfo'] as $k=>$v){
			$price+=$menu->where(array('id'=>$v))->getField('price');

		}
		$data['allprice']=$price;
		$data['orderinfo']=implode(',',$data['orderinfo']);
		if($data['discount']){
			if($data['discount']==100){
				$data['price']=0;
			}else{$data['price']=floor($price*($data['discount']/100)+$data['deliveryfee']);}
		}else{
			$data['price']=floor($price+$data['deliveryfee']);
		}
		$res = $Model->add($data);
		if ($res){
            $this->success('增加成功!',U('orders'));
            exit;
               	}else{
               		$this->error('增加失败！');
               	}
	}

	public function orderEdit(){
		$id=I('get.id');
		$db=M('order');
		$data=$db->where(array('id'=>$id))->find();
		$data['orderinfo']=explode(',',$data['orderinfo']);
		$data['ordertime']=date('Y-m-d H:i',$data['ordertime']);
		$menuList=M('menu_category')->where(array('pid > 0','status'=>1))->order('pid')->select();
		$this->assign('list',$menuList);
		$this->assign('data',$data);
		$this->display();
	}

	public function doOrderEdit(){
		$data=I('post.');
		$time=$data['ordertime'];
		if($time){
			$time=strtotime($time);
			}else{
			$time=date('Y-m-d').' 17:00';
			$time=strtotime($time);
			}
		$data['ordertime']=$time;
		$menu=M('menu_category');
		$price=0;
		$orderlist='';
		foreach($data['orderinfo'] as $k=>$v){
			$price+=$menu->where(array('id'=>$v))->getField('price');

		}
		$data['allprice']=$price;
		$data['orderinfo']=implode(',',$data['orderinfo']);
		if($data['discount']){
			if($data['discount']==100){
				$data['price']=0;
			}else{$data['price']=floor($price*($data['discount']/100)+$data['deliveryfee']);}
		}else{
			$data['price']=floor($price+$data['deliveryfee']);
		}
		$Model=M('order');
		$res = $Model->save($data);
		 if ($res){
                   $this->success('订单修改成功!',U('orders'));
                   	exit;
               		 }else{
      		  $this->error('订单修改失败');
      		  		 }
	}

	public function orderDel(){
		$id =I('get.id');
       		if (empty($id)) {
            		$this->error('请选择要删除的订单!');
       		}
       		$map=array('id'=>$id);
        	$Model=M('order');
       		if($Model->where($map)->delete()){
            		$this->success('删除成功');
       	 	} else {
            		$this->error('删除失败！');
        		}
	}

	public function checkout(){
		$id=I('get.id');
		$db=M('order');
		$data=$db->where(array('id'=>$id))->find();
		$data['orderinfo']=explode(',',$data['orderinfo']);
		$data['ordertime']=date('Y-m-d H:i',$data['ordertime']);
		$menu=M('menu_category');
			foreach($data['orderinfo'] as $k=>$v){
				$data['orderinfo'][$k]=$menu->where(array('id'=>$v))->getField('name');
				}
		$this->assign('data',$data);
		$this->display();
	}

}