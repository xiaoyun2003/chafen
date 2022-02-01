<html>
<title>答案</title>
<body style="background-color:black;">
<?php
include "function.php";


//日志记录
file_put_contents("log.log","[".date('Y-m-d h:i:s',$_SERVER['REQUEST_TIME'])."]答案:".$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND);


$ksdm=$_GET["ksdm"];
if($ksdm==""){
die("<script>alert('参数非法(勇敢云云向前冲!)')</script>");
}

$kms=array(101=>"语文",102=>"数学",105=>"英语",131=>"日语",111=>"历史",112=>"地理",110=>"生物");

$url="http://dmj006.51baxue.com:19001/edei/";
foreach($kms as $k=>$v){
//从net读取cate
preg_match_all('/<input type="hidden" id="examPaperNum" value="(.*)"/U', Get("http://dmj006.51baxue.com:19001/edei/functionadd!getStuImg.action?&examNum=$ksdm&gradeNum=12&schoolNum=12&classNum=99412586508318497&studentType=0&step=10&type=0&sNum=2000&c_exam=&rpt_name=A1-%25E5%258D%2595%25E7%25A7%2591%25E6%2588%2590%25E7%25BB%25A9&source=0&isHistory=F&isMoreSchool=T&rate=50&islevelclass=F&expTagType=null&reCalcu=F&fufen=1&subCompose=0&islevel=0&subjectNum=$k&sId=99065012960263814"), $cr);

$res=Get("http://dmj006.51baxue.com:19001/edei/functionadd!getQueOrAns.action?examPaperNum=".$cr[1][0]."&imgtype=2");
preg_match_all('/<img id="img1" src="(.*)"/U', $res, $r);
$url1=$url.$r[1][0];
if($r[1][0]){
echo "<h1 style=\"color:white\">$v:<h1>";
echo "<img src='$url1' style='width: 100%;height:auto;border: 1px solid green;'>";
}

}



?>


</body>

</html>