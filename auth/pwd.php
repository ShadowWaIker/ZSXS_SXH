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

if(empty($_SESSION["user"]))
{
    die("<script language=javascript>alert('请重新进行登陆！');window.location='login.php'</script>");
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>密码修改</title>
<link href="images/css.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$username=$_SESSION["user"];
$sql = "SELECT * FROM Auth_User WHERE username='$username'";
$rs = $pdo->query($sql);
$rows = $rs->fetch(PDO::FETCH_ASSOC);

if($_POST["Submit"]){
    if($rows["password"]==$_POST["password"])
    {
        $password2=$_POST["password2"];
        $sql="update Auth_User set password='$password2' where username='$username'";
        $pdo->exec($sql);
        echo "<script language=javascript>alert('修改成功,请重新进行登陆！');window.location='login.php'</script>";
        exit();
    } else {
        echo "<script language=javascript>alert('原始密码不正确,请重新输入！');window.location='pwd.php'</script>";
        exit();
    }
}
?>

<table cellpadding="3" cellspacing="1" border="0" width="100%" class="table" align=center>
  <form name="renpassword" method="post" action="">
    <tr> 
      <th height=25 colspan=4 align="center" class="bg_tr">更改管理密码</th>
    </tr>
    <tr> 
      <td width="40%" align="right" class="td_bg">用户名：</td>
      <td width="60%" class="td_bg"><?php echo $rows["username"] ?></td>
    </tr>
    <tr> 
      <td align="right" class="td_bg">原密码：</td>
      <td class="td_bg"><input name="password" type="password" id="password" size="20"></td>
    </tr>
    <tr> 
      <td align="right" class="td_bg">新密码：</td>
      <td class="td_bg"><input name="password1" type="password" id="password1" size="20"></td>
    </tr>
    <tr> 
      <td align="right" class="td_bg">确认密码：</td>
      <td class="td_bg"><input  name="password2" type="password" id="password2" size="20"></td>
    </tr>
    <tr> 
      <td colspan="2" align="center" class="td_bg"> 
        <input class="button" onClick="return check();" type="submit" name="Submit" value="确定更改">
      </td>
    </tr>
  </form>
</table>
</body>
</html>
<script LANGUAGE="javascript">
<!--
function checkspace(checkstr) {
  var str = '';
  for(i = 0; i < checkstr.length; i++) {
    str = str + ' ';
  }
  return (str == checkstr);
}
function check()
{
	  if(checkspace(document.renpassword.password.value)) {
		document.renpassword.password.focus();
	    alert("原密码不能为空！");
		return false;
	  }
	  if(checkspace(document.renpassword.password1.value)) {
		document.renpassword.password1.focus();
	    alert("新密码不能为空！");
		return false;
	  }
	    if(checkspace(document.renpassword.password2.value)) {
		document.renpassword.password2.focus();
	    alert("确认密码不能为空！");
		return false;
	  }
	    if(document.renpassword.password1.value != document.renpassword.password2.value) {
		document.renpassword.password1.focus();
		document.renpassword.password1.value = '';
		document.renpassword.password2.value = '';
	    alert("新密码和确认密码不相同，请重新输入");
		return false;
	  }
		document.admininfo.submit();
	  }
//-->
</script> 
