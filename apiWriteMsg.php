<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26/09/17
 * Time: 10:10 PM
 */
require_once "wx_config.php";
include_once "KeywordsAnswer.php";


function writeIntoDatabase()
{
    //echo "writeMsg";
    $openId = $_POST['openid'];
    $time = $_POST['time'];
    $msg = $_POST['content'];
    $nickname = $_POST['nickname'];

    $conn = new mysqli(Wx_Config::SERVERNAME, Wx_Config::USERNAME,Wx_Config::PASSWORD ,Wx_Config::DATABASE);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else {
        $sQuery = "insert into userMsg (from_user,openId,update_time,message) values ('$nickname','$openId','$time','$msg')";
        $stmt = $conn->prepare($sQuery);
        $stmt->execute();
        $stmt->store_result();
        $stmt->close();
    }

}
function writeMsg()
{

    $msg = $_POST['content'];
    //$nickname = $_POST['nickname'];

    $QAItem=new KeywordsAnswer();
    $QAItem->readKeywordsAnswer();

    $answer=$QAItem->giveAnswer($msg);

    if(($QAItem->MatchKeyword and $QAItem->writeMatchedQuestion) or (!$QAItem->MatchKeyword)){
        writeIntoDatabase();
    };


    $data = array(
            'status' => 0,
            'msg' => $answer->content,
            'model'=> "no",
            /*
            'model'=> "ok",
            'menunum'=> 4,
            'menu'=> array(
                    "补充资料",
                    "输入问题",
                    "查看状态",
                    "更多..."*/
            );


        $response['data'] = $data;
        $response["error"] = false;
        header('Content-Type: application/json');
        echo json_encode($response);
    }
