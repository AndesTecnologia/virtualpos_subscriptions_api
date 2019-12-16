<?php
    
    require( dirname(__FILE__) . '/jwt/vendor/autoload.php' );
    use \Firebase\JWT\JWT;


    $api_key = MI_API_KEY;
    $secret_key = MI_SECRET_KEY;
      
    $email = "johndoe@email.com";
    $social_id = "111111111";
    $first_name = "john";
    $last_name = "doe";
    $phone_number = "56912345678";

    $plan_id = PLAN_ID_A_SUSCRIBIR;
     
    $return_url =  base64_encode("http://localhost:8888/client_api/response.php");
    $callback_url =  base64_encode("https://www.virtualpos.cl");

    $token_payload = array();
        
    $token_payload['api_key'] = $api_key;
    $token_payload['email'] = $email;
    $token_payload['social_id'] = $social_id;
    $token_payload['first_name'] = $first_name;
    $token_payload['last_name'] = $last_name;
    $token_payload['return_url'] = $return_url;
    $token_payload['plan_id'] = $plan_id;
    
    
    $jwt = JWT::encode($token_payload, $secret_key, '-_', '+/');
    
    
    $apiKey = "api_key=".$api_key;
    $email = "email=".$email;
    $social_id = "social_id=".$social_id;
    $first_name = "first_name=".$first_name;
    $last_name = "last_name=".$last_name;
    $return_url = "return_url=".$return_url;
    $phone_number = "phone_number=".$phone_number;
    $callback_url = "callback_url=".$callback_url;
    $plan_id = "plan_id=".$plan_id;

    $s = "s=".$jwt;
    
    $url = "https://dev-api.virtualpos.cl/v1/subscriptions/subscribe?".$apiKey."&".$email."&".$social_id."&".$first_name."&".$last_name."&".$return_url."&".$phone_number."&".$callback_url."&".$plan_id."&".$s;
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
    
    
    $redirect = $request['redirect_url'] . '&' . "uuid=". $request['uuid'];
    header("Status: 301 Moved Permanently");
    header("Location: " . $redirect);
    exit;
    
    
?>
