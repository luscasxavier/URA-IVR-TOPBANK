<?php
$dir='.';




    $patterns[0] = '/[á|â|à|å|ä]/';
    $patterns[1] = '/[ð|é|ê|è|ë]/';
    $patterns[2] = '/[í|î|ì|ï]/';
    $patterns[3] = '/[ó|ô|ò|ø|õ|ö]/';
    $patterns[4] = '/[ú|û|ù|ü]/';
    $patterns[5] = '/æ/';
    $patterns[6] = '/ç/';
    $patterns[7] = '/ß/';
    $patterns[8] = '/'.chr(204).'/';
    $patterns[9] = '/'.chr(236).'/';
    $patterns[10] = '/'.chr(131).'/';
    $patterns[11] = '/'.chr(130).'/';
    $replacements[0] = 'a';
    $replacements[1] = 'e';
    $replacements[2] = 'i';
    $replacements[3] = 'o';
    $replacements[4] = 'u';
    $replacements[5] = 'ae';
    $replacements[6] = 'c';
    $replacements[7] = 'ss';
    $replacements[8] = '';
    $replacements[9] = '';
    $replacements[10] = '';
    $replacements[11] = '';



if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
        $erro=false;
        $novo ='';
        $novo = strtolower($file);
        $novo = str_replace(' ', '', $novo);     
        $novo = preg_replace($patterns, $replacements, $novo);

        $i=0;
        $tam=strlen($novo);
        while($tam > $i){

            if(ord($novo[$i])>122){
            $erro=true;
            echo "ERRO ".$novo."|".ord($novo[$i])."|CARACTER INVALIDO FAVOR VERIFICAR\n";
            } 
            $i++;
        }

        if(!$erro){

            echo "\n OK RENOMENADO ARQUIVO DE ".$file." PARA ".$novo;
            rename($file, $novo);

        }
        }
        closedir($dh);
    }
}


echo "FIM";

?>