<?php 
/** Powerd by RebetaStudio 
 * 
 *  http://www.rebeta.cn
 * 
 * 20160814首版
 */

//设置时区
date_default_timezone_set('PRC');
header('Content-Type: text/html; charset=UTF-8');
require '../../../zjcweixin/wx_Main.php';

class SendMsgClass extends wxMain
{
    public function sendMsg($touser,$content)
    {
        $accessToken = parent::getAccessToken();
        
        $data = '{
	            "touser":"'.$touser.'",
	            "msgtype":"text",
	            "text":
	            {
	                 "content":"'.$content.'"
	            }
	            }';
        
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$accessToken;
        
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }
    
    private function https_post($url,$data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
}
?>