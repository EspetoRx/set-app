<?php
    $host = "us-cdbr-iron-east-01.cleardb.net";
    $username = "b4374046414e9f";
    $password = "05e528e1";
    $db = "heroku_7d1bac14eb9e1ae";
    $PicNum = $_GET["PicNum"];
    $con = mysqli_connect($host,$username,$password,$db) or die("Impossível conectar ao banco."); 
    $result = mysqli_query($con, "SELECT foto FROM perfil WHERE id=$PicNum") or die("Impossível executar a query "); 
    $row = mysqli_fetch_object($result); 
    Header( "Content-type: image/gif"); 
    echo $row->foto; 
?>