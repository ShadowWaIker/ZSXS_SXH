<?php 
/** Powerd by RebetaStudio 
 * 
 *  http://www.rebeta.cn
 * 
 * 20170403更新内容：
 * 
 */

error_reporting(0);
date_default_timezone_set('PRC');
define ("BoothLen",3);

$openid = $_POST["session"];
if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$openid) || ($openid == "")){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>检测到非法参数！</h1>");
}
$booth = $_POST["zwh"];
if(!is_numeric($booth) || strlen($booth) > BoothLen){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>检测到非法参数,展位号应当是长度小于或等于".BoothLen."位的数字。请返回重试！</h1>");
} else {
    $booth = str_pad($booth,3,"0",STR_PAD_LEFT);
}
$type = $_POST["jslx"];
if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$type) || ($type == "null")){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>请正确选择教室类型！</h1>");
}

define ("RebetaMySqlUSER","数据库用户名");
define ("RebetaMySqlPWD","数据库密码");
define ("RebetaMySqlDSN","mysql:host=数据库主机IP地址;port=数据库端口;dbname=数据库名");

try{
    //实例化mysqlpdo，执行这里时如果出错会被catch
    $pdo = new PDO(RebetaMySqlDSN,RebetaMySqlUSER,RebetaMySqlPWD);
}catch (Exception $e){
    $err = $e->getMessage();
    die($err);
}
$time = date('Y-m-d H:i:s');

$sql = "SELECT * FROM Recruit_Basic WHERE booth='$booth'";
$rs = $pdo->query($sql);
$info = $rs->fetch(PDO::FETCH_ASSOC);
$name = $info[name];
if(empty($name)){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>您输入的展位号还没有分配给任何招聘单位。请返回重试！</h1>");
}

$sql = "SELECT time FROM `Auth_ClassRoom_Apply` WHERE openid='$openid' ORDER BY id DESC";
$rs = $pdo->query($sql);
$info = $rs->fetch(PDO::FETCH_ASSOC);
$startdate = $info[time];
if(!empty($startdate)){
    $enddate = $time;
    $counthour=floor((strtotime($enddate)-strtotime($startdate))/3600);
    if($counthour < 1){
        header("Content-type: text/html; charset=utf-8");
        die("<h1>您的申请过于频繁，请稍后再试！</h1><script type='text/javascript'>alert('您的申请过于频繁，请稍后再试！');</script>");
    }
}

$sql = "SELECT COUNT(id)count FROM `Auth_ClassRoom_Apply` WHERE status='unknow'";
$rs = $pdo->query($sql);
$info = $rs->fetch(PDO::FETCH_ASSOC);
$count = $info[count];

$sql = "INSERT INTO `Auth_ClassRoom_Apply`(`booth`, `openid`, `type`, `time`, `status`) VALUES ('$booth','$openid','$type','$time','unknow')";
$pdo->exec($sql);

/*
print 'openid：'.$openid;
print '<br>展位号：'.$booth;
*/
?>

<!DOCTYPE html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
  <meta name="viewport" content="initial-scale=1.0,user-scalable=no"/> 
  <title>忻州师范学院-双选会</title>
  <script src="js/jquery-1.7.1.min.js"></script>
  <script src="js/easyBackground-min.js"></script>
<LINK REL=StyleSheet HREF="style/style.css" TYPE="text/css" MEDIA=screen>
<link rel="stylesheet" href="css/styles.css" media="screen">
<meta name="robots" content="noindex,follow" />
</head>
<body>
<script type="text/javascript">
      $(document).ready(function() {
    $('body').easyBackground({
        wrapNeighbours: true
    });
      });

  </script>
<div align="center" style="width=device-width;max-width:500px;margin:0 auto;"><img style="width:100%;" src="./images/title.png" /></div>
  <div class="container">
    <div class="login">
      <h1>试讲教室申请结果</h1>
      <p><?php print $name;?>：</p>
      <p style="text-indent:2em;">您好，您的申请已经受理，处理完成后将通过微信(忻州师范学院招生就业处公众号)通知您。</p>
      <p style="text-indent:2em;">在您前面还有<?php print $count;?>个单位在等待。</p>
      <p><br></p>
      <p style="text-align:right;">2017年双选会教室管理组<br><?php print $time;?></p>
    </div>
  </div>
  
<footer style="clear:both;text-align:center;width:auto;margin-top:6em;">
	<!--<a href="http://www.miibeian.gov.cn/" style="text-decoration:none;">蒙ICP备17000857号</a>-->
	<br><font color="white">忻州师范学院 · 招生就业处<br>大学生就业创业小组 · 掌上忻师团队
  <br>Copyright © 2016 - 2017 Rebeta Inc. All Rights Reserved.</font>
</footer>
</body>
</html>