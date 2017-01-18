<?php 

$data = file_get_contents("futbols1.json");
$json = json_decode($data);
        
        //print_r($json);

echo $json->Spele->Komanda[0]->Nosaukums;
echo $json->Spele->Komanda[1]->Nosaukums;