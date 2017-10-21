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

$username=$_SESSION["user"];
$sql = "SELECT * FROM Auth_User WHERE username='$username'";
$rs = $pdo->query($sql);
$rows = $rs->fetch(PDO::FETCH_ASSOC);
if($rows[type] != "admin" && $rows[type] != "recruit"){
    die("你没有此权限！");
}

if(empty($_GET["type"]) && empty($_GET["operate"]) && $_GET["type"] != "unknow" && $_GET["type"] != "pass" && $_GET["type"] != "denied" && $_GET["from"] != "unknow" && $_GET["from"] != "pass" && $_GET["from"] != "denied" && $_GET["operate"] != "pass" && $_GET["operate"] != "denied"){
    die("请选择操作！");
}

if($_GET["operate"]){
    //取出排在最后一位的展位号
    $sql = "SELECT * FROM Recruit_Basic ORDER BY booth DESC";
    $rs = $pdo->query($sql);
    $rows = $rs->fetch(PDO::FETCH_ASSOC);
    $maxbooth = $rows[booth];
    if(!is_numeric($_GET["id"])){
        die("请选择操作！");
    }
    if($_GET["operate"] == "pass"){
        $booth = str_pad($maxbooth+1,3,"0",STR_PAD_LEFT);
        $sql="SELECT * FROM Recruit_Basic WHERE id='".$_GET["id"]."'";
        $rs = $pdo->query($sql);
        $row = $rs->fetch(PDO::FETCH_ASSOC);
        if(!empty($row[booth])){
            $sql="UPDATE Recruit_Basic SET review='pass' WHERE id='".$_GET["id"]."'";
            $pdo->exec($sql);
            die("<script language=javascript>alert('已经分配过展位号： $row[booth] ！');window.location='recruit_right.php?type=".$_GET["from"]."'</script>");
        } else {
            $sql="UPDATE Recruit_Basic SET booth='$booth', review='pass' WHERE id='".$_GET["id"]."'";
            $pdo->exec($sql);
            die("<script language=javascript>alert('已经设置同意，自动分配的展位号为： $booth ！请选择短信或邮件方式通知参会单位！');window.location='recruit_right.php?type=".$_GET["from"]."'</script>");
        }
    } else {
        $sql = "SELECT * FROM Recruit_Basic WHERE id='".$_GET["id"]."'";
        $rs = $pdo->query($sql);
        $rows = $rs->fetch(PDO::FETCH_ASSOC);
        $booth = $rows[booth];
        $sql="UPDATE Recruit_Basic SET review='denied',booth=NULL WHERE id='".$_GET["id"]."'";
        $pdo->exec($sql);
        if(!empty($rows[booth]) && $booth < $maxbooth){
            $sql="UPDATE Recruit_Basic SET booth='$booth' WHERE booth='$maxbooth'";
            $pdo->exec($sql);
            die("<script language=javascript>alert('已经设置拒绝，并将最后一个展位的单位($maxbooth)前移到本展位($booth)！');window.location='recruit_right.php?type=".$_GET["from"]."'</script>");
        } else {
            die("<script language=javascript>alert('已经设置拒绝！');window.location='recruit_right.php?type=".$_GET["from"]."'</script>");
        }
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
    $sql="SELECT * FROM Recruit_Basic WHERE review='".$_GET["type"]."'";
    $rs = $pdo->query($sql);
    $rows = $rs->fetchall(PDO::FETCH_ASSOC);
    foreach ($rows as $row){
        echo "❖展位号：<b style=\"color:#E53333;font-size:40px;\">*$row[booth]</b>&emsp;❖单位名称：$row[name]&emsp;❖地址：$row[address]&emsp;❖联系人：$row[liaisons]&emsp;❖电话：$row[liaisons_phone]&emsp;❖邮箱：$row[liaisons_email]<br><br>";
        $sql="SELECT * FROM Recruit_Position WHERE bid='".$row[id]."'";
        $rs = $pdo->query($sql);
        $child_rows = $rs->fetchall(PDO::FETCH_ASSOC);
        foreach ($child_rows as $child_row){
            echo "&emsp;招聘职位：$child_row[position]($child_row[num]人) <br>&emsp;&emsp;备注：$child_row[remarks]<br><br>";
        }
        echo "<a href='recruit_right.php?operate=pass&id=$row[id]&from=".$_GET["type"]."'>同 意 ✔</a>&emsp;&emsp;<a href='recruit_right.php?operate=denied&id=$row[id]&from=".$_GET["type"]."'>拒 绝 ✘</a>&emsp;&emsp;<a href=\"https://api.rebeta.cn/sxh/login.php?session=".$row[id]."\" target=\"_blank\">【完善信息】</a>&emsp;&emsp;<b style=\"color:#E53333\">发送邮件-></b>&emsp;<a href='mail.php?title=hz&to=".$row[name]."&target=".$_GET["type"]."'>需要资质信息</a>&emsp;&emsp;<a href='mail.php?title=wsxx&to=".$row[name]."&target=".$_GET["type"]."'>需要招聘信息</a>&emsp;&emsp;<a href='mail.php?title=wsxxhz&to=".$row[name]."&target=".$_GET["type"]."'>需要资质信息及招聘信息</a>&emsp;&emsp;<a href='mail.php?title=tg&to=".$row[name]."&target=".$_GET["type"]."'>[再次发送]审核通过</a><br>"; //operate
        echo "<br>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<b style=\"color:#E53333\">发送短信-></b>&emsp;<a href='msg.php?to=".$row[liaisons_phone]."&booth=".$row[booth]."&target=".$_GET["type"]."'>审核通过</a><HR><br>"; //operate
    }
    
}
?>

</body>
</html>