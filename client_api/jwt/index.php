<?php
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

$secret_key = $_POST['secret_key'];
$api_key = $_POST["api_key"];
$email = $_POST["email"];
$token = $_POST["token"];

//$api_key = "1234-5678-90AB-12CD";
//$token = md5(uniqid(rand(), true));

$token_payload = [
  'api_key' => $api_key,
  'token' => $token,
  'email' => $email
];

$jwt = JWT::encode($token_payload, base64_decode(strtr($secret_key, '-_', '+/')), 'HS256');

$apiKey = "apiKey=".$api_key;
$token = "token=".$token;
$s = "s=".$jwt;
?>


<html>
<head></head>
<body>

<form id="tokenForm" method="post" action="">
    Email : <input type="text" name="email" value="<?php echo !isset($email)?"rlepe@gmail.com":$_POST["email"]; ?>" /><br/>
    Api_key :<input type="text" name="api_key" value="<?php echo !isset($api_key)?"r1234-5678-90AB-12CD":$_POST["api_key"]; ?>" /><br/>
    secret_key : <input type="text" name="secret_key" value="<?php echo !isset($secret_key)?"virtualpos_AB1234567890":$_POST["secret_key"]; ?>" /><br/>
    token: <input type="text" name="token" value="<?php echo !isset($secret_key)?md5(uniqid(rand(), true)):$_POST["token"];   ?>" /><br/><br/>
	<input type='submit' value='submit'>
</form>

<a href="<?php echo "http://localhost:8888/jwt/process.php?".$apiKey."&".$token."&".$s;     ?>">ir al api segura</a>
    

</body>
</html>
