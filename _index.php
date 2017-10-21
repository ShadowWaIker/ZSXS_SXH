<?php
/** Powerd by RebetaStudio
 * 
 *   http://www.rebeta.cn
 *   
 *   20170225
 */

//启动session
session_start();

header("Content-type:text/html;charset=utf-8");
require_once '../Public/Config.php';
$db = new DataBase;

if (!empty($_POST)) {
    //将身份证中的字母转换为大写
    $name = $_POST[dwmc];
    $type = $_POST[dwlx];
    $address = $_POST[dwdz];
    $liaisons = $_POST[lxr];
    $liaisons_phone = $_POST[sjh];
    $liaisons_email = $_POST[yx];
    
    if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$name) || ($name == "")) {
        header("Refresh:3;url=index.html");
        die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回主页!");
    } elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$type) || ($type == "null")) {
        header("Refresh:3;url=index.html");
        die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回主页!");
    } elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$address) || ($address == "")) {
        header("Refresh:3;url=index.html");
        die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回主页!");
    } elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$liaisons) || ($liaisons == "")) {
        header("Refresh:3;url=index.html");
        die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回主页!");
    }/* elseif(!is_numeric($liaisons_phone) || !preg_match("/^1[34578]{1}\d{9}$/",$liaisons_phone)) {
        header("Refresh:3;url=index.html");
        die ("<H1>联系电话只可以输入手机号码.<br><br>即将返回!");
    }*/ elseif(preg_match("/[\',:;*?~`!#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$liaisons_email) || ($liaisons_email == "")) {
        header("Refresh:3;url=index.html");
        die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回主页!");
    }
    $pdo = $db->mysqlconn("rebeta");
    
    $sql = "SELECT id FROM Recruit_Basic WHERE name='$name'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    if(!empty($info[id])){
        $_SESSION['id'] = $info[id];
        header("Refresh:3;url=login.html");
        die ("您填写的单位已经进行过双选会报名.<br><br>即将前往登录页面!");
    }
    
    $sql = "INSERT INTO Recruit_Basic(name, type, address, liaisons, liaisons_phone, liaisons_email, review) VALUES ('$name','$type','$address','$liaisons','$liaisons_phone','$liaisons_email','unknow')";
    $pdo->exec($sql);
	
    $sql = "SELECT id FROM Recruit_Basic WHERE name='$name'";
    $rs = $pdo->query($sql);
    $info = $rs->fetch(PDO::FETCH_ASSOC);
    
    //保存登陆信息
    $_SESSION['id'] = $info[id];
	
    //认证通过后重定向浏览器
    header("Location: position.html");
    //确保重定向后，后续代码不会被执行
	
    exit();
} else {
   die ("非法请求!");
}

?> 