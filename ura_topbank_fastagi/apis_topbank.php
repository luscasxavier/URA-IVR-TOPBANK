<?php
$configs = include('config_topbank.php');


//############################################################
//FUNÇÃO QUE RETORNA O TOKEN DE LOGIN PARA USO NAS DEMAIS APIS
function api_login_token()
{
    global $configs;
    $usuario=$configs['user_login'];
    $senha =$configs['pass_login'];
    
    $url = $configs['server_api'].$configs['url_login'];
    $ch = curl_init($url);

    $data = array(
        'login' => $usuario,
        'password' => $senha
    );
    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result);

    if(property_exists($obj,'token')) return $obj->{'token'};
    else return 'error';
}

//############################################################



//############################################################
//FUNÇÃO QUE RETORNA O HORÁRIO DE ATENDIMENTO TOPBANK - API 01
function api_horario_atendimento()
{
    global $configs;
    $token=api_login_token();
    
    if($token != 'error'){//token retornado com sucesso
    $url = $configs['server_api'].$configs['url_api_horario'].$configs['siglaUra'];
    $ch = curl_init($url);
    //echo($url)."\n";


    //echo $url."\n";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Auth-Token: '.$token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result);

    //var_dump($obj);
    $horaInicio = $obj->{'horaInicio'};
    $horaFim = $obj->{'horaFim'};
    $diaSemanaInicio = $obj->{'diaSemanaInicio'};
    $diaSemanaFim = $obj->{'diaSemanaFim'};
    
    //Setando o time zone
    date_default_timezone_set('America/Sao_Paulo');
    //Definindo a hora e minuto atual
    $horaAtual = date('H:i');

    $data = date('Y-m-d');
    //Definindo o dia da semana atual como números
    $diaSemana_numero = date('w', strtotime($data));
    $diaSemana_numero = $diaSemana_numero +1;

    }
    
    //Variaveis para teste
    //$horaAtual = '20:00';
    //$diaSemana_numero = '7';
    
    //Comparação com os horarios de atendimento
    if($horaAtual >= $horaInicio && $horaAtual <= $horaFim && $diaSemana_numero >= $diaSemanaInicio && $diaSemana_numero <= $diaSemanaFim){
        return true;
    }   else{
            return false;
        }
        

}
//############################################################



//############################################################
//FUNÇÃO QUE VALIDA O CNPJ CPF TOPBANK CLIENTE CORPORATIVO SE EXISTE - API 02
function api_valida_cnpj_cpf($identificador, $tipocnpj){

    global $configs;
    $token=api_login_token();
    
    $url = $configs['server_api'].$configs['url_api_valida_cnpj'];
    //echo $url."\n";
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(
        'cnpjCpf'=> $identificador,
        'unidadeNegocio'=> $tipocnpj
    );


    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    //echo $result."\n";
    curl_close($ch);    

    $obj = json_decode($result);
    //var_dump($obj);

   /*if($obj->{'CLIENTE ATIVO'}){
        $retorno = $obj->{'CLIENTE ATIVO'};
        //echo 'CLIENTE ATIVO '.$retorno;
        return true;
   }*/


   if($obj->{'CLIENTE ATIVO'} == "S") return true;
   if($obj->{'CLIENTE ATIVO'} == "N") return false;
        

}
//############################################################



//############################################################
//FUNÇÃO PARA ABRIR SOLICITAÇÃO EASY PARA PROSPECT(EMISSOR) API 03
function api_abre_solicitacao_easy_emissor($identificador, $fone){

    global $configs;
    $token = api_login_token();
    
    if (strlen($identificador) == 11) $atendimentoHumano = 'S';
    if (strlen($identificador) == 14) $atendimentoHumano = 'N';
    
    
    $url = $configs['server_api'].$configs['url_api_prospect_easy_emissor'];
    
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(
        'atendimentoHumano' => $atendimentoHumano,
        'cnpjCpf' => 'string',
        'entidade' => 'CLI',
        'fone' => $fone,
        'identificacao' => $identificador,
        'mensagem' => "ENTRAR EM CONTATO PARA PROSPECT TOPBANK",
        'novaSenha' => 'string',
        'senha' => 'string',
        'tokenTopBank' => 'string'
    );

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);    

    $obj = json_decode($result);

    if(isset($obj->{'PROTOCOLO_ATENDIMENTO'})) return $obj->{'PROTOCOLO_ATENDIMENTO'};
    else return false;
     
}
//############################################################



//############################################################
//FUNÇÃO PARA VALIDAR SE CPF CONSTA NA BASE TOPBANK (USUARIO) - API 04
function api_valida_cadastro_cpf($identificador){

    global $configs;
    $token=api_login_token();
    
    
    $url = $configs['server_api'].$configs['url_api_valida_cadastro_cpf_topbank'];
    //echo $url."\n";
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(
        'atendimentoHumano' => 'string',
        'cnpjCpf' => $identificador,
        'codigoIdentificacao' => 'string',
        'entidade' => 'string',
        'fone' => 'string',
        'identificacao' => 'string',
        'login' => 'string',
        'mensagem' => 'string',
        'novaSenha' => 'string',
        'password' => 'string',
        'senha' => 'string',
        'tokenTopBank' => 'string',
    );

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch); 
    $obj = json_decode($result); 
    //echo $result;
    /*$retorno = $result
    if ($retorno = 'Cliente corporativo!') return true;
    else return false;*/
    return $obj;
   
}
    

//############################################################




