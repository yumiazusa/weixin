<?php
namespace Index\Controller;
use Think\Controller;

class MapController extends HomeController {
	public function index(){
		$this->display('index');
	}

	public function navigation(){
		$lon=str_replace("。",".",I('post.lon'));
		$lat=str_replace("。",".",I('post.lat'));

		$AK='011e564e2ba165af55ce939440dfb8eb';
		$this->assign(array(
 				'AK'=>$AK,
 				'lon'=>$lon,
 				'lat'=>$lat
 				));
		$this->display('navigation');
	}







}