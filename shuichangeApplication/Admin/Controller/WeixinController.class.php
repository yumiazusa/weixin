<?php

namespace Admin\Controller;
use Com\Wechat;
use Com\WechatAuth;

class WeixinController extends AdminController {


		public function mainImg(){
			$img=M('mainimg')->where(array('id'=>1))->find();
			$this->assign('img',$img);
			$this->display('mainImg');
		}

		public function editImg(){
			$img=M('mainimg')->where(array('id'=>1))->find();
			$this->assign('img',$img);
			$this->display('editImg');
		}


		public function saveimg(){
			$data=I('post.');
			$db=M('mainimg');
			$res=$db->where(array('id'=>1))->save($data);
	        if($res !== false){
	          $this->success('提交成功',U('mainImg'),3);
	        }else{
	          $this->success('提交失败');
	        }
		}

		public function category(){
			$db=M('newscategory');
			$data=$db->order('status ASC')->select();
			$this->assign('data',$data);
			$this->display();
		}

		public function addCategory(){
			$this->display('addCategory');
		}

		public function doAddcategory(){
			$data['title']=I('post.title');
			$data['image']=I('post.image');
			$data['status']=I('post.status');
			$data['subtitle']=I('subtitle');

			$catdb=M('newscategory');

			$res= $catdb->add($data);

			if($res !== false){
	          $this->success('提交成功',U('category'),3);
	        }else{
	          $this->success('提交失败');
	        }
		}

		public function editCategory(){
			$id=I('get.id');
			$data=M('newscategory')->where(array('id'=>$id))->find();
			$this->assign('data',$data);
			$this->display('editCategory');
		}

		public function doEditcategory(){
			$data=I('post.');
			$db=M('newscategory');
			$res=$db->where(array('id'=>$data['id']))->save($data);
	        if($res !== false){
	          $this->success('提交成功',U('category'),3);
	        }else{
	          $this->success('提交失败');
	        }
		}

		public function delCategory(){
			$id=I('get.id');
			$res=M('newscategory')->where(array('id'=>$id))->delete();
			if($res !== false){
	          $this->success('提交成功');
	        }else{
	          $this->success('提交失败');
	        }
		}

		public function news(){
			$list=M('newscategory')->order('status ASC')->select();
			$this->assign('list',$list);

			if(isset($_GET['category'])){
				$where=array('category'=>$_GET['category']);
			   }

			$db=M('news');

			$getPageCounts = $db->where($where)->count();
        	$pageSize = 10;
        	$page = new \Think\Page($getPageCounts, $pageSize, $where);
        	$data = $db->where($where)->order('create_time DESC')->limit($page->firstRow, $page->listRows)
                       ->select();

            foreach($data as $k=>$v){
            	foreach($list as $key => $vo){
            		if($v['category'] == $vo['id']){
            			$data[$k]['cat']=$vo['title'];
            		}
            	}
            }

            $pageShow = $page->show();
        	$this->assign('page', $pageShow);
			$this->assign('data',$data);
			$this->display();
		}

		public function editNews(){
			$list=M('newscategory')->order('status ASC')->select();
			$this->assign('list',$list);
			$id=$_GET['id'];
			$data=M('news')->where(array('id'=>$id))->find();
			$this->assign('data',$data);

			$this->display('editNews');
		}

		public function doEditnewscategory(){
			$data=I('post.');
			$res=M('news')->where(array('id'=>$data['id']))->save($data);
			if($res !== false){
	          $this->success('提交成功','javascript:history.back(-1);',3);
	        }else{
	          $this->success('提交失败');
	        }
		}


		public function delNews(){
			$id=I('get.id');
			$res=M('news')->where(array('id'=>$id))->delete();
			if($res !== false){
	          $this->success('提交成功');
	        }else{
	          $this->success('提交失败');
	        }
		}






