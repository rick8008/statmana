<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

include 'capturaInfo.php';
include 'capturaEstatistica.php';

// fractius : https://www.ligamagic.com.br/?view=dks/deck&id=1572279
// anje     : https://www.ligamagic.com.br/?view=dks/deck&id=1552001
// gwyn     : https://www.ligamagic.com.br/?view=dks/deck&id=1552008
// aminatou : https://www.ligamagic.com.br/?view=dks/deck&id=1436263



$url = "https://www.ligamagic.com.br/?view=dks/deck&id=1436263";


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
    $combinacao = [];
    $lands = $land_num;
    $i = 0;
    $initial_combination = [];
    $legenda= [];
    foreach ($color_lands as $key => $color) {
        if($i == 0){
            $initial_combination[$i] = $land_num-count($color_lands)-1;
            
        }else{
            $initial_combination[$i] = 1;
        }
        $legenda[$i] = $color;
        $i++;
    }
    $combinacao[] =  $initial_combination;
    $done = false;
    $colorspipe = count($initial_combination);
    
    $i2 = 0;
    $fim = false;
    $done = false;
    while($done == false){
        $fim = false;
        while($fim == false){
            if( $initial_combination[$i2] > 1 and isset($initial_combination[$i2+1]) ){
                $initial_combination[$i2+1]++;
                $initial_combination[$i2]--;
                $combinacao[] =  $initial_combination;
            }else{
                $fim= true; 
            }
            
            
        }
        $i2++;
        if($i2 >$colorspipe){
            $done = true;
        }
    }
    var_dump($combinacao);
    die();
        

    




    
    die();
}


?>