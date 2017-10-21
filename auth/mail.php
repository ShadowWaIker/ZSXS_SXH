<?php
session_start();
require_once '../../Public/Config.php';
require_once("../../Public/MailFunctions.php");
$db = new DataBase;
$LogWriter = new LogWriter;
$pdo = $db->mysqlconn("rebeta");
if(empty($_SESSION["user"]))
{
    die("<script language=javascript>alert('请重新进行登陆！');window.location='login.php'</script>");
}

$mailtitle = $_GET['title'];//邮件主题
$mailto = $_GET['to'];//邮件收件人称呼
$target = $_GET['target']; //将要返回的页面
if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$mailtitle)) {
    die ("<H1>非法参数.</H1>");
} elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$mailto)) {
    die ("<H1>非法参数.</H1>");
} elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$target)) {
    die ("<H1>非法参数.</H1>");
}

$sql="SELECT * FROM Recruit_Basic WHERE name='$mailto'";
$rs = $pdo->query($sql);
$rows = $rs->fetch(PDO::FETCH_ASSOC);

$zw = $rows[booth];//展位号
$smtpemailto = $rows[liaisons_email];//发送给谁
$session = $rows[id]; //将id起混淆名为session用于登陆

if($mailtitle == "wsxx"){
	$mailtitle = "需要您完善招聘信息";
	$mailcontent = "<p>".$mailto."：</p><p>&emsp;&emsp;您好，由于审核需要，现需您<span style=\"color:#E53333\">【继续完善信息】</span>。</p><p>&emsp;&emsp;需要完善的信息可能有：单位地址、联系人、联系电话、招聘职位信息。完善入口为：<a href=\"https://api.rebeta.cn/sxh/login.php?session=".$session."\" target=\"_blank\">【点击进入】</a></p><p>&emsp;&emsp;我们会在您完善信息后再次对贵单位招聘信息进行审核。</p><p><br></p><p style=\"text-align:right;\">忻州师范学院2017年毕业生双选会</p><p style=\"text-align:right;\">线上报名审核组</p>";
} elseif($mailtitle == "hz"){
	$mailtitle = "需要您提交单位资质信息";
	$mailcontent = "<p>".$mailto."：</p><p>&emsp;&emsp;您好，由于审核需要，现需您提交<span style=\"color:#E53333\">【营业执照复印件】</span>一份，用于认定招聘单位资质。回执获取方式为：</p><p>&emsp;&emsp;<span style=\"color:#E53333\">营业执照复印件需注明【与原件一致】并【加盖公章】。</span></p><p>&emsp;&emsp;以上信息可拍照回复至本邮箱，或传真至0350-3611333。</p><p><br></p><p style=\"text-align:right;\">忻州师范学院2017年毕业生双选会</p><p style=\"text-align:right;\">线上报名审核组</p>";
} elseif($mailtitle == "wsxxhz"){
	$mailtitle = "需要您完善招聘及单位资质信息";
	$mailcontent = "<p>".$mailto."：</p><p>&emsp;&emsp;您好，由于审核需要，现需您<span style=\"color:#E53333\">【继续完善信息】</span>并提交<span  style=\"color:#E53333\">【营业执照复印件】</span>一份。 </p><p>&emsp;&emsp;需要完善的信息可能有：单位地址、联系人、联系电话、招聘职位信息。完善入口为：<a href=\"https://api.rebeta.cn/sxh/login.php?session=".$session."\" target=\"_blank\">【点击进入】</a>。</p><p>&emsp;&emsp;<span style=\"color:#E53333\">营业执照复印件需注明【与原件一致】并【加盖公章】。</span></p><p>&emsp;&emsp;以上信息可拍照回复至本邮箱，或传真至0350-3611333。 </p><p><br></p><p style=\"text-align:right;\">忻州师范学院2017年毕业生双选会</p><p style=\"text-align:right;\">线上报名审核组</p>";
} elseif($mailtitle == "tg"){
	$mailtitle = "审核通过";
	$mailcontent = "<p>".$mailto."：</p><p>&emsp;&emsp;您好，贵单位所提交招聘信息已经审核通过，审核系统为您分配的展位号为<span style=\"color:#E53333\">【".$zw."】</span>。贵公司所提交信息将在我院“掌上忻师”微信小程序（打开微信-&gt;搜索-&gt;输入掌上忻师即可获取）及“忻州师范学院招生就业处”微信公众号进行公示及宣传。</p><p>&emsp;&emsp;请与2017年04月08日07:40到会，并凭以上展位号进行签到。届时我们的工作人员将带领您前往您的展位。</p><p>&emsp;&emsp;如贵单位无法参加本次双选会请回复本邮件知会我院。</p><p><br></p><p style=\"text-align:right;\">忻州师范学院2017年毕业生双选会</p><p style=\"text-align:right;\">线上报名审核组</p>";
}
$flag = sendMail($smtpemailto,$mailtitle,$mailcontent,$mailto);
if($flag){
    header("Content-type: text/html; charset=utf-8");
    echo "发送邮件成功！";
    if(empty($target)){
        die("<script language=javascript>alert('发送邮件成功！');window.location='https://api.rebeta.cn/sxh/login.html'</script>");
    }
    die("<script language=javascript>alert('发送邮件成功！');window.location='recruit_right.php?type=".$target."'</script>");
}else{
    echo "发送邮件失败！";
    if(empty($target)){
        die("<script language=javascript>alert('发送邮件失败，请重新发送！');window.location='https://api.rebeta.cn/sxh/login.html'</script>");
    }
    die("<script language=javascript>alert('发送邮件失败，请重新发送！');window.location='recruit_right.php?type=".$target."'</script>");
}
?>