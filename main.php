<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

include 'helpers/capturaInfo.php';
include 'helpers/capturaEstatistica.php';
include 'helpers/geraAlternativa.php';


// fractius : https://www.ligamagic.com.br/?view=dks/deck&id=1572279
// anje     : https://www.ligamagic.com.br/?view=dks/deck&id=1552001
// gwyn     : https://www.ligamagic.com.br/?view=dks/deck&id=1552008
// aminatou : https://www.ligamagic.com.br/?view=dks/deck&id=1436263



$url = "https://www.ligamagic.com.br/?view=dks/deck&id=1572279";

$deck = captura_deck($url);
$estatistica = captura_estatisticas($deck);
$possibilidades_land = determine_color_lands($estatistica);
$index_decks = emula_deck($deck,$possibilidades_land,$estatistica);
emulate_heand($index_decks);






function emulate_heand($possibilidades){
    $i = 1;
    while ($i < $possibilidades) {
        $deck = file_get_contents('allDecks/deck_possibiliti_'.$i.'.txt');
        $deck = json_decode($deck,true);
        $heandIndex = array_rand($deck["cards"],17);
        $heand = [];
        $draw = [];
        $i2=0;
        foreach ($heandIndex as $key => $value) {
            if($i2 <7){
                $heand[] = $deck["cards"][$value];
            }else{
                $draw[] = $deck["cards"][$value];
            }
            $i2++;
        }
        score_heand($heand,$draw,$deck["commander"]);
       
    }

}


function score_heand($heand,$draw,$commander){

    $land_sorcer= [];
    foreach ($heand as $key => $value) {
        if($value["land"]){
            if(isset($land_sorcer[get_color_lands($value["identity"])])){
                $land_sorcer[get_color_lands($value["identity"])] = $land_sorcer[get_color_lands($value["identity"])]+1;
            }else{
                $land_sorcer[get_color_lands($value["identity"])]=1;
            }      
        }else{
            $card_infos = get_color_cards_sorce($value["identity"]);
            var_dump($card_infos);
        }
       
    }
}




function get_color_cards_sorce($card){

    $return['r'] = intval(substr_count($card,'{R}'));
    $return['w'] = intval(substr_count($card,'{W}'));
    $return['u'] = intval(substr_count($card,'{U}'));
    $return['g'] = intval(substr_count($card,'{G}'));
    $return['b'] = intval(substr_count($card,'{B}'));

    $re = '/{([0-9]+)}/m';
    preg_match_all($re, $card, $matches, PREG_SET_ORDER, 0);
    
    // Print the entire match result
    if(isset($matches[0][1])){
        $return['cost'] = $matches[0][1]+substr_count($card,'{R}')+substr_count($card,'{W}')+substr_count($card,'{U}')+substr_count($card,'{G}')+substr_count($card,'{B}');
    }else{
        $return['cost'] = 0+substr_count($card,'{R}')+substr_count($card,'{W}')+substr_count($card,'{U}')+substr_count($card,'{G}')+substr_count($card,'{B}');
    }
 
    return  $return;
}



function get_color_lands($land){
    switch ($land) {
        case '{W}':
            return 'w';
            break;
        case '{R}':
            return 'r';
            break;
        case '{B}':
            return 'b';
            break;
        case '{R}':
            return 'r';
            break;
        case '{U}':
            return 'u';
            break;
    
        default:
            return '';
            break;
    }

}






function emula_deck($deck,$possibilidades_land,$estatistica){

    
    $land_combination = explode("\n",$possibilidades_land);
    $index_deck = 1;
    foreach ($land_combination as $key => $land_base) {
        if($land_base != ''){
            $deck_possibiliti['cards'] = landtxt_to_array($land_base);
            $i = 0;
            foreach ($estatistica["deck_by_identiti"] as $key => $card) {
                if($i == 0){
                    $deck_possibiliti['commander'] = $card;
                }
                else{
                    $deck_possibiliti['cards'][] = ['land'=>false,'identity'=> $card];
                }
                $i++;
            }
            file_put_contents('allDecks/deck_possibiliti_'.$index_deck.'.txt',json_encode($deck_possibiliti));
            $index_deck++;
        }
        
    }
   return $index_deck-1;

    
}

function landtxt_to_array($txt){
    $land_array = [];
    $re = '/([a-z]): ([0-9]+)/m';

    preg_match_all($re, $txt, $matches, PREG_SET_ORDER, 0);

    foreach ($matches as $key => $match) {
        $i = 1;
        while ($i <= $match[2]) {
            $land_array[] = ['land'=>true,'identity'=> "{".strtoupper($match[1])."}" ];
            $i++;
        }

    }
    return $land_array;

}











?>