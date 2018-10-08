<?php
require_once("scripts/raelgc/view/Template.php");
use raelgc\view\Template;

$tpl = new Template("index.html");

$tpl->erro = "<center><p style='color: red;'>E-mail ou senha inválidos.</center>";
$tpl->allert= "allert";

$tpl->show();
?>