//############################################################
//FUNÇÃO VALIDAR CNPJ OU CPF EM BASE CORE TOPBANK(VAREJO) - API 05
function api_valida_cnpjCpf_core_topbank($identificador){

    //echo 'identificador: '.$identificador."\n";

    global $configs;
    $token=api_login_token();
    
    $url = $configs['server_api'].$configs['url_api_verifica_Ec_varejo_topbank_varejo'];
    //echo $url."\n";
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(
        'atendimentoHumano' => 'string',
        'cnpjCpf' => $identificador,
        'entidade' => 'string',
        'fone' => 'string',
        'identificacao' => 'string',
        'mensagem' => 'string',
        'novaSenha' => 'string',
        'senha' => 'string',
        'tokenTopBank' => 'string'
    );

    //var_dump($data);
    //echo "\n";

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);    

    $obj = json_decode($result);

    if($obj->{'CLIENTE ATIVO'} == "S") return true;
   else return false;
    
}
//############################################################




//############################################################
//FUNÇÃO ABRIR SOLICITAÇÃO EASY PARA PROSPECT (VAREJO) - API 06 NÃO ESTÁ SENDO UTILIZADA
function api_abre_solicitacao_easy_varejo($identificador, $fone){

    global $configs;
    $token=api_login_token();
    
    
    $url = $configs['server_api'].$configs['url_api_prospect_easy_varejo'];
    //echo $url."\n";
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(

        'atendimentoHumano' => 'string',
        'cnpjCpf' => 'string',
        'codigoIdentificacao' => 'string',
        'entidade' => 'USU',
        'fone' => $fone,
        'identificacao' => $identificador,
        'login' => 'string',
        'mensagem' => "ENTRAR EM CONTATO PARA PROSPECT TOPBANK",
        'novaSenha' => 'string',
        'password' => 'string',
        'senha' => 'string',
        'tokenTopBank' => 'string'
    );

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);    

    $obj = json_decode($result);
    //var_dump($obj);

    if(isset($obj->{'PROTOCOLO_ATENDIMENTO'})) return $obj->{'PROTOCOLO_ATENDIMENTO'};
    else return false;

}
//############################################################




//############################################################
//FUNÇÃO VALIDAÇÃO SE EXISTE O CNPJ/CPF NA BASE CORE DO TOPBANK(EMPRESA) - API 07
function api_valida_senha_topbank($identificador, $senha){

    global $configs;
    $token=api_login_token();

    //echo $identificador."\n";
    
    $url = $configs['server_api'].$configs['url_api_valida_senha_topbank_empresa'];
    //echo $url."\n";
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Accept: */*';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(

        'dominio'=> 'EMPRESA',
        'login'=> $identificador,
        'senha'=> $senha       
    );

    //var_dump($data);
    //echo "\n";

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    //echo $result."\n";
    curl_close($ch);    

    $obj = json_decode($result);
    //var_dump($obj);
    
    return $obj;
    
}
//############################################################




//############################################################
//ALTERAÇÃO DE SENHA TOPBANK - API 08
function api_altera_senha_topbank($identificador, $nova_senha){

    global $configs;
    $token=api_login_token();
    
    
    $url = $configs['server_api'].$configs['url_altera_senha_topbank'];
    //echo $url."\n";
    $ch = curl_init($url);


    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(
        
        "dominio"=> "EMPRESA",
        "login"=> "$identificador",
        "senha"=> "$nova_senha"    
        
    );

    //var_dump($data);
    //echo "\n";

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);    
    //echo $result;
    //var_dump($result);
    
    if($result == 'Senha alterada com sucesso!' ) return true;
    else return false;
    
}
//############################################################




//############################################################
//ALTERAÇÃO DE SENHA TOPBANK - API 09
function api_retorna_saldo_topbank($identificador){

    global $configs;
    $token=api_login_token();
    
    $url = $configs['server_api'].$configs['url_retorna_saldo_topbank']."/".$identificador;
    $ch = curl_init($url);

    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: application/json';
    $headers[] = 'X-Auth-Token: '.$token;

    $data = array(
        'clienteAtivo' => $identificador,
        'saldoContaCorrente' => 'string',
        'saldoFuturo' => 'string',
        'saldoParcelado' => 'string',
        'saldoTotal' => 'string',
        'saldoTrintaDias' => 'string'
    );

    
    //var_dump($data);
    //echo "\n";

    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);    

    $obj = json_decode($result);
    
    
    if(isset($obj)){
    
        return $obj; 
    }
    
    
}
//############################################################




//############################################################
//GERAÇÃO DE PROTOCOLO - API 10
function protocolo_atendimento($identificador, $processId, $origem){
    global $configs;
    $token=api_login_token();
        
    $url = $configs['server_api'].$configs['url_api_protocolo_atendimento'];
    //echo $url."\n";
    $ch = curl_init($url);
    
    
    $headers = array();
    $headers[] = 'Content-Type:application/json';
    $headers[] = 'Accept: text/plain';
    $headers[] = 'X-Auth-Token: '.$token;
    
    $data = array(
            'cnpj'=>"$identificador",
            'empresa'=>'TOPBANK',
            'flagEntidade'=>'flag_entidade_pros_estab_oc',
            'origemSolicitacao'=>'URA',
            'processId'=>"$processId",
            'telefone'=> "$origem"
    );
    
    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);    

    $obj = json_decode($result);

    //var_dump($obj);

    if(isset($obj)) return $obj->{'PROTOCOLO_ATENDIMENTO'};
    else return false;

}
?>