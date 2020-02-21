<?php

include 'capturaInfo.php';
 
$url = "https://www.ligamagic.com.br/?view=dks/deck&id=1552008";
$deck = captura_deck($url);
captura_estatisticas($deck);






function captura_estatisticas($deck){
    $lands = 0;
    $devotion = [];
    $mana_curve = [];

    foreach ($deck as $key => $value) {
        $card_name_cache = str_replace(' ','_',$value['name']);
        $card_name_cache = str_replace('/','|',$card_name_cache);
        $card_json = file_get_contents('cache/'.$card_name_cache.'.json');
        $card_info = json_decode($card_json,true);

        $card_type = $card_info['data'][0]["type_line"];
        $mana_cost = $card_info['data'][0]["mana_cost"];
        $cmc = $card_info['data'][0]["cmc"];
        
        if(is_land($card_type)){
            $lands++;
        }else{

            if(isset($mana_curve[$cmc])){
                $mana_curve[$cmc] = $mana_curve[$cmc]+1;
            }
            else{
                $mana_curve[$cmc] = 1;
            }
            $devition_card = get_devotion($mana_cost);
            if(count($devotion) == 0){
                $devotion = $devition_card;
            }
            else{
                $devotion['r'] = $devotion['r'] + $devition_card['r'];
                $devotion['w'] = $devotion['w'] + $devition_card['w'];
                $devotion['u'] = $devotion['u'] + $devition_card['u'];
                $devotion['g'] = $devotion['g'] + $devition_card['g'];
                $devotion['b'] = $devotion['b'] + $devition_card['b'];
            }

        }
    }
    echo "\n\n";
    echo "quantidade de terrenos : ".$lands."\n\n" ;
    echo "devoção :\n" ;
    echo "  red : ".$devotion['r']."\n";
    echo "  wite : ".$devotion['w']."\n";
    echo "  blue : ".$devotion['u']."\n";
    echo "  green : ".$devotion['g']."\n";
    echo "  black : ".$devotion['b']."\n\n";
    echo "curva de mana\n";
    ksort($mana_curve);
    foreach ($mana_curve as $key => $value) {
        echo "  ".$key." : ".$value."\n";
    }
    echo "\n\n";
    die();
}

function get_devotion($mana_cost){

    $devotion['r'] = intval(substr_count($mana_cost,'{R}'));
    $devotion['w'] = intval(substr_count($mana_cost,'{W}'));
    $devotion['u'] = intval(substr_count($mana_cost,'{U}'));
    $devotion['g'] = intval(substr_count($mana_cost,'{G}'));
    $devotion['b'] = intval(substr_count($mana_cost,'{B}'));

    return $devotion;

}

function is_land($card_type){

    $findme   = 'Land';
    $pos = strpos($card_type, $findme);
    if ($pos === false) {
        return false;
    } else {
        return true;
    }
}
var_dump($deck);


?>