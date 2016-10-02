wx.config({
    debug: false,
    appId: appIdstr,
    timestamp: timestampstr,
    nonceStr: nonceStrstr,
    signature: signaturestr,
    jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard'
    ]
  });

function doWeixin() {  
wx.ready(function () {
 
 	var sharebackurl =document.getElementById('sharebackurl').value;
  // 2. 鍒嗕韩鎺ュ彛
  // 2.1 鐩戝惉"鍒嗕韩缁欐湅鍙�"锛屾寜閽偣鍑汇€佽嚜瀹氫箟鍒嗕韩鍐呭鍙婂垎浜粨鏋滄帴鍙�
 // document.querySelector('#onMenuShareAppMessage').onclick = function () {
    wx.onMenuShareAppMessage({
      title: document.getElementById('wx-share-title').value,
      desc: document.getElementById('wx-share-desc').value,
      link: document.getElementById("wx-share-link").value,
      imgUrl: document.getElementById('wx-share-img').value,
      trigger: function (res) {
       // alert('鐢ㄦ埛鐐瑰嚮鍙戦€佺粰鏈嬪弸');
      },
      success: function (res) {
       // alert('宸插垎浜�');
	   	 var image=new Image();   
		image.src=sharebackurl;        
      },
      cancel: function (res) {
        //alert('宸插彇娑�');
      },
      fail: function (res) {
        //alert(JSON.stringify(res));
      }
    });
 
 // };

  // 2.2 鐩戝惉"鍒嗕韩鍒版湅鍙嬪湀"鎸夐挳鐐瑰嚮銆佽嚜瀹氫箟鍒嗕韩鍐呭鍙婂垎浜粨鏋滄帴鍙�
 // document.querySelector('#onMenuShareTimeline').onclick = function () {
    wx.onMenuShareTimeline({
      title: document.getElementById('wx-share-title').value,
      link: document.getElementById("wx-share-link").value,
      imgUrl: document.getElementById('wx-share-img').value,
      trigger: function (res) {
       // alert('鐢ㄦ埛鐐瑰嚮鍒嗕韩鍒版湅鍙嬪湀');
      },
      success: function (res) {
       	 var image=new Image();   
		image.src=sharebackurl;        
      },
      cancel: function (res) {
      //  alert('宸插彇娑�');
      },
      fail: function (res) {
        //alert(JSON.stringify(res));
      }
    });
   // alert('宸叉敞鍐岃幏鍙�"鍒嗕韩鍒版湅鍙嬪湀"鐘舵€佷簨浠�');
 // };

  // 2.3 鐩戝惉"鍒嗕韩鍒癚Q"鎸夐挳鐐瑰嚮銆佽嚜瀹氫箟鍒嗕韩鍐呭鍙婂垎浜粨鏋滄帴鍙�
  //document.querySelector('#onMenuShareQQ').onclick = function () {
    wx.onMenuShareQQ({
      title: document.getElementById('wx-share-title').value,
      desc: document.getElementById('wx-share-desc').value,
      link: document.getElementById("wx-share-link").value,
      imgUrl: document.getElementById('wx-share-img').value,
      trigger: function (res) {
       // alert('鐢ㄦ埛鐐瑰嚮鍒嗕韩鍒癚Q');
      },
      complete: function (res) {
       //alert(JSON.stringify(res));
      },
      success: function (res) {
       	 var image=new Image();   
		image.src=sharebackurl;        
      },
      cancel: function (res) {
       // alert('宸插彇娑�');
      },
      fail: function (res) {
        //alert(JSON.stringify(res));
      }
    });
  //  alert('宸叉敞鍐岃幏鍙�"鍒嗕韩鍒� QQ"鐘舵€佷簨浠�');
 // };
  
  // 2.4 鐩戝惉"鍒嗕韩鍒板井鍗�"鎸夐挳鐐瑰嚮銆佽嚜瀹氫箟鍒嗕韩鍐呭鍙婂垎浜粨鏋滄帴鍙�
 // document.querySelector('#onMenuShareWeibo').onclick = function () {
    wx.onMenuShareWeibo({
      title: document.getElementById('wx-share-title').value,
      desc: document.getElementById('wx-share-desc').value,
      link: document.getElementById("wx-share-link").value,
      imgUrl: document.getElementById('wx-share-img').value,
      trigger: function (res) {
        //alert('鐢ㄦ埛鐐瑰嚮鍒嗕韩鍒板井鍗�');
      },
      complete: function (res) {
       // alert(JSON.stringify(res));
      },
      success: function (res) {
       	 var image=new Image();   
		image.src=sharebackurl;        
      },
      cancel: function (res) {
        //alert('宸插彇娑�');
      },
      fail: function (res) {
       // alert(JSON.stringify(res));
      }
    });
 
});
}

doWeixin();