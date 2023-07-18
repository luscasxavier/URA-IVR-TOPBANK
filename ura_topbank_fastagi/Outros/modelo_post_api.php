<?php
$usuario='ura.telek';
$senha='22222';

echo token($usuario,$senha);

function token($usuario,$senha)
{
$url = 'http://10.1.0.43:8080/ura-vale-api/auth/login';
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
?>