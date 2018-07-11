<?php
// 引入jssdk.php
require_once "jssdk.php";
$jssdk = new JSSDK("yourAppID", "yourAppSecret");
// 获取signPackage数组
$signPackage = $jssdk->GetSignPackage();
// 得到 JSSDK文件中的$signPackage
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>请求示例</title>
    <!-- 使用微信接口js -->
    <script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        //  $signPackage 这个包里的数据需要加入配置项
        wx.config({
            // 默认debug为false，开发调试建议开启
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: '<?php echo $signPackage["timestamp"];?>',
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            // 将要使用的接口 写入下面数组
            jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage']
        });
        wx.ready(function(){
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
        });
        wx.error(function(res){
            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
        });
    </script>
    <script>
        var share = {
            desc: '雀巢主题日',
            title: '主题日',
            link: location.href,
            imgUrl:'图片的外链'
        }
        wx.ready(function () {
            // 分享给好友
            wx.onMenuShareAppMessage({
                title: share.title,
                desc: share.desc,
                link: share.link,
                imgUrl:share.imgUrl
            });
            // 分享朋友圈
            wx.onMenuShareTimeline({
                title: share.title,
                desc: share.desc,
                link: share.link,
                imgUrl: share.imgUrl
            });
        });
    </script>
</head>
<body>
    
</body>
</html>