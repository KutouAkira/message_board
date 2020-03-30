<?php
date_default_timezone_set("Asia/Shanghai");
class msg{
    public function __construct($cnt){
        $this->cnt = $cnt;
        $getid =mysqli_query($this->cnt, "SELECT `id` FROM `yuza` WHERE `name`='".$_SESSION['name']."'");
        if ($_SESSION['name'] === 'hikawa'){
            $this->uid = -1;
        }elseif ($getid->num_rows>0){
            $id = $getid->fetch_assoc();
            $this->uid = $id['id'];
        }
    }

    public function send($msg, $imgid){
        if (empty($msg) && empty($imgid)){
            echo "<script>alert(\"留言不能为空\");location.href='/'</script>";
            exit();
        }
        $time = ''.date('Y-m-d H:i:s',time());
        if (mysqli_query($this->cnt, "INSERT INTO msgs VALUE(`id`, $this->uid, '$time', '$msg', $imgid)")){
            echo "<script>alert(\"留言成功\");location.href='/'</script>";
        }else{
            echo "<script>alert(\"留言失败\");location.href='/'</script>";
        }
    }

    public function getmsg($name){
        $msgs = mysqli_query($this->cnt, "SELECT * FROM `msgs` ORDER BY `time` desc");
        $msg = '';
        while($row = $msgs->fetch_assoc()){
            $this_id = $row['user'];
            $get_name = mysqli_query($this->cnt, "SELECT * FROM `yuza` WHERE `id`=$this_id");
            $this_name = $get_name->fetch_assoc();
            $msg .= "<div class='text-body'><span>".base64_decode($row["msg"])."<br/></span><br/><div>";
            if (!empty($row['imgid'])){
                $imgid = $row['imgid'];
                $get_img = mysqli_query($this->cnt, "SELECT * FROM `imgs` WHERE `id`=$imgid");
                $img = $get_img->fetch_assoc();
                $img = $img['path'];
                $file_ext = strtolower(substr(strrchr($img, '.'), 1));
                $tmp = 'data:image/'.$file_ext.';base64,'.base64_encode(file_get_contents("$img"));
                $msg .= "<img src='".$tmp."'/><br />";
            }
            if ($row['user'] === $this->uid || $name === 'hikawa') {
                if (empty($this_name['name'])){
                    $msg .= "By <i>hikawa</i> on <small>".$row['time']."</small>";
                }else{
                    $msg .= "By <i>".$this_name['name']."</i> on <small>".$row['time']."</small>";
                }
                $msg .= "<form method='post' role='form' action='index.php'><button type='submit' class='btn btn-default btn-danger' name='del' id='del' value='".$row['id']."'>删除</button></a></form>";
            }else{
                if (empty($this_name['name'])){
                    $msg .= "By <i>hikawa</i> on <small>".$row['time']."</small>";
                }else{
                    $msg .= "By <i>".$this_name['name']."</i> on <small>".$row['time']."</small>";
                }
                $msg .= "<button type='button' class='btn btn-default disabled btn-danger'>删除</button>";
            }
            $msg .= "</div></div><br/>";
        }
        return $msg;
    }

    public function delmsg($id){
        if (!preg_match("/^[0-9]/",$id)){
            echo "<script>alert(\"No, not good.\");location.href='/'</script>";
            exit();
        }
        $uid = mysqli_query($this->cnt, "SELECT * FROM `msgs` WHERE `id`=$id");
        $uid = $uid->fetch_assoc();
        $imgid = $uid['imgid'];
        $uid = $uid['user'];
        if (empty($uid)){
            echo "<script>alert(\"没有此留言\");location.href='/'</script>";
        }
        if ($uid === $this->uid || $_SESSION['name'] === 'hikawa'){
            if (!empty($imgid)){
                $get_path = mysqli_query($this->cnt, "SELECT * FROM `imgs` WHERE `id`=$imgid");
                $imgpath = $get_path->fetch_assoc();
                $imgpath = $imgpath['path'];
                mysqli_query($this->cnt, "DELETE FROM `imgs` WHERE `id`=$imgid");
                unlink("$imgpath");
            }
            mysqli_query($this->cnt, "DELETE FROM `msgs` WHERE `id`=$id");
            echo "<script>alert(\"已删除\");location.href='/'</script>";
        }else{
            echo "<script>alert(\"无权限删除\");location.href='/'</script>";
        }
    }

    public function is_img($str)
    {
        $img = ["jfif", "pjpeg", "jpeg", "jpg", "pjp", "png"];
        $flag = true;
        foreach ($img as $ext) {
            if (preg_match("/$ext/", $str)) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    public  function uploadimg($file){
        if (empty($file['name'])){
            echo "<script>alert(\"看起来什么都没传\");location.href='/'</script>";
        }
        $file_ext = strtolower(substr(strrchr($file['name'], '.'), 1));
        if ($this->is_img($file_ext)) {
            echo "<script>alert(\"这看起来不是图片┗|｀O′|┛ 嗷~~\");location.href='/'</script>";
        }else {
            if ($file_ext !== 'png' ){
                $file_ext = 'jpeg';
            }
            mysqli_query($this->cnt, "INSERT INTO imgs VALUE(`id`, '')");
            $imgid = mysqli_insert_id($this->cnt);
            $tmp_name = ''.$this->uid."_".$imgid.".".$file_ext;
            $path = './img/'.$tmp_name;
            move_uploaded_file($file['tmp_name'], "$path");
            if (mysqli_query($this->cnt, "UPDATE `imgs` SET `path`='$path' WHERE `id`=$imgid")){
                echo "<script>alert(\"上传成功\");location.href='/'</script>";
            }else{
                echo "<script>alert(\"上传失败\");location.href='/'</script>";
            }
            return $imgid;
        }
    }
}
