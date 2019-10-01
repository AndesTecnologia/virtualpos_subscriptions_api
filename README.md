

![enter image description here](https://s3-us-west-2.amazonaws.com/virtualpos/media/api_images/image4.png)
# API de Integración REST - Suscripciones

Esto es una guía para integrarse modelo de suscripciones de virtualpos.cl, para realizar esto, virtualpos disponibiliza una API REST con los métodos necesarios para generar una suscripción a través de la plataforma.

Como requisito, para poder hacer uso de las herramientas de virtualpos.cl a través de su api de integración, el usuario debe contar con lo siguiente.

 1. Ser usuario de virtualpos.cl, esto significa tener una cuenta válida y activa en la plataforma.
 2. Contar con una secret_key activa y válida con la cual poder realizar la integración.  
 3. Contar con una plataforma de software que implemente el estándar JWT (RFC 7519)

La API REST de suscripciones de virtualpos proporciona las siguientes operaciones

 1. Realizar una suscripción a un plan recurrente. 
 2. Consultar por el estado de una suscripción.

**Credenciales de la Cuenta VirtualPOS**

Para obtener la **Api Key** y **Secret Key** de su cuenta virtualpos.cl, debes ingresar a la siguiente sección.

> **www.virtualpos.cl -> Perfil ->Configuración de cuenta VirtualPOS ->    Integración, seleccionar API REST**

![enter image description here](https://s3-us-west-2.amazonaws.com/virtualpos/media/api_images/image5.png)

    Copiar los parámetros API KEY y SECRET KEY

![enter image description here](https://s3-us-west-2.amazonaws.com/virtualpos/media/api_images/image1.png)

**Acceso a la API**
SI tienes una cuenta en Virtualpos, puedes acceder al API REST mediante los siguientes endpoints:

|Ambiente|Base URL  |
|--|--|
|  **Producción**|https://api.virtualpos.cl/v1 |
|**Sandbox**|https://dev-api.virtualpos.cl/v1

El endpoint de **Producción** proporciona acceso directo para generar transacciones reales. El endpoint **Sandbox** permite probar su integración sin afectar los datos reales.

**I.Integración API REST.**
La integración de VirtualPOS se basa en la utilización de Json Web Token(JWT) como mecanismo de seguridad para autenticar las invocaciones a la API, el proceso resumido es el siguiente.

 1. Obtener una secret_key desde VirtualPOS, esta “clave compartida” solo debe ser conocida por VirtualPOS y por la parte que está realizando la integración.
 2. Obtener una api_key desde virtualpos, este es un identificador único de la cuenta que realizará la invocación de la api VirtualPOS.
 3. Invocar el servicio REST de virtualpos, usando la secret_key para firmar el mensaje(payload).
 4. Invocar el servicio REST de virtualpos, usando la secret_key para firmar el mensaje(payload).
Procesar el resultado de la invocación al servicio REST.

Tanto su ApiKey como su SecretKey se obtienen desde su cuenta de VirtualPos:

**Ambientes:**

| Producción | https://www.virtualpos.cl/admin/index.php?controller=pjAdmin&action=pjActionOwner&tab=8 |
|--|--|
| **Sandbox** | **https://dev.virtualpos.cl/admin/index.php?controller=pjAdmin&action=pjActionOwner&tab=8** |


**Tarjetas de pruebas Sandbox**
 
Para las transacciones de pruebas en estos ambientes se deben usar estas tarjetas:

VISA 4051885600446623, CVV 123, cualquier fecha de expiración. Esta tarjeta genera transacciones aprobadas.
MASTERCARD 5186059559590568, CVV 123, cualquier fecha de expiración. Esta tarjeta genera transacciones rechazadas.

Cuando aparece un formulario de autenticación con RUT y clave, se debe usar el RUT 11.111.111-1 y la clave 123.

**Firmando con la secret_key**

Para integrarse con VirtualPOS es necesario firmar el mensaje con la secret_key asociada a la cuenta VirtualPOS.

En el siguiente recuadro se ejemplifica, en lenguaje **php**, el código necesario para la firma de parámetros utilizando la secret key de su cuenta virtualpos.

En este ejemplo, el servicio requiere 2 parámetros(**api_key, uuid**), los cuales deben incluirse como parámetros en el **querystring**(GET) y además, una vez concatenados, deben ser firmados digitalmente con la secret_key utilizando el estándar JWT, una vez obtenida la firma, esta debe ser incluida en el **querystring** como un parámetro más(“s”).

Los recursos necesarios para la implementación los puedes encontrar en [https://jwt.io/](https://jwt.io/)

**Ejemplo de firma en PHP**

	$secret_key = TU_SECRET_KEY;  // TU SECRET KEY VIRTUALPOS
	$api_key = TU_API_KEY;  // TU API KEY VIRTUALPOS 

	$token_payload = array();    
	$token_payload['api_key'] = $api_key;
	$token_payload['uuid'] = $uuid;

	// FIRMA DE LOS PARAMETROS QUE SE DEBEN INCLUIR EN EL REQUEST HACIA VIRTUALPOS
	$jwt = JWT::encode($token_payload, base64_decode(strtr($secret_key, '-_', '+/')));

	$apiKey = "api_key=".$api_key;
	$uuid = "uuid=".$uuid;
		
	// FIRMA
	$s = "s=".$jwt;
		
	// URL HACIA VIRTUALPOS
	$url = "https://api.virtualpos.cl/v1/payment/getstatus?".$apiKey."&".$uuid."&".$s;

**Endpoints**

PRODUCCIÓN: https://api.virtualpos.cl
SANDBOX: https://dev-api.virtualpos.cl


**Suscribir a un plan de cobro recurrente.** 

Endpoint Producción: 

 1. https://api.virtualpos.cl/v1/subscriptions/subscribe: Inicia una suscripcion en virtualpos.cl ambiente de producción, retorna una url y un uuid para redireccionar el navegador su cliente.
 2. https://api.virtualpos.cl/v1/subscriptions/get: Retorna el estado de la suscripción, se debe invocar una vez que virtualpos.cl retorna el control a la pagina del su comercio. 
 
Para efectuar el pago de una transacción por medio de la API de VirtualPOS, es necesario seguir el siguiente procedimiento.
 
![enter image description here](https://s3-us-west-2.amazonaws.com/virtualpos/media/api_images/flujo-api-pat.png)


**1.- https://api.virtualpos.cl/v1/subscriptions/subscribe**: Operación que permite iniciar la suscripción a un plan con un PAT. El registro se realiza a través de una transacción de suscripción, en donde su aplicación debe entregar datos del usuario y plan. El usuario acepta el plan e inscribe una tarjeta de crédito para que se realice el cargo.

**Parámetros de entrada:**

|  Parámetro| Descripción|
|--|--|
| api_key |  Clave de la API proporcionada en back-office virtualPOS , Tipo: String(255)|
|email|Correo electrónico del cliente, Tipo: String (255)|
|social_id|Rut del cliente, Tipo: Rut válido sin puntos ni guion|
|first_name|Nombre del cliente, Tipo: String (255)|
|last_name|Apellido del cliente, Tipo: String (255)|
|phone_number|Número teléfonico del cliente, Tipo: String(11)|
|plan_id|Identificador del plan a suscribir, Tipo: String|
|return_url|URL a la cual se retornará una vez que se haya finalizado el proceso de pago en VirtualPOS, La URL debe ser codificada en Base64, Tipo: String (512)|
|callback_url(**opcional**)|URL a la cual se realizará un callback Asincrono una vez que se haya finalizado el proceso de inscripción en VirtualPOS, La URL debe ser codificada en Base64, Tipo: String (1024)|
|s|La firma de los parámetros efectuada con su secret_key|

**Parámetros de salida:**

| Parámetro | Descripción |
|--|--|
| response |  200|
|  message| Descripción de respuesta, ver tabla. |
| client | Cliente creado y asociado a la suscripción |
| email |  Correo electrónico del cliente creado y asociado la suscripción|
| first_name | Nombre del cliente creado y asociado a la suscripción |
| last_name |Apellido del cliente creado y asociado a la suscripción  |
|uuid|Código único que representa la transacción de suscripción a un plan con PAT virtualPOS. Se recomienda almacenar este token para posteriormente consultar el resultado del registro.|
|url_redirect|URL de virtualPOS  a la cual debe ser redirigido el usuario para inscribir su tarjeta de crédito en forma segura. A esta URL se debe enviar el token vía POST.|

**Códigos de respuesta:**

| Código |  Descripción|
|--|--|
|200|Transacción de suscripción aceptada, se devuelven datos para continuar el proceso de suscripción. |
|401|Transacción de suscripción  rechazada, cuenta virtualPOS sin contrato PAT activo.|
|402|Transacción de suscripción rechazada, cuenta virtualPOS con error en configuración PAT |
|405|Parámetro email no cumple con formato e-mail.|
|406|Parámetro return_url no cumple con formato de url.|
|500|Error no existe cuenta virtualPOS asociada a api_ke|
|501|Firma incorrecta|
|504|No fue posible crear el cliente en virtualPOS.|
|510|Error en parámetro api_key|
|511|Error en parámetro email|
|512|Error en parámetro social_id|
|513|Error en parámetro first_name|
|514|Error en parámetro last_name|
|515|Error en parámetro return_url|
|516|Error en parámetro signature|

**Ejemplo:** 

require( dirname(__FILE__) . '/jwt/vendor/autoload.php' );
    use \Firebase\JWT\JWT;
    

    $api_key = TU_API_KEY;
    $secret_key = TU_SECRET_KEY;

    $email = "JohnDoe@gmail.com";
    $social_id = "176290986";
    $first_name = "John";
    $last_name = "Doe";
    $phone_number = "56912345678";
    $plan_id = "f93670c4cb6dfbca5db1d45a2ee61278";
    $return_url =  base64_encode("http://localhost:8888/client_api/responsePAT.php");

    $token_payload = array();
        
    $token_payload['api_key'] = $api_key;
    $token_payload['email'] = $email;
    $token_payload['social_id'] = $social_id;
    $token_payload['first_name'] = $first_name;
    $token_payload['last_name'] = $last_name;
    $token_payload['return_url'] = $return_url;
    $token_payload['plan_id'] = $plan_id;
    
    
    $jwt = JWT::encode($token_payload, base64_decode(strtr($secret_key, '-_', '+/')));
    
    
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
    
    $url = "http://dev-api.virtualpos.cl/v1/subscriptions/suscribe/&".$apiKey."&".$email."&".$social_id."&".$first_name."&".$last_name."&".$return_url."&".$phone_number."&".$callback_url."&".$plan_id."&".$s;
    
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
    error_log("redirect : ".$redirect);
    header("Status: 301 Moved Permanently");
    header("Location: " . $redirect);
    exit;



**2.-https://api.virtualpos.cl/v1/subscriptions/subscribe/get/:** Operación que permite consultar el estado de una suscripción a plan con PAT VirtualPOS.

**Parámetros de entrada:**

| Parámetro |  Descripción|
|--|--|
| api_key | código único asociado a la cuenta que se está integrando a VirtualPOS a través de la API, Tipo: String |
|uuid|Código único que representa la transacción de suscripción a un plan con PAT virtualPOS. Se recomienda almacenar este uuid para posteriormente consultar el resultado del registro, Tipo: String (255)|
|s|La firma de los parámetros efectuada con su secret_key|

**Parámetros de salida:**


| Parámetro | Descripción |
|--|--|
| response |  Código de respuesta del mensaje, 200 indica que se procesó correctamente la suscripción al plan. Ver tabla de códigos de respuesta del servicio|
|  message| Descripción de respuesta, ver tabla. |
| client | Cliente creado y asociado a la suscripción |
| email |  Correo electrónico del cliente creado y asociado a la suscripción|
| first_name | Nombre del cliente creado y asociado a la suscripcion |
| last_name |Apellido del cliente creado y asociado a la suscripción  |
| plan_id | Identificador del plan  |
|status|Estado de suscripción: existen los siguientes 3 estados: **SUSCRIBIENDO, ACTIVA, CANCELADA**|
|suscription_date|Fecha de creación de la suscripción **Formato: yyyy-mm-dd hh:mm:ss**|
|last4CardDigit|Últimos 4 dígitos de la tarjeta registrada.|
|creditCardType|Marca de la tarjeta registrada (Visa, Mastercard, Magna, Amex…).|

**Códigos de respuesta:**

| Código | Descripción |
|--|--|
| 200 | Transacción de suscripción aceptada, se devuelven datos para continuar el proceso de suscripción. |
|401|Inscripción rechazada por banco emisor.|
|402|En proceso de inscripción|
|403|Inscripción cancelada por usuario.|
|404|Inscripción expirada.|
|411|No existe un usuario asociado al uuid proporcionado.|
|500|No existe cuenta virtualPOS asociada a api_key|
|501|Firma incorrecta|
|510|Error en parámetro api_key|
|511|Error en parámetro  uuid|
|512|Error en parámetro s|

**Ejemplo:** 

PHP:

    require( dirname(__FILE__) . '/jwt/vendor/autoload.php' );
    use \Firebase\JWT\JWT;

    $uuid = $_POST['uuid'];	

    $api_key = TU_API_KEY;
    $secret_key = TU_SECRET_KEY;
      

    $token_payload = array();
        
    $token_payload['api_key'] = $api_key;
    $token_payload['uuid'] = $uuid;

    $jwt = JWT::encode($token_payload, base64_decode(strtr($secret_key, '-_', '+/')));

    $apiKey = "api_key=".$api_key;
    $uuid = "uuid=".$uuid;

    $s = "s=".$jwt;

    $url = "https://dev-api.virtualpos.cl/v1/subscriptions/suscribe/get/&".$apiKey."&".$uuid."&".$s;
    
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
    
    
**2.-https://api.virtualpos.cl/v1/subscriptions/plan/create:** Operación que permite crear un plan de cobro recurrente, el cual quedará en estado activo.

**Parámetros de entrada:**

| Parámetro |  Descripción|
|--|--|
| api_key | código único asociado a la cuenta que se está integrando a VirtualPOS a través de la API, Tipo: String |
|name|Nombre del plan recurrente, Tipo: String (255) - debe ser urlencode|
|description|Descripción del plan recurrente, Tipo: String (255) - debe ser urlencode|
|amount|Monto Tipo: String , sin separador ni comas.|
|currency|Moneda, Tipo: String, Valores : CLP, UF|
|tax|Impuesto aplicado al cobro, Tipo: String (2), Valores: 0, 10,19|
|trial_days|días de prueba, el primer cobro se realizará una vez transcurridos este indicador, Tipo: int|
|num_charges|número de cargos,Tipo: int|
|frequency_type|Frecuencia del cargo, Tipo: String, Valores: Diario, Semanal, Mensual|
|return_url|URL de su aplicación a la cual se retornará una vez que se haya finalizado la suscripción. En esta URL se deberá ejecutar la consulta del resultado de suscripción, ya que por motivos de seguridad la respuesta no se entrega en forma directa a la URL de retorno. La URL debe ser codificada en Base64 Tipo: String (255)|
|s|La firma de los parámetros efectuada con su secret_key|

**Parámetros de salida:**


| Parámetro | Descripción |
|--|--|
| response |  Código de respuesta del mensaje, 200 indica que se procesó correctamente la suscripción al plan. Ver tabla de códigos de respuesta del servicio.|
|  message| Descripción de respuesta, ver tabla. |
| plan_id | Identificador único del plan creado. |


**Códigos de respuesta:**

| Código | Descripción |
|--|--|
| 200 | Plan creado. |
|401|Creación de Plan rechazado, cuenta virtualpos sin contrato PAT activo|
|500|No existe cuenta virtualPOS asociada a api_key|
|501|Firma incorrecta|
|510|Error en parámetro api_key|
|511|Error en el parametro name|
|512|Error en el parametro description|
|513|Error en el parametro amount|
|514|Error en el parametro currency|
|515|Error en el parametro tax|
|516|Error en el parametro trial_days|
|517|Error en el parametro num_charges|
|518|Error en el parametro frequency_type|
|519|Error en el parametro return_url|
|520|Error en el parametro s|

**Ejemplo:** 

PHP:

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
    
    $jwt = JWT::encode($token_payload, base64_decode(strtr($secret_key, '-_', '+/')));
    
    
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
    
    $url = "https://dev-api.virtualpos.cl/v1/subscriptions/plan/create?".$apiKey."&".$name."&".$description."&".$amount."&".$currency."&".$tax."&".$trial_days."&".$num_charges."&".$frequency_type."&".$return_url."&".$s;
       
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

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);
	$request =  json_decode($result, TRUE);
	echo print_r($request,TRUE);
