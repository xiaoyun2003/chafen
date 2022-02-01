<html>


<title><?php $name=$_GET["name"];echo $name."的答题卡";?></title>


<body style="background-color:black;">
<?php
include "function.php";
include "config.php";
include "sql.php";
$ksdm=$_GET["ksdm"];
$km=$_GET["km"];
$aaa=strval($_GET["sid"]);
$name=$_GET["name"];






if($ksdm=="" || $km=="" || $aaa==""){
die("<script>alert('参数非法(勇敢云云向前冲!)')</script>");
}


//日志记录
file_put_contents("log.log","[".date('Y-m-d h:i:s',$_SERVER['REQUEST_TIME'])."]答题卡:".$name."@".$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND);
$kms=array(101=>"语文",102=>"数学",105=>"英语",131=>"日语",111=>"历史",112=>"地理",110=>"生物");

$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(!$conn)
{
    die('Could not connect: ' . mysqli_error());
}
mysqli_select_db($conn, $database);
mysqli_query($conn , "set names utf8");



$url="http://dmj006.51baxue.com:19001/edei/";



foreach($kms as $k=>$v){
if(!CheckData($conn,"k".$ksdm,"dtk","subject=$k and sid=$aaa")){

if($ksdm=="" || $km=="" || $aaa==""){
die("<script>alert('参数非法(勇敢云云向前冲!)')</script>");
}


$res=Get("http://dmj006.51baxue.com:19001/edei/functionadd!getStuImg.action?&examNum=$ksdm&gradeNum=12&schoolNum=12&classNum=99412586508318497&studentType=0&step=10&type=0&sNum=2000&c_exam=&rpt_name=A1-%25E5%258D%2595%25E7%25A7%2591%25E6%2588%2590%25E7%25BB%25A9&source=0&isHistory=F&isMoreSchool=T&rate=50&islevelclass=F&expTagType=null&reCalcu=F&fufen=1&subCompose=0&islevel=0&sId=".strval($aaa)."&subjectNum=$k");




preg_match_all('/<img id="img1" src="(.*)"/U', $res, $r);
preg_match_all('/<img id="img2" src="(.*)"/U', $res, $r1);
$url1=$url.$r[1][0];
$url2=$url.$r1[1][0];
if($r[1][0]){
//var_dump("更新....$aaa.");


var_dump(Updata($conn,"k".$ksdm,array("dtk"=>$url1."|".$url2),"subject=$k and sid=$aaa"));
}




}else{
////咩咩咩，有数据
$dtk=Query($conn,"k".$ksdm,array("dtk"),"subject=$k and sid=$aaa")[0]["dtk"];

//var_dump("数据");
$url1=explode("|",$dtk)[0];
$url2=explode("|",$dtk)[1];

}

if($url1!="http://dmj006.51baxue.com:19001/edei/"){
echo "<h1 style=\"color:white\">$v:<h1>";
echo "<img src='$url1' style='width: 100%;height:auto;border: 1px solid green;'>";
echo "<img src='$url2' style='width: 100%;height:auto;border: 1px solid green;'>";
}
}




?>


</body>


</html>