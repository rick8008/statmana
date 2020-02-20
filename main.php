<?php


 
$url = "https://www.ligamagic.com.br/?view=dks/deck&id=1552008";
  captura_deck($url);


function captura_deck($url){

// create curl resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, $url);

//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// $output contains the output string
$output = curl_exec($ch);

// close curl resource to free up system resources
curl_close($ch);  
//echo $output;  
separa_cartas($output);

}


// seleciona parte importante do conteudo html 
function separa_cartas($output){

    $re = '/\<tbody\>(.*?)\<\/tbody\>/m';
    preg_match_all($re, $output, $matches, PREG_SET_ORDER, 0);
    $deck = $matches[0][0];
    separa_linha($deck);

}



function separa_linha($deck){
    $cardnames = [];
    $re = '/\<tr\>(.*?)\<\/tr\>/m';
    preg_match_all($re, $deck, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $key => $value) {
        separa_coluna($value[0]);
        die();
    }
}

function separa_coluna($deck){
    $cardnames = [];
    $re = '/\<td(.*?)\<\/td\>/m';
    preg_match_all($re, $deck, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $key => $value) {
        echo count ($matches)."\n";
        var_dump($key,$value[0]);
        die();
    }
}

?>