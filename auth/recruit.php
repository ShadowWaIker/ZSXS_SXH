<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<?php
/** Powerd by RebetaStudio
 *
 *  http://www.rebeta.cn
 *
 * 20170306
 *
 */

session_start();
require_once '../../Public/Config.php';

$LogWriter = new LogWriter;

if(empty($_SESSION["user"]))
{
    die("<script language=javascript>alert('请重新进行登陆！');window.location='login.php'</script>");
}

?>

</head>
<body>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="147" valign="top"><iframe height="100%" width="100%" border="0" frameborder="0" src="recruit_left.php" name="leftFrame" id="leftFrame" title="leftFrame"></iframe></td>
    <td width="10" bgcolor="#add2da">&nbsp;</td>
    <td valign="top"><iframe height="100%" width="100%" border="0" frameborder="0" src="recruit_right.php" name="rightFrame" id="rightFrame" title="rightFrame"></iframe></td>
  </tr>
</table>
</body>
</html>
