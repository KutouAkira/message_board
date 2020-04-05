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
        }
        .container{
            width: 100%;
            position: relative;
            top: 20%;
        }
        .form-horizontal{
            width: 100%;
            position: relative;
            top: 20%;
            left: 7.5%;
        }
        .text-tip{
            width: 75%;
            position: relative;
            top: 10px;
        }
        .control-label{
            position: relative;
            left: 5%;
        }
    </style>
    <title>登录或注册</title>
</head>
<body>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <h3 class="text-center">
                Akira的留言板
            </h3>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-md-4 column">
        </div>
        <div class="col-md-4 column">
            <form method="post" class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="name" class="control-label">用户名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name" placeholder="英文字母、数字及_" required />
                    </div>
                </div>
                <div class="form-group">
                    <label for="pwd" class="control-label">密码</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="pwd" name="pwd" placeholder="密——码——" required />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div style="float:right;"><button type="submit" class="btn btn-default btn-primary" name="submit" value="1">登录</button></div>
                        <p class="text-tip">*第一次登录会自动注册</p>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4 column">
        </div>
    </div>
</div>
</body>
<?php
header("Content-Security-Policy: default-src 'self'; script-src 'unsafe-inline' 'self'; style-src 'unsafe-inline' 'self'; img-src 'self' data:; object-src 'none';");
session_start();
if (empty($_POST['submit'])) {
    exit();
}
include_once "config.php";
$name = $_POST['name'];
$pwd = $_POST['pwd'];
if(empty($name) || empty($pwd)){
    echo "<script>alert(\"用户名或密码为空\")</script>";
    exit();
}elseif (!preg_match("/^[A-Za-z0-9_]*$/",$name)){
    echo "<script>alert(\"日别人是不好的哦\")</script>";
    exit();
}elseif ($name === 'hikawa'){
    $pwd_hash = md5($pwd);
    if ($pwd_hash === $root_pwd){
        $_SESSION['name']='hikawa';
        header("location:index.php");
    }else{
        echo "<script>alert(\"日别人是不好的哦\")</script>";
        exit();
    }
}
else{
    $pwd_hash = md5($pwd);
    $search = mysqli_query($cnt, "SELECT * FROM `yuza` WHERE `name`='$name'");
    if($search->num_rows === 1) {
        $search = mysqli_query($cnt, "SELECT * FROM `yuza` WHERE `name`='$name' AND `pwdh`='$pwd_hash'");
        if ($search->num_rows === 1){
            $_SESSION['name']=$name;
            header("location:/");
        }else{
            echo "<script>alert(\"用户名已存在或密码错误，有问题请找管理员\")</script>";
        }
    }else{
        if (mysqli_query($cnt, "INSERT INTO yuza VALUE(`id`, '$name', '$pwd_hash')")){
            $_SESSION['name']=$name;
            echo "<script>alert(\"注册成功\");location.href='/'</script>";
        }else{
            echo "<script>alert(\"注册失败\");location.href='/'</script>";
        }
    }
}
?>