<?php 

$fastagi->verbose("<<<<<<<<<<<<<INICIO AGI TESTE>>>>>>>>>>>>>>>"); //NooP Verbose de informação
//$fastagi->exec("Playback","br/prepaid-welcome"); //Tocar audio com Playback
//$fastagi->exec("background", "br/prepaid-welcome");//Tocar audio com Background
$fastagi->exec("read","TESTE,br/prepaid-welcome,4,,2,5");//read parametros: 1 - Variavel de retorno|2 - audio a ser tocado|3 - qtde digitos esperado|4 - options vazio|5 - qtde tentativas|6 - tempo timeout em segundos
$entrada = $fastagi->get_variable("TESTE");
$fastagi->verbose("RESULT|".$entrada['result']."|");//ao pegar uma variavel o result indica 0 se não encontrada e 1 se encontrada a variavel
$fastagi->verbose("DATA|".$entrada['data']."|");// ao pegar uma variavel os dados estarão no 'data' do array
$fastagi->say_digits($entrada['data']);//falar um numero
$origem = preg_replace("#[^0-9]#","",$fastagi->request['agi_callerid']);//pegar o numero de origem bina
syslog(LOG_INFO, "CHAMADA TESTE ORIGEM:".$origem);//Logar informações no messages do sistema operacional
$fastagi->set_variable("test", "1111");//setar uma variavel
$fastagi->set_variable("test2", $origem);//setar uma variavel

?>
