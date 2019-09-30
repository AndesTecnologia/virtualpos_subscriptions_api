

![enter image description here](https://s3-us-west-2.amazonaws.com/virtualpos/media/api_images/image4.png)
# API de Integración REST - Suscripciones

Esto es una guía para integrarse con virtualpos.cl - Suscripciones, para realizar esto, virtualpos disponibiliza una API REST con los métodos necesarios para generar una suscripción a través de la plataforma.

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

| Producción | https://www.virtualpos.cl/admin/index.php?controller=pjAdmin&action=pjActionOwner#integración |
|--|--|
| **Sandbox** | **https://dev.virtualpos.cl/admin/index.php?controller=pjAdmin&action=pjActionOwner#integración** |


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
| client | Cliente creado y asociado a la solicitud de pago |
| email |  Correo electrónico del cliente creado y asociado a la solicitud de pago|
| first_name | Nombre del cliente creado y asociado a la solicitud de pago |
| last_name |Apellido del cliente creado y asociado a la solicitud de pago  |
|uuid|Código único que representa la transacción de suscripción a un plan con PAT virtualPOS. Se recomienda almacenar este token para posteriormente consultar el resultado del registro.|
|url_redirect|URL de virtualPOS  a la cual debe ser redirigido el usuario para inscribir su tarjeta de crédito en forma segura. A esta URL se debe enviar el token vía POST.|

**Ejemplo:** 

{"status":"OK","code":200,"cliente":{"email":"johndoe@gmail.com","first_name":"John","last_name":"Doe"},"order":{"uuid":"34125c784ee5e520","status":"pendiente","created":"2019-02-28 18:13:09"},"url_redirect":"https:\/\/www.virtualpos.cl\/admin\/index.php?controller=apiPublic&action=pjActionDoPay"}


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

**2.-https://api.virtualpos.cl/v1/subscriptions/subscribe/get/:** Operación que permite consultar el estado de una suscripción a plan con PAT VirtualPOS..

**Parámetros de entrada:**

| Parámetro |  Descripción|
|--|--|
| api_key | código único asociado a la cuenta que se está integrando a VirtualPOS a través de la API, Tipo: String |
|uuid|identificador único de la transacción en virtualpos, Tipo: String (255)|
|s|La firma de los parámetros efectuada con su secret_key|

**Parámetros de salida:**


| Parámetro | Descripción |
|--|--|
| response |  200|
|  message| ok |
| cliente | Cliente creado y asociado a la solicitud de pago |
| email |  Correo electrónico del cliente creado y asociado a la solicitud de pago|
| first_name | Nombre del cliente creado y asociado a la solicitud de pago |
| last_name |Apellido del cliente creado y asociado a la solicitud de pago  |
| order | Solicitud de pago creada para esta solicitud |
|uuid|Código único que representa la solicitud de pago de una transacción. Se recomienda almacenar este token para posteriormente consultar el resultado del registro.|
|status|Estado de la solicitud de pago: existen los siguientes 2 estados: **pendiente, pagado**|
|created|Fecha de creación de la solicitud de pago, **Formato: yyyy-mm-dd hh:mm:ss**|
|buyOrder|Orden de compra de la solicitud.|



**Ejemplo:** 

{"status":"OK","code":200,"cliente":{"email":"johndoe@gmail.com","first_name":"John","last_name":"Doe","social_id":"11111111"},"order":{"uuid":"34125c795429b3a8","status":"pagado","created":"2019-03-01 12:47:53","buyOrder":"OCx000065x0655c2f63a81c9fc"}}

**Códigos de respuesta:**

| Código | Descripción |
|--|--|
| 200 | Solicitud de pago existente, se devuelven datos del estado actual de la solicitud de pago |
|401|Ocurrió un problema al recuperar el registro.|
