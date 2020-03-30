<?php
header("Content-Security-Policy: default-src 'self'; script-src 'unsafe-inline' 'self'; style-src 'unsafe-inline' 'self'; img-src 'self' data:; object-src 'none';");
session_start();
include_once "config.php";
include_once "msg.php";
if(empty($_SESSION['name'])) {
    header("location:login.php");
    exit();
}
if(mysqli_query($cnt,"SELECT * FROM `yuza` WHERE `name`='".$_SESSION['name']."'")->num_rows === 0 && $_SESSION['name'] !== 'hikawa'){
    $_SESSION=array();
    session_destroy();
    header("location:login.php");
    exit();
}
$message = new msg($cnt);
if ($_POST['del']){
    $message->delmsg($_POST['del']);
    $_POST['del'] = NULL;
}
if ($_POST['post']) {
    $msg = htmlspecialchars($_POST['msg'], ENT_QUOTES);
    $msg = addslashes($msg);
    if (!empty($_SESSION['imgid'])) {
        $imgid = $_SESSION['imgid'];
    }else{
        $imgid = 0;
    }
    $message->send(base64_encode($msg), $imgid);
    $_SESSION['imgid'] = NULL;
    $_POST['post'] = NULL;
}
if ($_POST['upload']){
    $_SESSION['imgid'] = $message->uploadimg($_FILES['file']);
    $_POST['upload'] = NULL;
}
if ($_POST['logout']){
    $_POST['logout'] = NULL;
    $_SESSION = Array();
    session_destroy();
    echo "<script>alert(\"已登出\");location.href='login.php'</script>";
}
?>
<!doctype html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <script src="/static/js/jquery-3.4.1.min.js"></script>
    <script src="/static/js/bootstrap.min.js"></script>
    <style type="text/css">
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background:#66ccff;
            position: absolute;
        }
        .container{
            width: 100%;
            position: relative;
            top: 10%;
        }
        .text-body{
            position: relative;
            background:#DDDDDD90;
            border-radius:5px
        }
        .btn-danger{
            position: absolute;
            left: 482px;
            bottom: 0px;
        }
        img{
            position: relative;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
            bottom: 18px;
        }
    </style>
    <title>Akira的留言板</title>
</head>
<body>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <h2 class="text-center">
                你好<?php echo $_SESSION['name']?>
            </h2>
            <br /><br />
            <h4 class="text-center">
                请问您今天要说点什么吗？
            </h4>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-md-3 column">
        </div>
        <div class="col-md-6 column">
            <form method="post" role="form" action="index.php">
                <div class="form-group">
                    <textarea class="form-control" rows="3" id="msg" name="msg" placeholder="Tips：想插入图片的话选择图片点击上传，提示成功后可选写留言，再点发送留言就可以啦"></textarea>
                    <form method="post" role="form" action="index.php"><div style="float:left;"><button type="submit" class="btn btn-default btn-primary" name="logout" id="logout" value="1">登出</button></div></form>
                    <div style="float:right;"><button type="submit" class="btn btn-default btn-primary" name="post" id="post" value="1">发送留言</button></div>
                </div>
            </form>
            <br />
            <form action="index.php" method="post" enctype="multipart/form-data">
                <input type="file" name="file" accept="image/jpeg, image/png" />
                <div style="float:right;"><button type="submit" class="btn btn-default btn-primary" name="upload" id="upload" value="1">上传</button></div>
            </form>
            <br />
            <?php
            echo $message->getmsg($_SESSION['name']);
            $cnt->close();
            ?>
        </div>
        <div class="col-md-3 column">
        </div>
    </div>
</div>
</body>