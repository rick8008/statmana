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
emula_deck($deck,$possibilidades_land,$estatistica);





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