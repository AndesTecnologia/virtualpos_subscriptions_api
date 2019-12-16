<?php
    
    require( dirname(__FILE__) . '/jwt/vendor/autoload.php' );
    use \Firebase\JWT\JWT;
    
    $api_key = TU_API_KEY;
    $secret_key = TU_SECRET_KEY;
      
    $name = urlencode("Plan de cobro diario");
    $description = urlencode("Plan de cobro diario");
    $amount = "10";
    $currency = "CLP";
    $tax = "0";
    $trial_days = "0";
    $num_charges = "30";
    $frequency_type = "Diario";

    $return_url =  base64_encode("https://www.google.cl");

    $token_payload = array();
        
    $token_payload['api_key'] = $api_key;
    $token_payload['name'] = $name;
    $token_payload['description'] = $description;
    $token_payload['amount'] = $amount;
    $token_payload['currency'] = $currency;
    $token_payload['tax'] = $tax;
    $token_payload['trial_days'] = $trial_days;
    $token_payload['num_charges'] = $num_charges;
    $token_payload['frequency_type'] = $frequency_type;
    $token_payload['return_url'] = $return_url;
    
    $jwt = JWT::encode($token_payload, $secret_key, '-_', '+/');
    
    
    $apiKey = "api_key=".$api_key;
    $name = "name=".$name;
    $description = "description=".$description;
    $amount = "amount=".$amount;
    $currency = "currency=".$currency;
    $tax = "tax=".$tax;
    $trial_days = "trial_days=".$trial_days;
    $num_charges = "num_charges=".$num_charges;
    $frequency_type = "frequency_type=".$frequency_type;
    $return_url = "return_url=".$return_url;
    
    $s = "s=".$jwt;
    
//  localhost
    $url = "https://dev-api.virtualpos.cl/v1/subscriptions/plan/create?".$apiKey."&".$name."&".$description."&".$amount."&".$currency."&".$tax."&".$trial_days."&".$num_charges."&".$frequency_type."&".$return_url."&".$s;
    error_log($url);
    
    
    
    
    
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Authority: dev.virtualpos.cl';
$headers[] = 'Cache-Control: max-age=0';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36';
$headers[] = 'Sec-Fetch-Mode: navigate';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
$headers[] = 'Sec-Fetch-Site: none';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Accept-Language: es-ES,es;q=0.9,en;q=0.8,und;q=0.7,la;q=0.6,gl;q=0.5';
//$headers[] = 'Cookie: _ga=GA1.2.705290129.1525361644; messagesUtk=9c09443a230f47a2b0aa975409bebd04; mp_a36067b00a263cce0299cfd960e26ecf_mixpanel=%7B%22distinct_id%22%3A%20%2216aea6c784c560-0031993619d456-37657e03-240000-16aea6c784dcee%22%2C%22%24device_id%22%3A%20%2216aea6c784c560-0031993619d456-37657e03-240000-16aea6c784dcee%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Fwww.virtualpos.cl%2Fwp-admin%2Fupdate.php%3Faction%3Dupload-plugin%22%2C%22%24initial_referring_domain%22%3A%20%22www.virtualpos.cl%22%7D; virtualPOS=apgnep74hdbcolku0qc31om6i7; messagesUtk=9c09443a230f47a2b0aa975409bebd04; __hssrc=1; hubspotutk=a90db42e22efae7f95ed8798a2967685; __hssrc=1; hubspotutk=a90db42e22efae7f95ed8798a2967685; _gid=GA1.2.1809262828.1569844337; __hstc=189466292.a90db42e22efae7f95ed8798a2967685.1525782724403.1567724261371.1569856268710.182; __hstc=189466292.a90db42e22efae7f95ed8798a2967685.1525782724403.1569606961539.1569938526346.183; __hssc=189466292.1.1569938526346';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

error_log("result 1 : ".print_r($result,true));
$request =  json_decode($result, TRUE);

echo print_r($request,TRUE);
   
    
?>
