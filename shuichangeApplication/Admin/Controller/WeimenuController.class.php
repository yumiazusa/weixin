<?php

namespace Admin\Controller;
use Com\Wechat;
use Com\WechatAuth;

class WeimenuController extends AdminController {

public function weixinMenu(){
		$data=$this->get_data();
		$this->assign('data',$data);
		$this->display('weixinMenu');
	}


	public function weixinMenuAdd(){
		$pid=M ( 'custom_menu' )->field('title,id')->where('pid= 0')->order ( 'sort asc' )->select();
		$this->assign('pid',$pid);
		$this->display('weixinMenuAdd');
	}

	public function doWeixinMenuAdd(){
		$data=I('post.');
		$Model=M('custom_menu');
        		if ($Model->create($data)){
           	 if($data['title'] ==''){
           	 	$this->error('菜单名不能为空');
           	 	exit;
           	 }elseif($data['pid'] !=0 AND $Model->where('pid = '.$data['pid'])->count() >=5){
           	 	$this->error('二级菜单不能超过5个！');
           	 	exit;
           	 }elseif($data['pid'] ==0 AND $Model->where('pid = 0')->count() >=3){
           	 	$this->error(' 一级菜单不能超过3个！');
           	 	exit;
           	 }
            	$res = $Model->add($data);
           	 if ($res){
                    		$this->success('菜单添加成功!',U('weixinMenu'));
                   		 exit;
               		 }
            	}
      		  $this->error('菜单添加失败');
	}

	public function weixinMenuEdit(){
		$Model=M('custom_menu');
		$pid=$Model->field('title,id')->where('pid= 0')->order ( 'sort asc' )->select();
		$data=$Model->where('id ='.I('get.id'))->find();
		$this->assign(array(
			'pid'=>$pid,
			'data'=>$data,
			));
		$this->display('weixinMenuEdit');
	}

	public function doWeixinMenuEdit(){
		$data=I('post.');
		$Model=M('custom_menu');
        		if ($Model->create($data)){
           	 if($data['title'] ==''){
           	 	$this->error('菜单名不能为空');
           	 	exit;
           	 }elseif($data['pid'] !=0 AND $Model->where('pid = '.$data['pid'].' and id !='.$data['id'])->count() >=5){
           	 	$this->error('二级菜单不能超过5个！');
           	 	exit;
           	 }elseif($data['pid'] ==0 AND $Model->where('pid = 0 and id !='.$data['id'])->count() >=3){
           	 	$this->error(' 一级菜单不能超过3个！');
           	 	exit;
           	 }
            	$res = $Model->save($data);
           	 if ($res){
                    		$this->success('菜单修改成功!',U('weixinMenu'));
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
        		$Model=M('custom_menu');
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

	public function doCreate(){
		$data = $this->get_data();
		foreach ( $data as $k => $d ) {
			if ($d ['pid'] != 0)
				continue;
			$tree ['button'] [$d ['id']] = $this->_deal_data ( $d );
			unset ( $data [$k] );
		}
		foreach ( $data as $k => $d ) {
			$tree ['button'] [$d ['pid']] ['sub_button'] [] = $this->_deal_data ( $d );
			unset ( $data [$k] );
		}
		$tree2 = array ();
		$tree2 ['button'] = array ();

		foreach ( $tree ['button'] as $k => $d ) {
			$tree2 ['button'] [] = $d;
		}

		$tree = $this->json_encode_cn ( $tree2 );

		$Token=A('Index/Api')->getAccessToken();

		$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$Token["access_token"];
		$res=$this->httpsPost($url,$tree);
		$res=json_decode($res,true);
		if ($res ['errcode'] == 0) {
			$this->success ( '发送菜单成功' );
		} else {
			$this->error ( '发送菜单失败，错误的返回码是：' . $res ['errcode'] . ', 错误的提示是：' . $res ['errmsg'],'',7);
		}
	}


	private function get_data($map=null){
		$list = M ( 'custom_menu' )->where ( $map )->order ( 'pid asc, sort asc' )->select ();
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

				$l ['title'] = '├──' . $l ['title'];
				$two_arr [] = $l;
				unset ( $list [$key] );
			}

			$data = array_merge ( $data, $two_arr );
		}
			return $data;
	}

	private function _deal_data($d) {
		$res ['name'] = str_replace ( '├──', '', $d ['title'] );
		if($d['type']=='view'){
			$res ['type'] = 'view';
			$res ['url'] = trim ( $d ['url'] );
		}elseif($d['type']!='non'){
			$res ['type'] = trim( $d['type'] );
			$res ['key'] = trim ( $d ['keyword'] );
		}elseif($d['type']=='non'){
			unset($d['type']);
			unset($d ['keyword']);
		}

		return $res;
	}

	private function json_encode_cn($data) {
		$data = json_encode ( $data );
		return preg_replace ( "/\\\u([0-9a-f]{4})/ie", "iconv('UCS-2BE', 'UTF-8', pack('H*', '$1'));", $data );
	}

	private function httpsPost($url, $data = null) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {
			return 'Errno' . curl_error($curl);
		}
		curl_close($curl);
		return $result;
	}

	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}

	public function clearMemory(){
		S('Token',null);
		if(S('Token')){
			$this->error ('清空失败！','',3);
		}else{
			$this->success ( '清空成功！' );
		}
	}

}