		public function getNews(){
				$Token=A('Index/Api')->getAccessToken();
    			$count=$this->totalStuff();
    			$page=floor($count['news_count']/20);
    			$url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$Token["access_token"];
    			$data1=array();
    			for($i=0;$i<=$page;$i++){
    				$offset=$i*20;
    				$data='{
    			 	"type":"news",
    			 	"offset":'.$offset.',
    		     	"count":20
			      	}';
				$res=$this->httpsPost($url,$data);
		        $news=json_decode($res,true);
		        $vv=$news['item'];
		        foreach($vv as $k=>$v){
		        	array_push($data1,$v);
		           }
		        }
		        rsort($data1);
		        S('weixin2',$data1);
		        $lala=S('weixin2');
		        if($lala){
		        	echo 'ok';
		        }
   		}


   		public function updateNews(){
   			$Token=A('Index/Api')->getAccessToken();
   			$new=M('news')->group('media_id')->select();
   			$news=count($new);
   			$count=$this->totalStuff();
   			if($count['news_count']>$news){
   				$offset=intval($count['news_count']-$news);
   				$page=floor($offset/20);
   				$url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$Token["access_token"];
    			$data1=array();
    			for($i=0;$i<=$page;$i++){
    				$offsets=$i*20;
    				if($page == 0 && $i==$page){
    					$data='{
    			 		"type":"news",
    			 		"offset":'.$offsets.',
    		     		"count":'.$offset.'
			      		}';
    				}elseif($page > 0 && $i<$page){
    					$data='{
    			 		"type":"news",
    			 		"offset":'.$offsets.',
    		     		"count":20
			      		}';
    				}elseif($page>0 && $i==$page){
    					$data='{
    			 		"type":"news",
    			 		"offset":'.$offsets.',
    		     		"count":'.$offset.'
			      		}';
    				}
			    $res=$this->httpsPost($url,$data);
		        $news=json_decode($res,true);
		        $vv=$news['item'];
		        foreach($vv as $k=>$v){
		        	array_push($data1,$v);
		           }
		        }
		        rsort($data1);
		        if($data1){
		        	$data2=array();
		        	foreach($data1 as $k=>$v){
		        		$data2['media_id']=$v['media_id'];
		        		$data2['create_time']=$v['update_time'];
		        		foreach($v['content']['news_item'] as $key=>$vo){
		        		$data2['title']=$vo['title'];
		        		$data2['url']=$vo['url'];
		        		$data2['thumb_url']=$this->getNewsimg($vo['thumb_media_id']);
		        		$res=M('news')->add($data2);
   			  				}
		        	}
		        }
		        if($res){
		        	echo 1;
		        }else{
		        	echo 0;
		        }
   			}
   		}


   		public function saveNews(){
	   			$data1=S('weixin2');
	   			$db=M('news');
	   			$data2=array();
	   			foreach($data1 as $k => $v){
   					$data2['media_id']=$v['media_id'];
		        	$data2['create_time']=$v['update_time'];
		        	foreach($v['content']['news_item'] as $key=>$vo){
		        		$data2['title']=$vo['title'];
		        		$data2['url']=$vo['url'];
		        		$data2['thumb_url']=$this->getNewsimg($vo['thumb_media_id']);
		        		$res=$db->add($data2);
   			  }
   		  	}
   	    }


   		// public function lala(){
   		// 	$data=S('weixin');
   		// 	$data1=array();
   		// 	foreach($data as $k => $v){
   		// 		foreach($v as $key => $vo){
   		// 			array_push($data1, $vo);
   		// 		}
   		// 	}
   		// 	dump($data1);
   		// }

   		
   		public function checkUpdate(){
   			$new=M('news')->group('media_id')->select();
   			$news=count($new);
   			$count=$this->totalStuff();
   			if($count['news_count']>$news){
   				$offset=$count['news_count']-$news;
   				echo $offset;
   			}elseif($count['news_count']==$news){
   				echo 0;
   			}else{
   				echo -1;
   			}
   		}



   		public function getStuff($type){
    	$Token=A('Index/Api')->getAccessToken();
    	$url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$Token["access_token"];
        $data='{
    		"type":"'.$type.'",
    		"offset":0,
    		"count":10
			}';
		$res=$this->httpsPost($url,$data);
		$res=json_decode($res,true);
		dump($res);
		die;
		return $res;
   		}

   		public function totalStuff(){
    	$Token=A('Index/Api')->getAccessToken();
    	$url="https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=".$Token["access_token"];
		$res=$this->httpsPost($url,$data);
		$res=json_decode($res,true);
		return $res;
   		}

    	public function getNewsimg($media_id){
    		$Token=A('Index/Api')->getAccessToken();
    		$url="https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=".$Token["access_token"];
    		$data='{
					"media_id":"'.$media_id.'"
					}';
			$res=$this->httpsPost($url,$data);
			$file=$this->savenewImg($res,$media_id);
			return $file;
    	}


    	private function savenewImg($res,$media_id){
		$path='./Uploads/Weixin/Newsimg';
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		$filename=$media_id.'.jpg';
		$file=$path.'/'.$filename;
		$local_file=fopen($file,'w');
		if($local_file){
		 	fwrite($local_file,$res);
			fclose($local_file);
		   }
		  return $file;
		}

		

		public function uploadImage(){
	    	$appid=C('WEI_APPID');
			$secret=C('WEI_SECRET');
			$Token=A('Api')->getAccessToken();
			$WechatAuth = new WechatAuth($appid, $secret, $Token["access_token"]);
			$userimg = './1.jpg';
		    $type = 'image';
			$media = $WechatAuth->materialUpload($userimg,$type);
			$res=json_decode($media);
			dump($res);
			die;
    }


    	public function replayWords(){
    		
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

	public function positions(){
		$this->display();
	}

	public function addPosition(){
		$this->display('addPosition');
	}


	}