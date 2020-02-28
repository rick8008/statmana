<?php

function captura_estatisticas($deck){
    $lands = 0;
    $devotion = [];
    $mana_curve = [];
    $mana_curve_by_color = [];
    $deck_by_identiti = [];
    foreach ($deck as $key => $value) {

        $card_name_cache = str_replace(' ','_',$value['name']);
        $card_name_cache = str_replace('/','|',$card_name_cache);
        $re = '/\(\#(.*?)\)/m';
        preg_match_all($re, $card_name_cache, $matches, PREG_SET_ORDER, 0);
        if(count($matches) >=1 ){
            $card_name_cache = str_replace('_'.$matches[0][0],'',$card_name_cache);
        }
        $card_json = file_get_contents('cache/'.$card_name_cache.'.json');
        $card_info = json_decode($card_json,true);
        $card_type = $card_info["type_line"];
        $mana_cost = $card_info["mana_cost"];
        
        $cmc = $card_info["cmc"];
        
        if(is_land($card_type)){
            $lands = $lands+$value["quanty"];
        }else{

            if(isset($mana_curve[$cmc])){
                $mana_curve[$cmc] = $mana_curve[$cmc]+1;
            }
            else{
                $mana_curve[$cmc] = 1;
            }
            $devition_card = get_devotion($mana_cost);
            $identiti = get_manacurve_by_color($devition_card,$cmc);
            $deck_by_identiti[]=$mana_cost;
            if(!isset($mana_curve_by_color[$cmc][$identiti])){
                $mana_curve_by_color[$cmc][$identiti] = 1;
            }
            else{
                $mana_curve_by_color[$cmc][$identiti]++;
            }
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
    //echo "\n\n";
    //echo "quantidade de terrenos : ".$lands."\n\n" ;
    //echo "devoção :\n" ;
    //echo "  red : ".$devotion['r']."\n";
    //echo "  wite : ".$devotion['w']."\n";
    //echo "  blue : ".$devotion['u']."\n";
    //echo "  green : ".$devotion['g']."\n";
    //echo "  black : ".$devotion['b']."\n\n";
    //echo "curva de mana\n";
    ksort($mana_curve);
    foreach ($mana_curve as $key => $value) {
        //echo "  ".$key." : ".$value."\n";
        ksort($mana_curve_by_color[$key]);
        foreach($mana_curve_by_color[$key] as $key2 => $value2){
            //echo "      ".$key2." : ".$value2."\n";
        }
    }

    //echo "\n\n";
    $return['devotion'] = $devotion;
    $return['lands'] = $lands;
    $return['mana_curve'] = $mana_curve;
    $return['mana_curve_by_color'] = $mana_curve_by_color;
    $return['deck_by_identiti'] =  $deck_by_identiti;
   
    return $return;
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

function get_manacurve_by_color($devition_card,$cmc){
    $cartd_identiti = '';
    $cartd_identiti = ($devition_card['r'] >=1 ? $cartd_identiti.'r':$cartd_identiti);
    $cartd_identiti = ($devition_card['w'] >=1 ? $cartd_identiti.'w':$cartd_identiti);
    $cartd_identiti = ($devition_card['u'] >=1 ? $cartd_identiti.'u':$cartd_identiti);
    $cartd_identiti = ($devition_card['g'] >=1 ? $cartd_identiti.'g':$cartd_identiti);
    $cartd_identiti = ($devition_card['b'] >=1 ? $cartd_identiti.'b':$cartd_identiti);
    
    if($cartd_identiti == ''){
        $cartd_identiti = 'any';
    }
    return $cartd_identiti;
}



?>