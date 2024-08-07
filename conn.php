<?php

$host = "mysql:host=localhost;dbname=exercicio;charset=utf8mb4";
$dbuser = "root";
$dbpass = "";
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'];

try{
  //Conecta ao banco de dados
  $pdo = new PDO($host, $dbuser, $dbpass, $options);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Error $e){
  //Verifica se houve algum erro
  echo "Erro".$e->getMessage()."";
}