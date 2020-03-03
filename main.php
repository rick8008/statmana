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













?>