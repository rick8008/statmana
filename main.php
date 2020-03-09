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



$url = "https://www.ligamagic.com.br/?view=dks/deck&id=1552001";



$basedir = getcwd();
delete_files($basedir.'/allDecks');
mkdir($basedir.'/allDecks');
delete_files($basedir.'/allHeands');
mkdir($basedir.'/allHeands');
delete_files($basedir.'/tmp');
mkdir($basedir.'/tmp');

$deck = captura_deck($url);
$estatistica = captura_estatisticas($deck);
$possibilidades_land = determine_color_lands($estatistica);
$index_decks = emula_deck($deck,$possibilidades_land,$estatistica);
emulate_heand($index_decks);
get_perfect_heand();



function get_perfect_heand(){
    $target = getcwd().'/allHeands';
    $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
    var_dump($files);
       die(); 
    foreach( $files as $file ){

       $json = file_get_contents($file)  ;
       var_dump($file,$json);
       die();    
    }

}



function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

        foreach( $files as $file ){
            delete_files( $file );      
        }

        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
}





function emulate_heand($possibilidades){
    $i = 1;
    
    while ($i < $possibilidades) {
        $deck = file_get_contents('allDecks/deck_possibiliti_'.$i.'.txt');
        $deck = json_decode($deck,true);
        $i3 = 1;
        $score = [];
        while ($i3 < 10000) {
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
            $score[] = score_heand($heand,$draw,$deck["commander"]);
            $i3++;
        }

        $final_score = trata_score($score);
        $json = json_encode($final_score);
        file_put_contents('allHeands/heand_score_'.$i.'.txt',json_encode($json));
        echo $i.' possibility verified'."\n";
        $i++;
    }
}


function trata_score($score) {
    $media_land = 0;
    $moda_land = [];

    $media_can_cast = 0;
    $moda_can_cast = [];

    $media_cant_cast = 0;
    $moda_cant_cast = [];

    foreach ($score as $key => $value) {
        $media_land = $media_land+$value["lands"];
        $moda_land[$value["lands"]] = (isset($moda_land[$value["lands"]])?$moda_land[$value["lands"]]:0)+1;

        $media_can_cast = $media_can_cast+$value["can_cast_how_mutch"];
        $moda_can_cast[$value["can_cast_how_mutch"]] = (isset($moda_can_cast[$value["can_cast_how_mutch"]])?$moda_can_cast[$value["can_cast_how_mutch"]]:0)+1;

        $media_cant_cast = $media_cant_cast+$value["cant_cast_how_mutch"];
        $moda_cant_cast[$value["cant_cast_how_mutch"]] = (isset($moda_cant_cast[$value["cant_cast_how_mutch"]])?$moda_cant_cast[$value["cant_cast_how_mutch"]]:0)+1; 
    }
    

    $return['media_land'] = $media_land/count($score);
    $return['media_can_cast'] = $media_can_cast/count($score);
    $return['media_cant_cast'] = $media_cant_cast/count($score);
    $return['moda_land'] = retur_moda($moda_land);
    $return['moda_can_cast'] = retur_moda($moda_can_cast);
    $return['moda_cant_cast'] = retur_moda($moda_cant_cast);
    return $return;
}


function retur_moda($array){
    $return = [];
    arsort($array);
    foreach ($array as $key => $value) {
        if(end($array) == $value ){
            $return[] =  $key;
        }
    }

    return implode(', ',$return);
}
function score_heand($heand,$draw,$commander){

    $land_sorcer= [];
    $land_on_headn = 0;
    $heand_max_cost = [
        "r" =>0,
        "b" =>0,
        "g" =>0,
        "u" =>0,
        "w" =>0,
        "cost" =>0
    ];
    $cards = [];
    foreach ($heand as $key => $value) {
        if($value["land"]){
            if(isset($land_sorcer[get_color_lands($value["identity"])])){
                $land_sorcer[get_color_lands($value["identity"])] = $land_sorcer[get_color_lands($value["identity"])]+1;
            }else{
                $land_sorcer[get_color_lands($value["identity"])]=1;
            }   
            $land_on_headn++;   
        }else{
            $card_infos = get_color_cards_sorce($value["identity"]);
            $cards[] = $card_infos;
            if($heand_max_cost['r'] < $card_infos['r']  ){
                $heand_max_cost['r'] = $card_infos['r'];
            }
            if($heand_max_cost['b'] < $card_infos['b']  ){
                $heand_max_cost['b'] = $card_infos['b'];
            }
            if($heand_max_cost['g'] < $card_infos['g']  ){
                $heand_max_cost['g'] = $card_infos['g'];
            }
            if($heand_max_cost['u'] < $card_infos['u']  ){
                $heand_max_cost['u'] = $card_infos['u'];
            }
            if($heand_max_cost['w'] < $card_infos['w']  ){
                $heand_max_cost['w'] = $card_infos['w'];
            }
            if($heand_max_cost['cost'] < $card_infos['cost']  ){
                $heand_max_cost['cost'] = $card_infos['cost'];
            }
        }
       
    }
    $return['can_cast_how_mutch'] = 0;
    $return['cant_cast_how_mutch'] = 0;
    $return['lands'] = $land_on_headn;

    foreach ($cards as $key => $value) {
        $castable = true;
        
        if((isset($value['r']) ? $value['r']:0) > (isset($land_sorcer['r']) ? $land_sorcer['r']:0)){ 
            $castable = false;
        }
        if((isset($value['b']) ? $value['b']:0) >  (isset($land_sorcer['b']) ? $land_sorcer['b']:0)){
            $castable = false;
        }
        if((isset($value['g']) ? $value['g']:0) > (isset($land_sorcer['g']) ? $land_sorcer['g']:0)){
            $castable = false;
        }
        if((isset($value['u']) ? $value['u']:0) >  (isset($land_sorcer['u']) ? $land_sorcer['u']:0)){
            $castable = false;
        }
        if((isset($value['w']) ? $value['w']:0) >  (isset($land_sorcer['w']) ? $land_sorcer['w']:0)){
            $castable = false;
        }
        if( $land_on_headn <  (isset($land_sorcer['cost']) ? $land_sorcer['cost']:0)){
            $castable = false;
        }
        if($castable == true){
            $return['can_cast_how_mutch']++;
        }else{
            $return['cant_cast_how_mutch']++; 
        }
    }
    return $return;
   
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
        case '{G}':
            return 'g';
            break;
        case '{U}':
            return 'u';
            break;
    
        default:
        echo "morreu \n";
        var_dump($land);
        die();
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