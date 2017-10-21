<?php 
/** Powerd by RebetaStudio 
 * 
 *  http://www.rebeta.cn
 * 
 * 20170403更新内容：
 * 
 */

error_reporting(0);
$openid = $_GET["session"];
if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$openid) || ($openid == "")){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>检测到非法参数！</h1>");
}

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
<!--
<script type="text/javascript">
alert("试讲教室使用期限为自申请成功起2小时，请按需申请!")
</script>
-->
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
      <h1>试讲教室申请</h1>
      <form method="post" action="apply.php">
        <p><input type="hidden" name="session" value="<?php print $openid;?>"></p>
        <p><input type="text" name="zwh" placeholder="展位号"></p>
        <p><select size="1" name="jslx" id="jslx">
                <option value="null">教室类型</option> 
                <option value="普通教室">普通教室</option>
                <option value="多媒体教室">多媒体教室</option>
                <option value="阶梯教室">阶梯教室</option> 
            </select>
		</p>
        <p class="submit"><input type="submit" name="commit" value="申请"></p>
      </form>
    </div>
  </div>
  
  <footer style="clear:both;text-align:center;width:auto;margin-top:12em;">
	<!--<a href="http://www.miibeian.gov.cn/" style="text-decoration:none;">蒙ICP备17000857号</a>-->
	<br><font color="white">忻州师范学院 · 招生就业处<br>大学生就业创业小组 · 掌上忻师团队
  <br>Copyright © 2016 - 2017 Rebeta Inc. All Rights Reserved.</font>
</footer>
</body>
</html>