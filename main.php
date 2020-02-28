<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

include 'capturaInfo.php';
include 'capturaEstatistica.php';

// fractius : https://www.ligamagic.com.br/?view=dks/deck&id=1572279
// anje     : https://www.ligamagic.com.br/?view=dks/deck&id=1552001
// gwyn     : https://www.ligamagic.com.br/?view=dks/deck&id=1552008
// aminatou : https://www.ligamagic.com.br/?view=dks/deck&id=1436263



$url = "https://www.ligamagic.com.br/?view=dks/deck&id=1572279";


$deck = captura_deck($url);
$estatistica = captura_estatisticas($deck);
determine_color_lands($estatistica);









function determine_color_lands($estatistica){

    $land_colors = [];
    if($estatistica["devotion"]["r"] >= 1){
        $land_colors[] = 'r';
    } 
    if($estatistica["devotion"]["g"] >= 1){
        $land_colors[] = 'g';
    } 
    if($estatistica["devotion"]["u"] >= 1){
        $land_colors[] = 'u';
    } 
    if($estatistica["devotion"]["b"] >= 1){
        $land_colors[] = 'b';
    } 
    if($estatistica["devotion"]["w"] >= 1){
        $land_colors[] = 'w';
    } 
    combinations_color_lands($land_colors,$estatistica["lands"]);

}


//dont ask me how i did it !
function combinations_color_lands($color_lands,$land_num){
    $initial = '';
    $land_num=$land_num;
    $possibilidade = [];
    $legenda = [];
    $i = 0;
    foreach ($color_lands as $key => $color) {
        if($i == 0){
            for ($i2=0; $i2 < $land_num - count($color_lands)+1 ; $i2++) { 
                $initial= $initial.$i;
            }
        }else{
            $initial = $initial.$i;
        }
        $i++;
        $legenda[] = $color;
    }
    $possibilidade[] = $initial;
    $done = false;
    $possibilidade = next_possibiliti($initial);
    $pronto = possibiliti_to_options( $possibilidade,$legenda);
    file_put_contents('combination.json',json_encode($pronto));
}



function next_possibiliti($string,$possibilities = []){
   
    $end = false;
    $initial_array = str_split($string);
    $pre_possibiliti = [];
    if(count($possibilities) == 0){
        $possibilities[]=$string;
    }
    foreach ($initial_array as $key => $value) {
        if(isset($initial_array[$key+1]) and $value != $initial_array[$key+1]){

            if(isset($initial_array[$key-1]) and $value == $initial_array[$key-1]){
                $pre_possibiliti = $initial_array;
                $pre_possibiliti[$key] = $pre_possibiliti[$key+1];
                if(!in_array(implode('',$pre_possibiliti),$possibilities)){
                    $possibilities[] = implode('',$pre_possibiliti);
                    echo implode('',$pre_possibiliti)."\n";
                    $possibilities =  next_possibiliti(implode('',$pre_possibiliti),$possibilities);
                }
                
            }
            
             
        }
    }
    
    return $possibilities;
   
}


function possibiliti_to_options($possibilidade,$legenda){
    $return = [];
    foreach ($possibilidade as $key => $value) {
        $colors = [];
        foreach ($legenda as $key2 => $value2) {
            $num = substr_count($value,$key2);
            $colors[$value2] = $num;
        }
        $return[]=$colors;
    }
    return $return;

}



?>