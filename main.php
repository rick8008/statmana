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
    $array_elems_to_combine = $color_lands;
    $size = $land_num;
    $current_set = array('');

    for ($i = 0; $i < $size; $i++) {
        $tmp_set = array();
        foreach ($current_set as $curr_elem) {
            foreach ($array_elems_to_combine as $new_elem) {
                $tmp_set[] = $curr_elem . $new_elem;
            }
        }
        $current_set = $tmp_set;
    }
    var_dump( $current_set);
    die();
}


?>