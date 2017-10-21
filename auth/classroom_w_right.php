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

$username = $_SESSION["user"];
$sql = "SELECT * FROM Auth_User WHERE username='$username'";
$rs = $pdo->query($sql);
$rows = $rs->fetch(PDO::FETCH_ASSOC);
if($rows[type] != "admin" && $rows[type] != "classroom"){
    die("你没有此权限！");
}

if(empty($_GET["type"]) && empty($_GET["operate"]) && $_GET["type"] != "unknow" && $_GET["type"] != "pass" && $_GET["type"] != "denied" && $_GET["from"] != "unknow" && $_GET["from"] != "pass" && $_GET["from"] != "denied" && $_GET["operate"] != "pass" && $_GET["operate"] != "denied"){
    die("请选择操作！");
}

if($_GET["operate"]){
    if(!is_numeric($_GET["id"])){
        die("请选择操作！");
    }
    
    require_once 'wx_SendMsg.php';
    $sender = new SendMsgClass();
    $sql = "SELECT * FROM Auth_ClassRoom_Apply WHERE id='".$_GET["id"]."'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    $booth = $info[booth];
    $type = $info[type];
    $zpdw = $info[openid];
    
    $time = date("y-m-d H:i:s");
    $sql="INSERT INTO `Auth_ClassRoom_Log`(`booth`, `user`, `time`, `operation`) VALUES ('$booth','$username','$time','".$_GET["operate"]."')";
    //die("$sql");
    $pdo->exec($sql);
    
    if($_GET["operate"] == "pass"){
        $sql = "SELECT * FROM Recruit_Basic WHERE booth='$booth'";
        $rs = $pdo->query($sql);
        $dwinfo = $rs->fetch(PDO::FETCH_ASSOC);
        if($dwinfo[classroom] != "无"){
            $sql="UPDATE Auth_ClassRoom_Apply SET status='pass' WHERE id='".$_GET["id"]."'";
            $pdo->exec($sql);
            $sql = "SELECT * FROM Auth_ClassRoom WHERE aka='$dwinfo[classroom]'";
            $rs = $pdo->query($sql);
            $info = $rs->fetch(PDO::FETCH_ASSOC);
            $outtime = date('H:i:s', strtotime ("+2 hour", strtotime($info[mark])));
            $result = $sender->sendMsg($zpdw,$dwinfo[name].'：您好，您已经申请过试讲教室('.$info[aka].'),到期时间：'.$outtime.'！如需增加时间请联系您的教室管理员:'.$info[manager].'（联系电话：'.$info[manager_phone].'）');
            if($result->errcode == "0"){
                die("<script language=javascript>alert('已经申请过试讲教室！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
            } else {
                die("<script language=javascript>alert('已经申请过试讲教室，通知消息发送失败！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
            }
        }
        $sql = "SELECT * FROM Auth_ClassRoom WHERE type='$type' ORDER BY mark ASC LIMIT 1";
        $rs = $pdo->query($sql);
        $info = $rs->fetch(PDO::FETCH_ASSOC);
        $now = date("y-m-d H:i:s");
        $mark = $info[mark];
        $hour = floor((strtotime($now)-strtotime($mark))/3600);
        if($hour > 2){
            $sql = "SELECT * FROM Recruit_Basic WHERE classroom='$info[aka]'";
            $rs = $pdo->query($sql);
            $tempinfo = $rs->fetch(PDO::FETCH_ASSOC);
            if(!empty($tempinfo[id])){
                $sql = "UPDATE Recruit_Basic SET classroom='无' WHERE id='$tempinfo[id]'";
                $pdo->exec($sql);
            }
            $sql = "UPDATE Recruit_Basic SET classroom='$info[aka]' WHERE booth='$booth'";
            $pdo->exec($sql);
            /*
            if($sysj == "长期"){
                $now = date("Y-m-d H:i:s",strtotime("+1 day"));
            }*/
            $sql = "UPDATE Auth_ClassRoom SET mark='$now' WHERE id='$info[id]'";
            $pdo->exec($sql);
            $sql="UPDATE Auth_ClassRoom_Apply SET status='pass' WHERE id='".$_GET["id"]."'";
            $pdo->exec($sql);
            
            $outtime = date('H:i:s', strtotime ("+2 hour", strtotime($now)));
            $result = $sender->sendMsg($info[manager_openid],'招聘单位【'.$dwinfo[name].'】即将前往：'.$info[aka].'（到期时间：'.$outtime.'）。联系人：'.$dwinfo[liaisons].'，联系电话：'.$dwinfo[liaisons_phone].'。请注意接待。     （收到后请回复）');
            if($result->errcode == "0"){
                print "<script language=javascript>alert('教室管理员：$info[manager]通知成功！');</script>";
            } else {
                print "<script language=javascript>alert('教室管理员：$info[manager]通知失败！请使用其他方式通知！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>";
            }
            
            $result = $sender->sendMsg($zpdw,$dwinfo[name].'：您好，分配给您的试讲教室为：'.$info[aka].',到期时间：'.$outtime.'！您的教室管理员是:'.$info[manager].'（联系电话：'.$info[manager_phone].'）');
            if($result->errcode == "0"){
                die("<script language=javascript>alert('申请成功，教室为：$info[aka]！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
            } else {
                die("<script language=javascript>alert('申请成功，通知消息发送失败：$info[aka]！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
            }
        } else {
            $result = $sender->sendMsg($zpdw,$dwinfo[name].'您好，很抱歉的通知您：当前已经没有可用教室！请您半小时后再试。');
            if($result->errcode == "0"){
                die("<script language=javascript>alert('".$hour."当前已经没有可用教室！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
            } else {
                die("<script language=javascript>alert('".$hour."当前已经没有可用教室！通知消息发送失败！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
            }
        }
    } else {
        $sql="UPDATE Auth_ClassRoom_Apply SET status='denied' WHERE id='".$_GET["id"]."'";
        $pdo->exec($sql);
        die("<script language=javascript>alert('已经设置拒绝！');window.location='classroom_w_right.php?type=".$_GET["from"]."'</script>");
    }
}

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
if($_GET["type"]){
    echo "<h1 style='text-align:center;'>";
    if($_GET["type"] == "unknow"){
        echo "待审核";
    } elseif ($_GET["type"] == "pass") {
        echo "已同意";
    } else {
        echo "已拒绝";
    }
    echo "</h1>";
    $sql="SELECT * FROM Auth_ClassRoom_Apply WHERE status='".$_GET["type"]."'";
    $rs = $pdo->query($sql);
    $rows = $rs->fetchall(PDO::FETCH_ASSOC);
    foreach ($rows as $row){
        echo "❖展位号：<b style=\"color:#E53333;font-size:40px;\">*$row[booth]</b>&emsp;❖申请教室类型：$row[type]&emsp;❖申请时间：$row[time]&emsp;❖OPENID：$row[openid]<br><br>";
        echo "<a href='classroom_w_right.php?operate=pass&id=$row[id]&from=".$_GET["type"]."'>同 意 ✔</a>&emsp;&emsp;<a href='classroom_w_right.php?operate=denied&id=$row[id]&from=".$_GET["type"]."'>拒 绝 ✘</a><br>"; //operate
        echo "<br><HR><br>"; //operate
    }
    
}
?>

</body>
</html>