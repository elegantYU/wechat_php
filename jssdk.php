<?php
class JSSDK
{
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId = 'yourAppId';
        $this->appSecret = 'yourAppSecret';
    }

    public function getSignPackage()
    {
        // 获取 getJsApiTicket的ticket
        $jsapiTicket = $this->getJsApiTicket();
        // url 是获取当前服务器的域名和请求参数
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // 获取时间戳
        $timestamp = time();
        // 获取随机字符串
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序 生成signature
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    
        // 使用sha1 加密
        $signature = sha1($string);
        // 生成一个数组 等调用
        $signPackage = array(
        "appId"     => $this->appId,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string
        );
        return $signPackage;
    }

    //  生成长度16位的随机字符串
    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码为将jsapi_ticket存在jsapi_ticket.txt文件中
        // 文件位置 名称
        $file = "jsapi_ticket.txt";
        // 判断问文件是否存在
        if (!file_exists($file)) {
            // 获取accessToken
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            // 使用httpGet请求该api
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                // 若获取到ticket 将数据写入文件
                $wfile = fopen($file,"w") or die("unable to open file");
                fwrite($wfile,$ticket);
                fclose($wfile);
            }
        } else {
            // 打开文件读取数据
            $rfile = fopen($file,"r") or die("unable to open file");
            $ticket = fread($rfile,filesize($file));
        }
        return $ticket;
    }

    private function getAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        // 同上获取ticket步骤
        $file = "access_token.txt";
        if (!file_exists($file)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $wfile = fopen($file,"w") or die("unable to open file");
                fwrite($wfile,$access_token);
                fclose($wfile);
            }
        } else {
            $rfile = fopen($file,"r") or die("unable to open file");
            $access_token = fread($rfile,filesize($file));
        }
        return $access_token;
    }

    // 请求接口方法
    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 过HTTPS验证
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
