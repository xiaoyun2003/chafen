<?php
//导入相关库函数
include "function.php";
include "config.php";
include "sql.php";


$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(!$conn)
{
    die('Could not connect: ' . mysqli_error());
}
mysqli_select_db($conn, $database);
mysqli_query($conn , "set names utf8");



$ksdm=strval(intval($_GET["ksdm"]));
$sid=$_GET["sid"];
$km=$_GET["km"];




//前置访问测试
if(!Get("http://dmj006.51baxue.com:19001/edei/loginAction!loginstart.action")){
//当cookie无效时
//这里使用交叉变更登录技术
if(GetConfig("ua")=="pc"){
SetConfig("ua","mobile");
}else{
SetConfig("ua","pc");
}
$coo=GetCookies();
SetConfig("cookie",$coo);
}


//获取考试信息
$temp=json_decode(Get("http://dmj006.51baxue.com:19001/edei/ajaxAction!getReportExam.action?startTime=&endTime=&isHistory=&ziyuan_num=802&_=1639195726662"),true)["data"];
//自动踢出高二数据
$ksdms=array();
foreach($temp as $val){
if(stripos($val["name"],"19级")===false){
}else{
$ksdms[strval($val["num"])]=$val["name"];
}

}

foreach($ksdms as $key=>$val){
if($ksdm=="0"){
$ksdm=$key;
$ksmc=$val;
break;
}else{
$ksmc=$ksdms[$ksdm];
break;
}
}




//日志记录
file_put_contents("log.log","[".date('Y-m-d h:i:s',$_SERVER['REQUEST_TIME'])."]访问$ksmc:".$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND);
//单访问更新算法
//获取科目信息
$kms=json_decode(Get("http://dmj006.51baxue.com:19001/edei/jsp/main/ajaxAction!getReportSubject.action?exam=$ksdm&school=12&grade=12&type=0&isHistory=F&showZSubject=true&subCompose=0&showHekeSubject=true&_=1643549372916"),true);
$ea=array();
$ea[-1]="总分";
foreach($kms as $val){
$nkm=intval($val["num"]);
if($nkm==101 || $nkm==102 || $nkm==105 || $nkm==131 || $nkm==111 || $nkm==112 || $nkm==110 || $nkm==0 || $nkm==27){



///这太ff了，我还需要对于英语和日语进行合并处理
if($km==""){
$km=strval($nkm);
$kmmc=$val["name"];
}





//好的，现在获取sql已有的表
if(!CheckData($conn,"k".$ksdm,"subject","subject=".strval($nkm))){
//没有这条数据
SetExam($conn,$ksdm,$nkm);
}
if($nkm==105 || $nkm==131){
$nkm=105;
$ea[$nkm]="外语";
}else{
$ea[$nkm]=$val["name"];
}



}
}

if(!CheckData($conn,"k".$ksdm,"subject","subject=-1")){
//没有这条数据
SetExam($conn,$ksdm,-1);
}
if(count($ea)==8 && $km==""){
$km="-1";
$kmmc="总分";
}else{
$kmmc=$ea[intval($km)];
}
//获取成绩
//读取太费网络，先读取缓存
$res=GetExam($conn,$ksdm,intval($km));

$chengji=array();
if($km=="-1"){
$chengji["110"]=array(101=>"语文",102=>"数学",105=>"外语",111=>"历史",112=>"地理(原始分/赋分)",110=>"生物(原始分/赋分)",0=>"三总",27=>"三选(原始分/赋分)",-1=>"总分(原始分/赋分)");
//重新组合数据
foreach($res as $key=>$val){
$tmpkm=intval($val["subject"]);
if($tmpkm==105 || $tmpkm==131){
$tmpkm=105;
$tmp="外语";
}
if($tmpkm==-1){
$chengji[$val["sid"]]=array($tmpkm=>array("score"=>$val["score"],"fscore"=>$val["fscore"],"crank"=>$val["crank"]),"name"=>$val["name"]);
}else{
$chengji[$val["sid"]][$tmpkm]=array("score"=>$val["score"],"fscore"=>$val["fscore"]);
$chengji[$val["sid"]]["name"]=$val["name"];
}
}
}else{
foreach($res as $key=>$val){


$tmpkm=intval($val["subject"]);
$tmp=array(27=>"三选(原始分/赋分)",0=>"三总",105=>"外语",102=>"数学",101=>"语文",110=>"生物(原始分/赋分)",112=>"地理(原始分/赋分)",111=>"历史")[intval($tmpkm)];



if($tmpkm==105 || $tmpkm==131){
$tmpkm=105;
$tmp="外语";
}
$chengji["110"][strval($tmpkm)]=$tmp;
$tmp=substr($tmp,0,6);
$chengji["110"]["c".strval($tmpkm)]=$tmp."班排名";
$chengji["110"]["g".strval($tmpkm)]=$tmp."年排名";
$chengji["110"]["a".strval($tmpkm)]=$tmp."区排名";
if($tmpkm==intval($km)){
$chengji[$val["sid"]]=array($tmpkm=>array("score"=>$val["score"],"fscore"=>$val["fscore"],"crank"=>$val["crank"],"grank"=>$val["grank"],"arank"=>$val["arank"]),"name"=>$val["name"]);
}else{
$chengji[$val["sid"]][$tmpkm]=array("score"=>$val["score"],"fscore"=>$val["fscore"],"crank"=>$val["crank"],"grank"=>$val["grank"],"arank"=>$val["arank"]);
$chengji[$val["sid"]]["name"]=$val["name"];
}
}
}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"> 
	<title>小云成绩查询平台</title>
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script>
	function select_click(s){
	//动态设置上显内容
	var t=document.getElementById("bt").title;
	window.location.href="./?ksdm="+s.id+"&km="+t
	}
	function select_click1(s){
	//动态设置上显内容
	var t=document.getElementById("btext").title;
	
	window.location.href="./?km="+s.id+"&ksdm="+t
	
	}
	
	
	</script>
	<style>
	.li-height{
	height:30px
	} 
	
	
	</style>
