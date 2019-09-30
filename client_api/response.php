<?php
    
    $uuid = $_POST['uuid'];
    require( dirname(__FILE__) . '/jwt/vendor/autoload.php' );
    use \Firebase\JWT\JWT;

    $api_key = MI_API_KEY;
    $secret_key = MI_SECRET_KEY;    

    $token_payload = array();
        
    $token_payload['api_key'] = $api_key;
    $token_payload['uuid'] = $uuid;

    $jwt = JWT::encode($token_payload, base64_decode(strtr($secret_key, '-_', '+/')));

    $apiKey = "api_key=".$api_key;
    $uuid = "uuid=".$uuid;

    $s = "s=".$jwt;
    $url = "https://dev-api.virtualpos.cl/v1/subscriptions/get?".$apiKey."&".$uuid."&".$s;    
    
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

    $headers = array();
    $headers[] = "Connection: keep-alive";
    $headers[] = "Pragma: no-cache";
    $headers[] = "Cache-Control: no-cache";
    $headers[] = "Upgrade-Insecure-Requests: 1";
    $headers[] = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36";
    $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
    $headers[] = "Accept-Encoding: gzip, deflate, br";
    $headers[] = "Accept-Language: es-ES,es;q=0.9,en;q=0.8,und;q=0.7,la;q=0.6";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
    
    $request =  json_decode($result, TRUE);
    
    echo print_r($request, TRUE);
 
?>
