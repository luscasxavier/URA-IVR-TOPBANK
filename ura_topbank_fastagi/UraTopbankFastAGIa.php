<?php
require_once 'apis_topbank.php';
require_once 'FrameWorkUraTelek.php';
date_default_timezone_set('America/Sao_Paulo');
$configs = include('config_topbank.php');//Carrega as configurações da URA do arquivo de configuração da mesma.

//INICIALIZACAO DE AMBIENTE
$tentativas=0;//tentativas relizadas
$debug_level=5; //nivel de log desejado  
//1 - ERROS DE EXECUÇÃO 
//2 - ALERTAS
//3 - MENSAGENS INFORMATIVAS
//4 - DEBUG LEVE
//5 - DEBUG TOTAL

$fastagi=$fastagi;

//ARQMAZENAR O NUMERO DE ORIGEM DO CLIENTE NA VARIAVEL ORIGEM
$origem = preg_replace("#[^0-9]#","",$fastagi->request['agi_callerid']);
$uniqueid = $fastagi->request['agi_uniqueid'];

$ddr='';
$ddr=$fastagi->request['agi_extension'];
if($ddr=='32937620') $ddr='40001422';
if($ddr=='32937610') $ddr='08009405511';

$timeStamp=time();
verbose("TIME STAMP : ".$timeStamp);
$canal= 'URA TOPBANK';
//$ticket=$origem.'_'.$timeStamp;
$ticket=$uniqueid;
$indice=0;
$horaAtual = date('H:i');

global $horaAtual;
global $canal;
global $ddr;
global $ticket;
global $indice;

verbose("<<<<<<<<<<<<< INICIANDO URA TOPBANK >>>>>>>>>>>>>>>",3); 
tracking_canal_ativo($canal, $ddr, $ticket, $indice);

//AUDIO0 MSG COVID
//playback("uraTopBank/audio0");

tracking($canal, $ddr, $ticket, $indice, 'INICIO', 'PERCURSO', 'INICIO');
tracking($canal, $ddr, $ticket, $indice, 'INICIO', 'CONTATO', $origem);

Menu_Principal($uniqueid, $origem);

function Menu_Principal($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    verbose("INICIANDO MENU PRINCIPAL",3);
    if(!tracking_canal_ativo($canal, $ddr, $ticket, $indice)) exit();

    //02
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'MENU PRINCIPAL', 'PERCURSO', '1 - ESTABELECIMENTO OU COOPERATIVA OU EMPRESA CLIENTE OU USUARIO');
    
    $opcao='';
    //$opcao = coletar_dados_usuario("uraTopBank/audio1",1);
    $opcao = coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio1",1);
    if($opcao == '-1'){hangup();break;}

    //03
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'MENU PRINCIPAL', 'RESPOSTA', $opcao);
  
    switch ($opcao) {
        case 1:
            playback("uraTopBank/audio100");
            encerra_com_tracking($canal, $ddr, $ticket, $indice, "PRINCIPAL","uraTopBank/audio100","URA ENCERRANDO A LIGACAO");
            hangup();
            //inicializa_ambiente_novo_menu();
            //M1_Estabelecimento_Cooperativa($origem);
        break;

        case 2:
            inicializa_ambiente_novo_menu();
            M2_Cliente_Empresa($uniqueid, $origem);
        break;

        case 3:
            inicializa_ambiente_novo_menu();
            M3_Usuario($uniqueid, $origem);
        break;

        default:
            if(retentar_dado_invalido("PRINCIPAL","uraTopBank/audio6","TIMEOUT")){
                //04
                $indice++;
                tracking($canal, $ddr, $ticket, $indice, 'MENU PRINCIPAL', 'PERCURSO', 'OPCAO INVALIDA (1 - ESTABELECIMENTO OU COOPERATIVA OU EMPRESA CLIENTE OU USUARIO)');
                Menu_Principal($uniqueid, $origem);
            }else{
                //05
                $indice++;
                tracking($canal, $ddr, $ticket, $indice, 'MENU PRINCIPAL', 'PERCURSO', 'TENTATIVAS EXCEDIDAS (1 - ESTABELECIMENTO OU COOPERATIVA OU EMPRESA CLIENTE OU USUARIO)');

                //encerra_por_retentativas("PRINCIPAL","uraTopBank/audio7","OPCAO INVALIDA");
                encerra_com_tracking($canal, $ddr, $ticket, $indice, "PRINCIPAL","uraTopBank/audio7","OPCAO INVALIDA");
            }
        break;
    }
}