</head>


<body>


<p>考试名称:<?php echo $ksmc."    ".$kmmc;?></p>

<div class="dropdown" style="float:left">
	<button type="button" class="btn dropdown-toggle" id="dropdownMenu1" 
			data-toggle="dropdown">
		<span id="btext" title="<?php echo $ksdm;?>"><?php echo $ksmc;?></span>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		<?php
		foreach($ksdms as $k=>$v){
		$out .="<li role=\"presentation\" class=\"li-height\" ><span onclick=\"select_click(this)\" id=".$k." role=\"menuitem\" tabindex=\"-1\">".$v."</span></li>";
		}
		echo $out;
		?>
	</ul>
</div>


<div class="dropdown" style="float:left;margin-left:50px;">
	<button type="button" class="btn dropdown-toggle" id="dropdownMenu1" 
			data-toggle="dropdown">
		<span id="bt" title="<?php echo $km;?>"><?php echo $kmmc;?></span>
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		<?php
		foreach($ea as $k=>$v){
		$out1 .="<li role=\"presentation\" class=\"li-height\" ><span onclick=\"select_click1(this)\" id=".strval($k)." role=\"menuitem\" tabindex=\"-1\">$v</span></li>";
		}
		echo $out1;
		?>
	</ul>
</div>

<!--Table5 -->
        
        <br><br>
        
        <p>*温馨提示:点击姓名可以查看答题卡*</p>
        <div class="table" style="width:1400px;">
            <table class="table table-striped table-bordered">
                
                <thead class="thead-dark">
                    <tr>
                        
                        <?php
                        
                      $harr=array();
                        if(count($chengji)<=1){
                
                
                 echo "<center><th scope=\"col\"><font size=\"5\">暂无数据(嘤嘤嘤)</font></th></center>";
                }else{
                
                echo "<th scope=\"col\">排名</th>".
                        "<th scope=\"col\">姓名</th>";
                       foreach($chengji["110"] as $k=>$v){
                      
                       echo "<th scope=\"col\">$v</th>";
                       
                       }
                       
                       }
                       ?>
                        
                    
                    </tr>
                </thead>
                
               
                <tbody>
                <?php
                
               
                if(count($chengji)<=1){ 
                }else{
                
    foreach($chengji as $k=>$v){
    if($k!="110"){
    echo "<tr><th scope=\"row\">".$v[intval($km)]["crank"]."</th><th scope=\"row\">"."<a href=\"dtk.php?ksdm=$ksdm&km=$km&sid=$k&name=".$v["name"]."\">".$v["name"]."</a></th>";
  foreach($chengji["110"] as $tm=>$tm1){
  //提取km
  $ttm=substr($tm,1);
  if(substr($tm,0,1)=="c"){
  echo "<th scope=\"row\">".$v[$ttm]["crank"]."</th>";    
  }elseif(substr($tm,0,1)=="g"){
  echo "<th scope=\"row\">".$v[$ttm]["grank"]."</th>";  
  }elseif(substr($tm,0,1)=="a"){
  echo "<th scope=\"row\">".$v[$ttm]["arank"]."</th>";  
  }else{
  if(strpos($tm1,"/")!==false){
             if($v[$tm]["fscore"]){         
                      $ff=$v[$tm]["fscore"];
                      }else{
                      $ff="暂无";  
             }
               echo "<th scope=\"row\">".$v[$tm]["score"]."/".$ff."</th>";
           }else{
          
            echo "<th scope=\"row\">".$v[$tm]["score"]."</th>";
         }
  }
  }
  echo "</tr>";
   }   }          
   
   }
                ?>
                
                
                
               
                    
                    
                </tbody>
            </table>
        
            <?php    
  echo "<a style=\"text-decoration:underline\" href=\"daan.php?ksdm=".$ksdm."\">查看答案</a>";
  ?>
<center>
<p>creat by 小云</p>
<p>风吹起，君已不见</p>

</center>

</body>
</html>