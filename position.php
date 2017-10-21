<?php
/** Powerd by RebetaStudio
 * 
 *   http://www.rebeta.cn
 *   
 *   20170227
 */


//启动session
session_start();

header("Content-type:text/html;charset=utf-8");
require_once '../Public/Config.php';
$db = new DataBase;
$pdo = $db->mysqlconn("rebeta");

if (!empty($_POST)) {
    if(empty($_SESSION['id']))
    {
        die("error");
    }
    $id = $_SESSION['id'];
    $data = $_POST;

    if(strstr($data['flag'],"f5")){
        $sql = "SELECT * FROM Recruit_Basic WHERE id='$id'";
        $rs = $pdo->query($sql);
        $info = $rs->fetch(PDO::FETCH_ASSOC);
        $sql = "SELECT * FROM Recruit_Position WHERE bid='$info[id]'";
        $rs = $pdo->query($sql);
        $position = $rs->fetchall(PDO::FETCH_ASSOC);
        $val = "<br>企业名称：$info[name]<br>联系人：$info[liaisons]<br>联系电话：$info[liaisons_phone]<br>联系邮箱：$info[liaisons_email]";
        if(!empty($position)){
            foreach ($position as $info){
                $val .= "<br><br>招聘职位：$info[position]($info[num]人)&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href='position.php?del=$info[id]'>[删除]</a><br>&nbsp&nbsp&nbsp备注：$info[remarks]";
            }
        }
        $json = '{"val":"'.$val.'"}';
        print json_encode($json);
        die();
    }
    
    //过滤html代码
    $position = htmlspecialchars($data['zpzw']);
    $num = htmlspecialchars($data['zpsl']);
    $remarks = htmlspecialchars($data['bz']);

    if($position!=""&&$num!=""&&$remarks!=""){/*
        if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$position) || ($position == "")) {
            header("Refresh:3;url=position.html");
            die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回!");
        } elseif(!is_numeric($num)) {
            header("Refresh:3;url=position.html");
            die ("<H1>非法参数,招聘人数只可以输入数字.<br><br>即将返回!");
        } elseif(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$remarks) || ($remarks == "")) {
            header("Refresh:3;url=position.html");
            die ("<H1>非法参数,请不要输入特殊字符.（如需要输入符号请使用中文标点）</H1><br><br>即将返回!");
        }
*/
        $sql="INSERT INTO Recruit_Position(bid, position, num, remarks) VALUES ('$id','$position','$num','$remarks')";
        $pdo->exec($sql);
        $sql="UPDATE Recruit_Basic SET review='unknow' WHERE id='$id'";
        $pdo->exec($sql);

        header("Refresh:0;url=position.html");
        die ("保存成功！");
    }
}elseif (!empty($_GET)){
    if(empty($_SESSION['id']))
    {
        header("Refresh:0;url=login.html");
        die("error");
    }
    if (isset($_GET['del'])){
        $id = htmlspecialchars($_GET['del']);
        $sql="DELETE FROM Recruit_Position WHERE id = $id";
        $pdo->exec($sql);
        $sql="DELETE FROM Recruit_Major WHERE pid = $id";
        $pdo->exec($sql);
        header("Refresh:0;url=position.html");
    }
} else {
    header("Refresh:3;url=position.html");
    die ("非法请求.<br><br>即将返回!");
}
?>