function M2_Cliente_Empresa($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    if(!tracking_canal_ativo($canal, $ddr, $ticket, $indice)) exit();

    //06
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'PERCURSO', '1 - CLIENTE EMPRESA OU 2 - DESEJA SE TORNAR');

    $opcao='';
    //$opcao=coletar_dados_usuario("uraTopBank/audio4",1);
    $opcao=coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio4",1);
    if($opcao == '-1'){hangup();break;}

    //07
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'RESPOSTA', $opcao);

    switch ($opcao){
        case 1:
            inicializa_ambiente_novo_menu();
            M2_1_Ja_Sou_Cliente_Empresa($uniqueid, $origem);
        break;

        case 2:
            inicializa_ambiente_novo_menu();
            M2_2_Desejo_me_tornar_cliente_empresa($uniqueid, $origem);
        break;

        default:
            if(retentar_dado_invalido("M1_1_Ja_e_Credenciada","uraTopBank/audio18","OPCAO INVALIDA")){
                //08
                $indice++;
                tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA', 'PERCURSO', 'OPCAO INVALIDA (1 - CLIENTE EMPRESA OU 2 - DESEJA SE TORNAR)');

                M2_Cliente_Empresa($uniqueid, $origem);
            }else{
                //09
                $indice++;
                tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA', 'PERCURSO', 'TENTATIVAS EXCEDIDAS (1 - CLIENTE EMPRESA OU 2 - DESEJA SE TORNAR)');

                //encerra_por_retentativas("M1_1_Ja_e_Credenciada","uraTopBank/audio7","OPCAO INVALIDA");
                encerra_com_tracking($canal, $ddr, $ticket, $indice, "M1_1_Ja_e_Credenciada","uraTopBank/audio7","OPCAO INVALIDA");
            }
        break;
    }
}

Function M2_1_Ja_Sou_Cliente_Empresa($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    if(!tracking_canal_ativo($canal, $ddr, $ticket, $indice)) exit();

    if(api_horario_atendimento()){
        verbose("API RETORNOU VALIDO HORARIO DE ATENDIMENTO");

        //11
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'PERCURSO', 'INFORMAR NUMERO DO CPF OU CNPJ');

        $cnpjcpf='';
        //$cnpjcpf = coletar_dados_usuario("uraTopBank/audio21",14);
        $cnpjcpf = coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio21",14);
        if($cnpjcpf == '-1'){hangup();break;}

        verbose("NUMERO DIGITADO PELO CLIENTE : ".$cnpjcpf);

        //12
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'CPF/ CNPJ', $cnpjcpf);

        $api02 = api_valida_cnpj_cpf($cnpjcpf, "CORPORATIVO");
        verbose("API VALIDOU CPF/CNPJ: ".$api02);
        if($api02){
            //13
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'RETORNO', 'CPF/ CNPJ VALIDO');

            verbose("GERANDO PROTOCOLO");
            $processId="WKF_Protocolo_Pai";
            $protocolo = protocolo_atendimento($cnpjcpf, $processId, $origem);

           //15
           $indice++;
           tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'PROTOCOLO', $protocolo);

            verbose("FALANDO NUMERO DE PROTOCOLO: ".$protocolo);
            playback("uraTopBank/audio9.1");
            falar_alfa($protocolo);
            playback("uraTopBank/audio9.2");
            falar_alfa($protocolo);     

            playback("uraTopBank/audio23");
            $fila= '303';

            //16
           $indice++;
           tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'PERCURSO', 'FILA QUE A LIGACAO FOI DIRECIONADA: '.$fila);

            tracking_canal_ativo($canal, $ddr, $ticket, $indice);
	        //dial_fast("gw01-kontac33/".$fila);
            dial_return("gw01-kontac33/".$fila);

            $indice++;
            $uniqueId_kontac= get_uniqueId_kontac($origem, '33');
            tracking($canal, $ddr, $ticket, $indice, 'PÓS ATENDIMENTO HUMANO', 'UNIQUEID KONTAC', $uniqueId_kontac);

            tracking_canal_ativo($canal, $ddr, $ticket, $indice);

            //17
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO - CLIENTE EMPRESA - JA SOU CLIENTE', 'PERCURSO', 'CLIENTE AINDA ESTA NA LINHA');

            pesquisa_satisfacao($uniqueid, $origem);
            
        }else{
            //13
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'RETORNO', 'CPF/ CNPJ INVALIDO');

            if(retentar_dado_invalido("M2_1_Ja_Sou_Cliente_Empresa","uraTopBank/audio15","API NAO VALIDOU O CNPJ/CPF"))M2_1_Ja_Sou_Cliente_Empresa($uniqueid, $origem);
            else{
                //14
                $indice++;
                tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - JA SOU CLIENTE', 'PERCURSO', 'TENTATIVAS EXCEDIDAS CPF/CNPJ INVALIDOS');

                //encerra_por_retentativas("M2_1_Ja_Sou_Cliente_Empresa","uraTopBank/audio7","OPCAO INVALIDA");
                encerra_com_tracking($canal, $ddr, $ticket, $indice, "M2_1_Ja_Sou_Cliente_Empresa","uraTopBank/audio7","OPCAO INVALIDA");
            }
            break;
        }
    }else{
        verbose("API RETORNOU HORARIO INVALIDO DE ATENDIMENTO");
        //10
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'FALAR COM ATENDENTE - CLIENTE EMPRESA - JA SOU CLIENTE', 'PERCURSO', 'OPCAO INVALIDA (1 - CLIENTE EMPRESA OU 2 - DESEJA SE TORNAR)');

        encerra_com_tracking($canal, $ddr, $ticket, $indice, "M2_1_Ja_Sou_Cliente_Empresa","uraTopBank/audio2","FORA HORARIO");
        //playback("uraTopBank/audio2");
        hangup();
        exit;
    }
}

