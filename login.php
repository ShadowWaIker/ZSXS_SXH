<?php
/** Powerd by RebetaStudio
 * 
 *   http://www.rebeta.cn
 *   
 *   20170308
 */

//启动session
session_start();

header("Content-type:text/html;charset=utf-8");
require_once '../Public/Config.php';
require_once("../Public/MailFunctions.php");
$db = new DataBase("rebeta");
if (!empty($_POST)) {
    $name = $_POST[dwmc];
 
    if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$name) || ($name == "")) {
        header("Refresh:3;url=login.html");
        die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回主页!");
    }
       
    $pdo = $db->mysqlconn("rebeta");
    $sql = "SELECT * FROM Recruit_Basic WHERE name='$name'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    if(empty($info[id])){
        echo "不存在本单位！";
        die("<script language=javascript>alert('不存在本单位，请检查是否进行过报名！');window.location='login.html'</script>");
    }
    //die(var_dump($info));
    $smtpemailto = $info[liaisons_email];
    $mailtitle = "完善信息入口";
    $mailcontent = "<p>".$name."：</p><p>&emsp;&emsp;您好，您申请的完善信息入口为：<a href=\"https://api.rebeta.cn/sxh/login.php?session=".$info[id]."\" target=\"_blank\">【点击进入】</a></p><p>&emsp;&emsp;如以上选项无法使用，请复制以下链接到浏览器打开：https://api.rebeta.cn/sxh/login.php?session=".$info[id]."。</p><p>&emsp;&emsp;我们会在您完善信息后再次对贵单位招聘信息进行审核。</p><p><br></p><p style=\"text-align:right;\">忻州师范学院2017年毕业生双选会</p><p style=\"text-align:right;\">线上报名审核组</p>";
    $flag = sendMail($smtpemailto,$mailtitle,$mailcontent,$name);
    if($flag){
        echo "发送邮件成功！";
        die("<script language=javascript>alert('发送邮件成功！');window.location='login.html'</script>");
    }else{
        echo "发送邮件失败！";
        die("<script language=javascript>alert('发送邮件失败！');window.location='login.html'</script>");
    }
} elseif (!empty($_GET)) {
    $id = $_GET[session];
    
    //保存登陆信息
    $_SESSION['id'] = $id;
    //认证通过后重定向浏览器
    header("Location: position.html");
    //确保重定向后，后续代码不会被执行
    exit();
}  else {
   die ("非法请求!");
}

?> 