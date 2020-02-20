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
$deck = separa_cartas($output);

var_dump(json_encode($deck));
}


// seleciona parte importante do conteudo html 
function separa_cartas($output){

    $re = '/\<tbody\>(.*?)\<\/tbody\>/m';
    preg_match_all($re, $output, $matches, PREG_SET_ORDER, 0);
    $deck = $matches[0][0];
    $deckstats = separa_linha($deck);
    return $deckstats;

}



function separa_linha($deck){
    $deckstats = [];
    $re = '/\<tr\>(.*?)\<\/tr\>/m';
    preg_match_all($re, $deck, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $key => $value) {
        $card = separa_coluna($value[0]);
        if( $card != false){
            $deckstats[] = $card;
        }
        
    }
    return $deckstats;
}

function separa_coluna($deck){
    $re = '/\<td(.*?)\<\/td\>/m';
    preg_match_all($re, $deck, $matches, PREG_SET_ORDER, 0);
    if(count ($matches) == 1){
        return false;
    }
    else{
        
        $quanty = (preg_replace("/[^0-9]/", '', strip_tags($matches[0][0])));
        $name = separa_nome_carta($matches[1][0]);
        $return['quanty'] = $quanty;
        $return['name'] = $name;
        return $return;
    }
   
}

function separa_nome_carta($linha){
    $re = '/href=".\/\?view=cards\/card&card=(.*?)">/m';
    preg_match_all($re, $linha, $matches, PREG_SET_ORDER, 0);
    return (trim($matches[0][1]));
}

?>