function M2_2_Desejo_me_tornar_cliente_empresa($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    if(!tracking_canal_ativo($canal, $ddr, $ticket, $indice)) exit();

    //18
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'PERCURSO', 'INFORMAR NUMERO DO CPF OU CNPJ');

    $cnpjcpf='';
    //$cnpjcpf=coletar_dados_usuario("uraTopBank/audio16",14);
    $cnpjcpf=coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio16",14);
    if($cnpjcpf == '-1'){hangup();break;}

    //19
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'CPF/ CNPJ', $cnpjcpf);

    if(strlen($cnpjcpf) ==11){
        verbose("CLIENTE INFORMOU O CPF : ".$cnpjcpf);

        //34
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'PERCURSO', 'PERGUNTAR LGPD');

        $lgpd='';
        //$lgpd = coletar_dados_usuario("uraTopBank/audio36",1);
        $lgpd = coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio36",1);
        if($lgpd == '-1'){hangup();break;}

        if($lgpd =='1'){
            verbose("USUARIO CONCORDOU COM A LEI LGDP");
            //35
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'RESPOSTA', 'ACEITOU LGPD');

            inicializa_ambiente_novo_menu();
            desejo_ser_cliente_telefone($uniqueid, $origem, $cnpjcpf);
        }else{
            verbose("USUÁRIO DISCORDOU COM A LEI LGPD");

            //35
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'RESPOSTA', 'NAO ACEITOU LGPD');

            verbose("GERANDO PROTOCOLO");
            $processId="WKF_Prospect";
            $protocolo = protocolo_atendimento($cnpjcpf, $processId, $origem);
           
            verbose("PROTOCOLO GERADO PELA API: ".$protocolo,3);
            verbose("FALANDO O NÚMERO DE PROTOCOLO : ".$protocolo);
            playback("uraTopBank/audio35.1");
            falar_alfa($protocolo);
            verbose("REPETINDO NÚMERO DE PROTOCOLO");
            playback("uraTopBank/audio35.2");
            falar_alfa($protocolo);    
            playback("uraTopBank/audio35.3");
            hangup();
        }
    }elseif(strlen($cnpjcpf) ==14){
        verbose("CNPJ DIGITADO : ".$cnpjcpf);

        inicializa_ambiente_novo_menu();
        desejo_ser_cliente_telefone($uniqueid, $origem, $cnpjcpf);

    }else{
        if(retentar_dado_invalido("M1_2_Desejo_me_Credenciar","uraTopBank/audio18","CPF/CPNJ INVALIDO"))M2_2_Desejo_me_tornar_cliente_empresa($uniqueid, $origem);
        else{

            //encerra_por_retentativas("M1_2_Desejo_me_Credenciar","uraTopBank/audio7","CPF/CPNJ INVALIDO");
            encerra_com_tracking($canal, $ddr, $ticket, $indice, "M1_2_Desejo_me_Credenciar","uraTopBank/audio7","CPF/CPNJ INVALIDO");
        }
    }
}

