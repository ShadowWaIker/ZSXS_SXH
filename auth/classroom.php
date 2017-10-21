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
if($rows[type] != "admin" && $rows[type] != "classroom"){
    die("你没有此权限！");
}

if($_POST["Submit"])
{
    $zwh = $_POST["zwh"];
    $jslx = $_POST["jslx"];
    $sysj = $_POST["sysj"];
    if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$zwh) || ($zwh == "")) {
        die("<script language=javascript>alert('请正确输入展位号，不要使用特殊符号！');window.location='classroom.php'</script>");
    }elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$jslx) || ($jslx == "null")) {
        die("<script language=javascript>alert('请选择教室类型，不要使用特殊符号！');window.location='classroom.php'</script>");
    }elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$sysj)) {
        die("<script language=javascript>alert('请选择使用时间！');window.location='classroom.php'</script>");
    }
    $sql = "SELECT * FROM Recruit_Basic WHERE booth='$zwh'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    if($info[classroom] != "无"){
        die("<script language=javascript>alert('已经申请过试讲教室，如需增加时间请选择增加时间按钮！');window.location='classroom.php'</script>");
    }
    $sql = "SELECT * FROM Auth_ClassRoom WHERE type='$jslx' ORDER BY mark ASC LIMIT 1";
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
        $sql = "UPDATE Recruit_Basic SET classroom='$info[aka]' WHERE booth='$zwh'";
        $pdo->exec($sql);
        if($sysj == "长期"){
            $now = date("Y-m-d H:i:s",strtotime("+1 day"));
        }
        $sql = "UPDATE Auth_ClassRoom SET mark='$now' WHERE id='$info[id]'";
        $pdo->exec($sql);
        die("<script language=javascript>alert('申请成功，教室为：$info[aka]，试讲教室信息将在掌上忻师中进行展示！');window.location='classroom.php'</script>");
    } else {
        die("<script language=javascript>alert('".$hour."当前已经没有可用教室！');window.location='classroom.php'</script>");
    }
}

if($_POST["add"])
{
    $zwh = $_POST["zwh"];
    if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$zwh) || ($zwh == "")) {
        die("<script language=javascript>alert('请正确输入展位号，不要使用特殊符号！');window.location='classroom.php'</script>");
    }
    $sql = "SELECT * FROM Recruit_Basic WHERE booth='$zwh'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    $mark = date("Y-m-d H:i:s",strtotime("+2 hours"));
    $sql = "UPDATE Auth_ClassRoom SET mark='$mark' WHERE aka='$info[classroom]'";
    if($pdo->exec($sql)){
        die("<script language=javascript>alert('增加时间成功！');window.location='classroom.php'</script>");
    } else {
        die("<script language=javascript>alert('66$info[id] 增加时间失败！');window.location='classroom.php'</script>");
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
<form id="frm" name="frm" method="post" action="" onSubmit="return check()">
  <div id="center">
    <div id="center_middle">
      <div class="zwh">
        <label>展 位 号:
        <input type="text" name="zwh" id="zwh" />
        </label>
      </div>
      <br>
      <div>
      <label>教室类型:
      	<select size="1" name="jslx" id="jslx">
        	 <option value="null">教室类型</option> 
        	 <option value="普通教室">普通教室</option>
        	 <option value="多媒体教室">多媒体教室</option>
        	 <option value="阶梯教室">阶梯教室</option> 
      	</select>
      </label>
      </div>
      <div <?php if($rows[type] != "admin"){echo 'style="display:none;"';}?> >
      <br>
      	<label>使用时间:
      		<select size="1" name="sysj" id="sysj">
        		 <option value="短期" selected="selected">短期</option>
        		 <option value="长期">长期</option>
      		</select>
      	</label>
      </div>
      <br>
    </div>
    <br>
    <div id="center_submit">
      <div class="button"> <input type="submit" name="Submit" class="submit" value="申请教室">&emsp;&emsp;<input type="submit" name="add" class="add" value="增加时间">
	</div>
    </div>
  </div>
</form>
<p><h1>Tips:<br>&emsp;&emsp;<span style="color:#E53333">试讲教室使用时间为自申请之时起2个小时。</span><br>&emsp;&emsp;如需申请案例教室（主楼401、主楼402），音乐教室（艺体楼418、艺体楼420、艺体楼422、艺体楼424）请联系管理员。<br>&emsp;&emsp;如需长期使用教室（使用时间为2小时以上至全天），请联系管理员。</h1></p>
</body>
</html>