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
        $land_colors[] = ['cor'  => 'r', 'quantidade'=> 0 ];
    } 
    if($estatistica["devotion"]["g"] >= 1){
        $land_colors[] = ['cor'  => 'g', 'quantidade'=> 0 ];
    } 
    if($estatistica["devotion"]["u"] >= 1){
        $land_colors[] = ['cor'  => 'u', 'quantidade'=> 0 ];
    } 
    if($estatistica["devotion"]["b"] >= 1){
        $land_colors[] = ['cor'  => 'b', 'quantidade'=> 0 ];
    } 
    if($estatistica["devotion"]["w"] >= 1){
        $land_colors[] = ['cor'  => 'w', 'quantidade'=> 0 ];
    } 
    
    recursive($land_colors, count($land_colors) - 1, $estatistica["lands"],$estatistica["lands"],count($land_colors).'color_'.$estatistica["lands"]."lands.txt");
}


function recursive($cores, $index, $manas_restantes ,$manas_totais,$filename){
	
	for($i = $manas_restantes; $i >= 0; $i--){
		$cores[$index]['quantidade'] = $i;
		if($index == 0){
			$total = array_sum(array_column($cores, 'quantidade'));
			if($total == $manas_totais){
                $valido = true;
                
                foreach ($cores as $key => $value) {
                    if( $value["quantidade"] == 0){
                        $valido = false;
                    }
                }
                if($valido){
                    $linha = implode( ', ' , array_map(function($item) { return implode( ': ' , $item); }, $cores));
                    file_put_contents($filename,$linha."\n",FILE_APPEND);
                }
				
			}
		}else{
			recursive($cores, $index - 1, $manas_restantes - $i, $manas_totais,$filename);
		}
	}
}



?>