<?


//一堆函数库
//有些函数写起来浪费时间，直接从网上爬了源码

//达美嘉登录后获取cookies
function GetCookies(){
//login api
$url="http://dmj006.51baxue.com:19001/edei/loginAction.action";

//login pack
$data="authcode_val=&fp=4086852310&wos=Android&user.loginType=0&logType=T&user.username=13403078661&user.mobile=&user.password=000000";


    if(GetConfig("ua")=="pc"){
    $ua="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36";
    }else{
    $ua="User-Agent:Mozilla/5.0 (Linux; Android 7.1.2; vivo X9Plus L Build/N2G47H; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/88.0.4324.152 Mobile Safari/537.36";
    }
    
    $headers = array(
        'Accept: application/json',
        $ua
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    //返回结果
    if ($res) {
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // 根据头大小去获取头信息内容
    $h= substr($res, 0, $headerSize);
    curl_close($ch);
    $h=explode("Set-Cookie:",$h);
    $h=explode("\n",$h[1]);
    return $h[0];
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        return $error;
    }

}

//http请求get
function Get($url,$headers=array()){
  if(GetConfig("ua")=="pc"){
    $ua="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36";
    }else{
    $ua="User-Agent:Mozilla/5.0 (Linux; Android 7.1.2; vivo X9Plus L Build/N2G47H; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/88.0.4324.152 Mobile Safari/537.36";
    }

$headers = array(
        $ua
    );
    
     $cookies=GetConfig("cookie");
     $headers[] = "Cookie: $cookies";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, false);
    $res = curl_exec($ch);
   //返回结果
    if ($res) {
        curl_close($ch);
        return $res;
      
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        return $error;
    }

}

//post
function Post($url,$data,$headers=array()){
 if(GetConfig("ua")=="pc"){
    $ua="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36";
    }else{
    $ua="User-Agent:Mozilla/5.0 (Linux; Android 7.1.2; vivo X9Plus L Build/N2G47H; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/88.0.4324.152 Mobile Safari/537.36";
    }
$headers = array(
        $ua
    );
    
     $cookies=GetConfig("cookie");
     $headers[] = "Cookie: $cookies";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $res = curl_exec($ch);
    $rh=curl_getinfo($ch);
    //返回结果
    if ($res) {
        curl_close($ch);
        return $res;
      
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        return $error;
    }

}

//获取配置
function GetConfig($key){
$text=file_get_contents("config.ini");
return explode("\n",explode($key.":",$text)[1])[0];
}

//设置配置
function SetConfig($key,$value){
$text=file_get_contents("config.ini");
$v=GetConfig($key);
$ntext=str_ireplace($key.":".$v,$key.":".$value,$text);
file_put_contents("config.ini",$ntext);
}


//获取成绩,
function GetExam($conn,$ksdm,$km){
include "config.php";
//读取数据库数据，没读到就添加
if(!CheckTable($conn,"k".$ksdm)){
CreatTable($conn,"k".$ksdm);
}
//查询
//$res=Query($conn,"k".$ksdm,array("*"),"subject=".$km,"ORDER BY score DESC");
/*

[{"ext1":"","ext2":"","ext3":"","name":"语文","num":"101"},{"ext1":"","ext2":"","ext3":"","name":"数学","num":"102"},{"ext1":"","ext2":"","ext3":"","name":"英语","num":"105"},{"ext1":"","ext2":"","ext3":"","name":"日语","num":"131"},{"ext1":"","ext2":"","ext3":"","name":"物理","num":"108"},{"ext1":"","ext2":"","ext3":"","name":"化学","num":"109"},{"ext1":"","ext2":"","ext3":"","name":"生物","num":"110"},{"ext1":"","ext2":"","ext3":"","name":"历史","num":"111"},{"ext1":"","ext2":"","ext3":"","name":"地理","num":"112"},{"ext1":"","ext2":"","ext3":"","name":"政治","num":"113"},{"ext1":"","ext2":"","ext3":"","name":"三科总分","num":"0"},{"ext1":"","ext2":"","ext3":"","name":"再选两科3","num":"22"},{"ext1":"","ext2":"","ext3":"","name":"选考三科3","num":"27"}]



*/
//判断是否已赋分
if(CheckData($conn,"k".$ksdm,"fscore","subject=$km and fscore!=0")){
$fenshu="fscore";
}else{
$fenshu="score";
}

//如果看总分
if($km==-1){
$res=Query($conn,"k".$ksdm,array("*"),"","ORDER BY $fenshu DESC");
return $res;
}
//看三总
if($km==0){
$res=Query($conn,"k".$ksdm,array("*"),"subject=101 or subject=102 or subject=105 or subject=131 or subject=0 or subject=131","ORDER BY $fenshu DESC");
return $res;

}

//看三选
if($km==27){
$res=Query($conn,"k".$ksdm,array("*"),"subject=111 or subject=112 or subject=110 or subject=27","ORDER BY $fenshu DESC");
return $res;
}
if($km==105){
$res=Query($conn,"k".$ksdm,array("*"),"subject=131 or subject=105","ORDER BY $fenshu DESC");
return $res;
}

$res=Query($conn,"k".$ksdm,array("*"),"subject=$km","ORDER BY $fenshu DESC");
return $res;
}


//////////////////////////

//存储数据，自动获取获取学生成绩，进行存储
function SetExam($conn,$ksdm,$km){
if(!CheckTable($conn,"k".$ksdm)){
CreatTable($conn,"k".$ksdm);
}

//赋分
$fu=json_decode(Get("http://dmj006.51baxue.com:19001/edei/scoreListAction!getData.action?&examNum=$ksdm&gradeNum=12&schoolNum=12&classNum=99412586508318497&studentType=0&step=10&type=0&sNum=100000000&c_exam=&rpt_name=A1-%25E5%258D%2595%25E7%25A7%2591%25E6%2588%2590%25E7%25BB%25A9&source=0&isHistory=F&isMoreSchool=T&rate=50&islevelclass=F&expTagType=null&reCalcu=F&fufen=1&subCompose=0&islevel=0&subjectNum=$km"),true);

//原始分
$yuan=json_decode(Get("http://dmj006.51baxue.com:19001/edei/scoreListAction!getData.action?&examNum=$ksdm&gradeNum=12&schoolNum=12&classNum=99412586508318497&studentType=0&step=10&type=0&sNum=100000000&c_exam=&rpt_name=A1-%25E5%258D%2595%25E7%25A7%2591%25E6%2588%2590%25E7%25BB%25A9&source=0&isHistory=F&isMoreSchool=T&rate=50&islevelclass=F&expTagType=null&reCalcu=F&fufen=0&subCompose=0&islevel=0&subjectNum=$km"),true);

if(count($yuan[1][0])==0){
return false;
}
foreach($yuan[1][0] as $val){
$kmmc=$kms[intval($val["subjectNum"])];
UpData($conn,"k".$ksdm,array("name"=>$val[2],"sid"=>$val[0],"subject"=>$km,"crank"=>intval($val[6]),"grank"=>intval($val[7]),"arank"=>intval($val[8]),"score"=>$val[5]),"sid=".$val[0]." and subject=".strval($km));
}
foreach($fu[1][0] as $val){
$kmmc=$kms[intval($val["subjectNum"])];
UpData($conn,"k".$ksdm,array("name"=>$val[2],"sid"=>$val[0],"subject"=>$km,"crank"=>intval($val[6]),"grank"=>intval($val[7]),"arank"=>intval($val[8]),"fscore"=>$val[5]),"sid=".$val[0]." and subject=".strval($km));
}


}

?>