function desejo_ser_cliente_telefone($uniqueid, $origem, $cnpjcpf){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    if(!tracking_canal_ativo($canal, $ddr, $ticket, $indice)) exit();

    //22
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'PERCURSO', 'INFORMAR NUMERO TELEFONE COM DDD');

    $fone='';
    //$fone=coletar_dados_usuario("uraTopBank/audio17",12);
    $fone=coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio17",15);
    if($fone == '-1'){hangup();break;}

    //23
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'CONTATO', $fone);
    verbose("NUMERO DIGITADO PELO CLIENTE: ".$fone);

    if(strlen($fone)< 8 || strlen($fone) > 12){
        //23
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'CONTATO', $fone);
        if(retentar_dado_invalido("M2_2_Desejo_me_Credenciar","uraTopBank/audio24","NUMERO INVALIDO")){
            //24
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'RETORNO', 'TELEFONE INVALIDO');

            inicializa_ambiente_novo_menu();
            desejo_ser_cliente_telefone($uniqueid, $origem, $cnpjcpf);
        }else{
            //25
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'PERCURSO', 'TENTATIVAS EXCEDIDAS (NUMERO TELEFONE)');

            //encerra_por_retentativas("M2_2_Desejo_me_Credenciar","uraTopBank/audio7","NÚMERO INVÁLIDO");
            encerra_com_tracking($canal, $ddr, $ticket, $indice, "M2_2_Desejo_me_Credenciar","uraTopBank/audio7","NÚMERO INVALIDO");
        }
    }else{
        verbose("NUMERO DIGITADO ATENDE AOS REQUISITOS");
        //24
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'RETORNO', 'TELEFONE VALIDO');

        $processId='WKF_Prospect';
        $protocolo = protocolo_atendimento($cnpjcpf, $processId, $fone);
        verbose("PROTOCOLO GERADO PELA API:".$protocolo,3);
        if(isset($protocolo)){
            playback("uraTopBank/audio19");

            //26
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'CLIENTE EMPRESA - DESEJO ME TORNAR', 'PROTOCOLO', $protocolo);

            verbose("FALANDO NUMERO DE PROTOCOLO: ".$protocolo);
            playback("uraTopBank/audio20.1");
            falar_alfa($protocolo);                        
            playback("uraTopBank/audio20.2");
            playback("uraTopBank/encerramento");
            hangup();
        }else{
            verbose("API NAO GEROU PROTOCOLO");
            hangup();
        }
    }
}

function M3_Usuario($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    if(!tracking_canal_ativo($canal, $ddr, $ticket, $indice)) exit();

    if(api_horario_atendimento()){
        verbose("API RETORNOU VALIDO HORARIO DE ATENDIMENTO");

        //28
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'USUARIO', 'PERCURSO', 'INFORMAR CPF DO USUARIO');

        $cnpjcpf='';
        //$cnpjcpf = coletar_dados_usuario("uraTopBank/audio5tmp",11);
        $cnpjcpf = coleta_dados2($canal, $ddr, $ticket, $indice, "uraTopBank/audio5tmp",11);
        if($cnpjcpf == '-1'){hangup();break;}

        verbose("DADOS DIGITADOS PELO USUÁRIO: ".$cnpjcpf);

        //29
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'USUARIO', 'CPF', $cnpjcpf);

        $retorno = api_valida_cadastro_cpf($cnpjcpf);
        verbose("O RETORNO FOI: ".$retorno->{'CLIENTE ATIVO'});

        if($retorno->{'CLIENTE ATIVO'} == "S"){
            //30
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'USUARIO', 'RETORNO', 'CPF VALIDO');

            $fila= '304';

            //32
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'USUARIO', 'PERCURSO', 'FILA: '.$fila);

            verbose("DIRECIONANDO CLIENTE PARA FILA ".$fila);
            playback("uraTopBank/audio23");

            tracking_canal_ativo($canal, $ddr, $ticket, $indice);
	        //inserirprotocolobanco($origem,$protocolo);
            //dial_fast("gw01-kontac33/".$fila);
            dial_return("gw01-kontac33/".$fila);

            $indice++;
            $uniqueId_kontac= get_uniqueId_kontac($origem, '33');
            tracking($canal, $ddr, $ticket, $indice, 'PÓS ATENDIMENTO HUMANO', 'UNIQUEID KONTAC', $uniqueId_kontac);

            tracking_canal_ativo($canal, $ddr, $ticket, $indice);

            //33
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO - USUARIO', 'PERCURSO', 'CLIENTE AINDA ESTA NA LINHA');

            inicializa_ambiente_novo_menu();
            pesquisa_satisfacao($uniqueid, $origem);
            hangup();
        } 
        else{
            //30
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'USUARIO', 'RETORNO', 'CPF INVALIDO');

            if(retentar_dado_invalido("M3_Usuario","uraTopBank/audio25","OPCAO INVALIDA"))M3_Usuario($uniqueid, $origem);
            else{
                //31
                $indice++;
                tracking($canal, $ddr, $ticket, $indice, 'USUARIO', 'PERCURSO', 'TENTATIVAS EXCEDIDAS CPF INVALIDO');

                //encerra_por_retentativas("M3_Usuario","uraTopBank/audio7","OPCAO INVALIDA");
                encerra_com_tracking($canal, $ddr, $ticket, $indice, "M3_Usuario","uraTopBank/audio7","OPCAO INVALIDA");
            }
            break;
        }
    }else{
        verbose("API RETORNOU HORARIO INVALIDO DE ATENDIMENTO");

        //27
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'FALAR COM ATENDENTE - USUARIO', 'PERCURSO', 'CLIENTE LIGOU FORA DO HORARIO DE ATENDIMENTO');
        
        encerra_com_tracking($canal, $ddr, $ticket, $indice, "M3_Usuario","uraTopBank/audio2","FORA DO HORARIO");
        //playback("uraTopBank/audio2");
        hangup();
        exit;
    }
}

