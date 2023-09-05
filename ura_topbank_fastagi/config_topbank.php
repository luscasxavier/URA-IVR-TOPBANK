<?php

return array(
    //'user_login' => $login,
    'user_login' => $login,
    //'pass_login' => $password,
    'pass_login' => $password,
    //'server_api' => $server,
    'server_api' => $server,
    'url_login' => $url_login,
    'siglaUra' => 'TOPBANK',//nome da ura para aparece nos logs e mensagens
    'copiar_audios_para_teste' => 'true', //usar essa opcao somente para testar sem os audio pois ira copiar o audioteste.wav para os audios faltantes. 
    'extensao_audio' =>'.wav',//extensao do formato padrao de audios
    'audio_lib' => '/var/lib/asterisk/sounds/',//pasta padrao de audios
    'audio_ura' => 'uraTopBank/', //pasta dos audios especificos da ura
    'max_timeout' => '6000', //tempo maximo de timeout ao esperar entrada do usuario em milisegundos
    'max_tentativas' => '3', //numero maxixo de retentavivas de input do usuario
    //TESTE PARA ACHAR OU NÃO O CNPJ/CPF
    'identificador' => '78734093000175', //CNPJ ATIVO
    //'identificador' => '47146938000188', //CNPJ INATIVO
    //'identificador' => '12648303000102', //NÃO ACHA CNPJ
    //'identificador' => '49518743002', //CPF ATIVO
    //'identificador' => '86573800052', //CPF INATIVO
    //'identificador' => '86573800052', //CPF API 06
    'atendimentoHumano' => 'S',
    //'atendimentoHumano' => 'N',
    //'senha' => '333333', //VALIDA
    'senha' => '333332', //INVALIDA
    'nova_senha' => '33333',
    'entidade_cli' => 'CLI',
    'entidade_usu' => 'USU',
    'entidade_conv' => 'CONV',
    'mensagem' => 'ENTRAR EM CONTATO PARA PROSPECT TOPBANK',
    'fone' => '3434656798',
    'token_topbank' => 'GciO1NiJ9.deyJhbGciOiJIUciOiJIUzI1',
     //URLS
    'url_api_horario' => 'ura-vale-api/services/uratopbank/api01horarioAtendimentoTopBank/',
    'url_api_valida_cnpj' => 'ura-vale-api/services/uratopbank/api02validaCnpjCpfTopBank',
    'url_api_prospect_easy_emissor' => 'ura-vale-api/services/uratopbank/api03ProspectEasyTopBank',
    'url_api_valida_cadastro_cpf_topbank' =>'ura-vale-api/services/uratopbank/api04validaCadastroCpfTopBankUsuario',
    'url_api_verifica_Ec_varejo_topbank_varejo' =>'ura-vale-api/services/uratopbank/api05VerificarEcVarejoTopBankUsuario',
    'url_api_prospect_easy_varejo' => 'ura-vale-api/services/uratopbank/api06ProspectEasyTopBankVarejo',
    'url_api_valida_senha_topbank_empresa' => 'ura-vale-api/services/uratopbank/api07ValidaSenhaTopBankEmpresa',
    'url_altera_senha_topbank' => 'ura-vale-api/services/uratopbank/api08AlteracaoSenhaTopBank',
    'url_retorna_saldo_topbank' => 'ura-vale-api/services/uratopbank/api09RetornoSaldoTopBank',
    'url_api_protocolo_atendimento' => 'ura-vale-api/services/uratopbank/api10ProtocoloAtendimento'
);
