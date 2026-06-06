<?php
require_once "../shared/config.inc.php";
require_once "./Implementacion.php";
require_once "./Heureka.php";
use Export\Heureka;
ini_set('memory_limit','3200M');
ini_set('max_execution_time', 0);
$expostInstant=null;
switch($_GET['name']){
    case 'heureka':
        $expostInstant=new Heureka($db);
        break;
}
if($expostInstant instanceof Export\Implementacion){
    $expostInstant->start();
    $xml=$expostInstant->getSimpleXML();
}
if($xml instanceof SimpleXMLElement){
    header('Content-type: text/xml');
    echo $xml->asXML();
   /* if($xml->asXML('./xml/heureka.xml')){
        echo "Count export products:".$expostInstant->getNumberExportItems()."<br>";
    }else{
        echo "Error: File not save to xml.<br>";
    }*/
}