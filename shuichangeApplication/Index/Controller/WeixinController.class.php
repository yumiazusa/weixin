<?php
namespace Index\Controller;
use Think\Controller;
use Com\Wechat;
use Com\WechatAuth;

class WeixinController extends HomeController {

	public function index() {
			$token = 'yumiazusa'; //微信后台填写的TOKEN
			$appid=C('WEI_APPID');
		    $secret=C('WEI_SECRET');
			/* 加载微信SDK */
			$wechat = new Wechat($token);
			$access_token['access_token']=$Token=A('Api')->getAccessToken();
			$WechatAuth = new WechatAuth($appid, $secret, $access_token['access_token']);
			/* 获取请求信息 */
			$data = $wechat->request();

			$access_token['openid'] = $data['FromUserName'];

			if ($data['Event'] == 'subscribe') {
				$response=C('WEI_REPLAYWORD_SUBSCRIBE');
				
			} else if ($data['Event'] == 'unsubscribe') {
				
			} else if ($data['Event'] == 'CLICK' && $data['EventKey'] == 'MENU_KEY_CUSTOMERMESSAGE') {
			} else if ($data['Event'] == 'CLICK' && $data['EventKey'] == 'MENU_KEY_TAKEMEDICAL') {
				
			} else if ($data['Event'] == 'CLICK' && $data['EventKey'] == 'MENU_KEY_POSTERCREATE') {
				
			} else if ($data['Event'] == 'SCAN') {
			
			}else if ($data && is_array($data) && $data['Event'] != 'LOCATION') {
				if(C('WEI_REPLAY_SWITCH')){
					$response=C('WEI_REPLAYWORD');
				}else{
					exit;
				}
			}

			$test_str = C('WEI_REPLAYWORD_SUBSCRIBE');
			$index_response = $test_str;
			$text = isset($response) ? $response : $index_response;
			$wechat->response($text, 'text');

	}


}