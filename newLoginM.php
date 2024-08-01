<?php

include './conexion.php';
date_default_timezone_set("America/Santiago");

$time = date('Y-m-d H:i:s');

$cuentas = [
    [
        'cuenta' => 'monitoreo_araucaniasur@masgps.la', //Araucania
        'password' => 'Monitoreo_Araucania'
    ],
    [
        'cuenta' => 'mineraventanas@masgps.com', //Ventana
        'password' => 'Ventanas2023'
    ],
    [
        'cuenta' => 'monitoreogps_lascondes@wit.la', //Camara
        'password' => 'MonitoreoLasCondes'
    ],
    [
        'cuenta' => 'mineraescondida@masgps.com', //Mel
        'password' => 'MEL_2023'
    ],
    [
        'cuenta' => 'mineracentinela@masgps.com', //Centinela
        'password' => 'Centinela_2024'
    ],
    [
        'cuenta' => 'bi_pullman@masgps.com', //Tandem
        'password' => 'BI_2024'
    ],
    [
        'cuenta' => 'proyecto_integral@masgps.com', //Integral
        'password' => 'Integral_2024'
    ],
    [
        'cuenta' => 'monitoreogps@pullman.cl', //Pullman
        'password' => '2024_MonitoreoGPS'
    ],
    [
        'cuenta' => 'monitoreo@ingegroup.cl', //Ingegroup
        'password' => 'Monitoreo2023.'
    ],
    [
        'cuenta' => 'Monitoreo_MVDM@masgps.cl', //Vina
        'password' => 'Monitoreo2023.'
    ],
    [
        'cuenta' => 'particulares@masgps.cl', //Particulares
        'password' => 'Particulares2023'
    ],
    [
        'cuenta' => 'ti.contador@masgps.com', //Las condes
        'password' => 'Contador24'
    ],
    [
        'cuenta' => 'monitoreo_araucaniasur@masgps.la', //Araucania
        'password' => 'Monitoreo_Araucania'
    ],
    [
        'cuenta' => 'monitoreomreina@masgps.cl', //laReina
        'password' => 'Monitoreo2024.'
    ]

    
];

$multiCurl = [];
$mh = curl_multi_init();

foreach ($cuentas as $key => $cuenta) {
    $user = $cuenta['cuenta'];
    $pasw = $cuenta['password'];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/user/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            "login" => $user,
            "password" => $pasw,
            "dealer_id" => 10004282,
            "locale" => "es",
            "hash" => null
        ]),
        CURLOPT_HTTPHEADER => [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: es-419,es;q=0.9,en;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Cookie: _ga=GA1.2.728367267.1665672802; locale=es; _gid=GA1.2.967319985.1673009696; _gat=1',
            'Origin: http://www.trackermasgps.com',
            'Referer: http://www.trackermasgps.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36'
        ],
    ]);

    $multiCurl[$key] = $curl;
    curl_multi_add_handle($mh, $curl);
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

foreach ($multiCurl as $key => $curl) {
    $response = curl_multi_getcontent($curl);
    $json = json_decode($response);
    $cap = $json->hash;
    echo $cap . "<br>";

    $user = $cuentas[$key]['cuenta'];

    $qry = "UPDATE `masgps`.`hash2` SET `timestamp` = '$time', `hash` = '$cap' WHERE `user` = '$user'";
    $resultado = mysqli_query($mysqli, $qry);

    curl_multi_remove_handle($mh, $curl);
    curl_close($curl);
}

curl_multi_close($mh);

?>
