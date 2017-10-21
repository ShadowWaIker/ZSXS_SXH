<?php 
/** Powerd by RebetaStudio
 *
 *  http://www.rebeta.cn
 *
 * 20170228
 *
 */

session_start();
require_once '../../Public/Config.php';

$db = new DataBase;
$LogWriter = new LogWriter;
$pdo = $db->mysqlconn("rebeta");

if($_POST["Submit"])
{
    $username=$_POST["username"];
    $pwd=$_POST["pwd"];
    $code=$_POST["code"];
    if($code<>$_SESSION["auth"]) {
        die("<script language=javascript>alert('验证码不正确！');window.location='login.php'</script>");
    }
    if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$username) || ($username == "")) {
        die("<script language=javascript>alert('请正确输入用户名，不要使用特殊符号！');window.location='login.php'</script>");
    }elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$pwd) || ($pwd == "请选择")) {
        die("<script language=javascript>alert('请正确输入密码，不要使用特殊符号！');window.location='login.php'</script>");
    }
    $sql = "SELECT * FROM Auth_User WHERE username='$username' AND password='$pwd'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    if(!empty($info[id]))
    {
        if($info[ban] == "True"){
            echo "<script language=javascript>alert('该用户已经被禁用，如需开通条请联系管理员！');window.location='login.php'</script>";
        } else {
            $_SESSION["user"]=$username;
            $_SESSION["id"]=session_id();
            $_SESSION["type"]=$info[type];
            echo "<script language=javascript>alert('登陆成功！');window.location='admin_index.php'</script>"; 
        }
    }
    else
    {
        echo "<script language=javascript>alert('用户名或密码错误！');window.location='login.php'</script>";
    
        die();
    }
}
    
if($_GET['tj'] == 'out'){
 session_destroy();
 echo "<script language=javascript>alert('退出成功！');window.location='login.php'</script>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>招聘单位管理系统 - 登陆</title>
</head>
<body>

<form id="frm" name="frm" method="post" action="" onSubmit="return check()">
  <div id="center">
    <div id="center_left"></div>
    <div id="center_middle">
      <div class="user">
        <label>用户名:
        <input type="text" name="username" id="username" />
        </label>
      </div>
      <div class="user">
        <label>密　码:
        <input type="password" name="pwd" id="pwd" />
        </label>
      </div>
      <div class="chknumber">
        <label>验证码:
        <input name="code" type="text" id="code" maxlength="4" class="chknumber_input" />
        </label>
        <img src="../../Public/Verify.php" style="vertical-align:middle" />
      </div>
    </div>
    <div id="center_middle_right"></div>
    <div id="center_submit">
      <div class="button"> <input type="submit" name="Submit" class="submit" value="登陆">
	</div>	  
    </div>
  </div>
</form>

</body>
</html>
