<?php

get_perfect_heand();


function get_perfect_heand(){
    $target = getcwd().'/allHeands';
    $files = scandir($target);
    $i = 0;
    $media = [];
    foreach( $files as $file ){
        if($i>1){
            $json = file_get_contents($target.'/'.$file)  ;
            $re = '/\"(.*?)\":(.*?)\,/m';
            preg_match_all($re, $json, $matches, PREG_SET_ORDER, 0);
            $media[] = ['media'=>$matches[1][2],'file'=>$file];
        }
       $i++; 
    }
    $maior = 0;
    $key2 = 0;
    foreach ($media as $key => $value) {
        if($maior < $value['media']){
            $maior= $value['media'];
            $key2 = $key;
        }
    }
    var_dump($media[$key2]['file']);
    $sucesso = str_replace('heand_score_','',$media[$key2]['file']);
    $sucesso = str_replace('.txt','',$sucesso);
    $color = file_get_contents(getcwd().'/tmp/5color_32lands.txt')  ;
    $color = explode("\n",$color);

    var_dump($color[$sucesso],$maior);
}