function pesquisa_satisfacao($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    tracking_canal_ativo($canal, $ddr, $ticket, $indice);
    //09
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'LIGAÇÃO CONTINUADA');

    //10
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'ATENDIMENTO TRATOU A SOLICITAÇÃO?');

    $opcao='';
    //$opcao= coletar_dados_usuario("Fraseologia/PS01",1);
    $opcao= coleta_dados2($canal, $ddr, $ticket, $indice, "Fraseologia/PS01",1);
    if($opcao == '-1'){hangup();break;exit;}
    tracking_canal_ativo($canal, $ddr, $ticket, $indice);
    
    if($opcao=='1' || $opcao=='2'){
        if($opcao=='1')$resp= 'SIM';
        if($opcao=='2')$resp= 'NÃO';
        

        //11
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'RESPOSTA', $resp);

        inicializa_ambiente_novo_menu();
        pesquisa_satisfacao2($uniqueid, $origem);

    }else{
        playback("FroCli/03");
        if(retentar_dado_invalido("pesquisa_satisfação","Fraseologia/PS04","OPCAO INVALIDA")){
            //13
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'OPCAO INVALIDA');
            pesquisa_satisfacao($uniqueid, $origem);
        }else{
            //12
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'TENTATIVAS EXCEDIDAS SELECAO OPCAO');
            //encerra_por_retentativas("pesquisa_satisfação","Fraseologia/PS05","OPCAO INVALIDA");
            encerra_com_tracking($canal, $ddr, $ticket, $indice, "pesquisa_satisfação","Fraseologia/PS05","OPCAO INVALIDA");
        }         
    }

}

function pesquisa_satisfacao2($uniqueid, $origem){
    global $canal, $ddr, $ticket, $indice, $horaAtual;
    tracking_canal_ativo($canal, $ddr, $ticket, $indice);

    //14
    $indice++;
    tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'INFORMAR PERGUNTA(AVALIACAO 1 A 5)');

    $opcao='';
    //$opcao= coletar_dados_usuario("Fraseologia/PS02",1);
    $opcao= coleta_dados2($canal, $ddr, $ticket, $indice, "Fraseologia/PS02",1);
    tracking_canal_ativo($canal, $ddr, $ticket, $indice);
    if($opcao == '-1'){hangup();break;exit;}

    if($opcao>='1' && $opcao<='5'){
        //15
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'RESPOSTA', $opcao);

        playback("Fraseologia/PS03");

        //18
        $indice++;
        tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'FINALIZAÇÃO DA PESQUISA DE SATISFAÇÃO');
        hangup();

    }else{
        verbose("INFORMADO PELO CLIENTE : ".$opcao);
        playback("FroCli/03");
        if(retentar_dado_invalido("pesquisa_satisfacao2","Fraseologia/PS04","OPCAO INVALIDA")){
            //17
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'OPCAO INVALIDA');
            pesquisa_satisfacao2($uniqueid, $origem);
        }else{
            //16
            $indice++;
            tracking($canal, $ddr, $ticket, $indice, 'PESQUISA DE SATISFAÇÃO', 'PERCURSO', 'TENTATIVAS EXCEDIDAS SELECAO OPCAO');
            //encerra_por_retentativas("pesquisa_satisfacao2","Fraseologia/PS05","OPCAO INVALIDA");
            encerra_com_tracking($canal, $ddr, $ticket, $indice, "pesquisa_satisfacao2","Fraseologia/PS05","OPCAO INVALIDA");
        } 
    }
}

return 0;
hangup();
break;
exit();
?>