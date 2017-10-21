<?php
/** Powerd by RebetaStudio
 *
 *  http://www.rebeta.cn
 *
 * 20170307
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

$sql = "SELECT COUNT(id)num FROM Recruit_Basic WHERE review='pass'";
$rs = $pdo->query($sql);
$row = $rs->fetch(PDO::FETCH_ASSOC);
$pass = $row[num];

$sql = "SELECT COUNT(id)num FROM Recruit_Basic WHERE review='unknow'";
$rs = $pdo->query($sql);
$row = $rs->fetch(PDO::FETCH_ASSOC);
$unknow = $row[num];

$sql = "SELECT COUNT(id)num FROM Recruit_Basic WHERE review='denied'";
$rs = $pdo->query($sql);
$row = $rs->fetch(PDO::FETCH_ASSOC);
$denied = $row[num];

$sql = "SELECT COUNT(id)num FROM Auth_ClassRoom WHERE campus='主区'";
$rs = $pdo->query($sql);
$row = $rs->fetch(PDO::FETCH_ASSOC);
$classroom = $row[num];

$sql = "SELECT COUNT(id)num FROM Auth_ClassRoom WHERE campus='主区' AND mark > SUBDATE(now(),interval 2 hour)";
$rs = $pdo->query($sql);
$row = $rs->fetch(PDO::FETCH_ASSOC);
$classroomout = $row[num];

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>招聘单位审核</title>
<style> 
a{ text-decoration:none} 
</style> 
</head>
<body>

<?php
echo "❖待审核单位：$unknow 个&emsp;❖通过审核单位：$pass 个&emsp;❖未通过审核单位：$denied 个<br><br>❖试讲教室总数：$classroom 个&emsp;❖批出教室数：$classroomout 个&emsp;❖剩余教室数：".($classroom-$classroomout)." 个";
?>

</body>
</html>