<?php
header("content-type: text/html; charset=utf-8");

function Query($conn,$table,$keys=array("*"),$where="",$order=""){
foreach($keys as $key=>$val){
//开始拼接
$out .=$val.",";
}
$out=substr($out,0,strlen($out)-1);


if($where){
$sql="select ".$out." from $table where $where";
}else{
$sql="select ".$out." from $table";
}




if($order){
$sql="$sql $order;";
}


$retval = mysqli_query( $conn, $sql );
if(! $retval )
{
    die('无法读取数据: ' . mysqli_error($conn));
}
$rows=array();
while($row = mysqli_fetch_assoc($retval))
{
array_push($rows,$row);
}
return $rows;
}




//判断数据是否存在
function CheckData($conn,$table,$data,$where=""){
if(!CheckTable($conn,$table)){
CreatTable($conn,$table);
}
if($where){
$sql="select COUNT($data) as amount from $table where $where";
}else{
$sql="select COUNT($data) as amount from $table";
}
$retval= mysqli_query( $conn,$sql);
if(! $retval )
{
 return false;
}
$res=mysqli_fetch_assoc($retval);

if(intval($res["amount"])==0){
return false;
}else{
return true;
}
}


//更新data
function UpData($conn,$table,$keys,$where=""){
//判断数据是否存在
if(!CheckData($conn,$table,"*",$where)){
Insert($conn,$table,$keys);
}


foreach($keys as $key=>$val){
//开始拼接
if(gettype($val)=="string"){
$val ="'".$val."'";
}
$out .=$key."=".$val.",";
}
$out=substr($out,0,strlen($out)-1);

if($where){
$sql="UPDATE $table SET ".$out." where $where";
}else{
$sql="UPDATE $table SET ".$out;
}


//var_dump($sql);
$retval= mysqli_query($conn,$sql);
if(!$retval )
{
 return false;
}
return true;
}

//插入一条数据
function Insert($conn,$table,$keys){
if(!CheckTable($conn,$table)){
CreatTable($conn,$table);
}
foreach($keys as $key=>$val){
//开始拼接
$out .=$key.",";
if(gettype($val)=="string"){
$out1 .="'".$val."',";
}else{
$out1 .=$val.",";
}
}

$out=substr($out,0,strlen($out)-1);
$out1=substr($out1,0,strlen($out1)-1);


$sql="INSERT INTO $table(".$out.") VALUES (".$out1.")";
$retval = mysqli_query( $conn, $sql );
if(! $retval )
{
  die('无法插入数据: ' . mysqli_error($conn));
  
}
return true;
}

//造一个表

function CreatTable($conn,$table,$keys=array("id"=>"INT NOT NULL AUTO_INCREMENT","name"=>"VARCHAR(20) NOT NULL","sid"=>"BIGINT","subject"=>"INT NOT NULL","score"=>"float(5,1) NOT NULL","fscore"=>"float(5,1)","crank"=>"INT","grank"=>"INT","arank"=>"INT","dtk"=>"VARCHAR(500)","PRIMARY KEY"=>"(id)")){

foreach($keys as $key=>$val){
//开始拼接
$out .=$key." ".$val.",";
}
$out=substr($out,0,strlen($out)-1);
$sql="CREATE TABLE IF NOT EXISTS $table(".$out.")ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$retval = mysqli_query($conn,$sql);
if(!$retval)
{
die('数据表创建失败: ' . mysqli_error($conn));
}
return true;
}




//判断数据表是否存在
function CheckTable($conn,$table) {
$retval= mysqli_query( $conn,"show tables;");
if($retval){
while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC)){
if($row["Tables_in_chafen"]==$table){
return true;
}
}
return false;
}else{
return false;
}
}
?>