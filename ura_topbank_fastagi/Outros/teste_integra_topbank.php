
<?php

require_once 'apis_topbank.php';

$opcao = $argv[1];




switch ($opcao) {
    case 1:
        echo api_login_token();
        break;

    case 2:
        echo api_horario_atendimento();
        break;

    case 3:
        echo api_valida_cnpj_cpf($argv[2], $argv[3])."\n";
        break;

    case 4:
        echo api_abre_solicitacao_easy_emissor($argv[2], $argv[3])."\n";
        break;

    case 5:
        echo api_valida_cadastro_cpf($argv[2]);
        break;

    case 6:
        
       echo api_valida_cnpjCpf_core_topbank($argv[2])."\n";
        break;
    
    case 7:
        echo api_abre_solicitacao_easy_varejo($argv[2], $argv[3])."\n";
        break;
    
    case 8:
        echo api_valida_senha_topbank($argv[2], $argv[3])."\n";
        break;

    case 9:
        echo api_altera_senha_topbank($argv[2], $argv[3])."\n";
        break;

    case 10:
        api_retorna_saldo_topbank($argv[2])."\n";
        break;

    case 11:
        echo protocolo_atendimento($argv[2], $argv[3], $argv[4])."\n";
        break;


    default:
        echo 'OPÇÃO INVALIDA';
            break;
}


?>