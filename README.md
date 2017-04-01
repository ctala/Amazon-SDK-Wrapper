# Amazon-SDK-Wrapper
Wrapper para el SDK de Amazon V3 para PHP

* Utiliza las variables de entorno por defecto.


## Variables de entorno por defecto.
Con respecto a las llaves de amazon se sobrescriben en caso de ser necesario.
Ejemplo; si creas un objeto SES AMAZON_KEY_SES reemplazará el valor de AMAZON_KEY en caso de existir. 

* AMAZON_REGION, default us-west-2
* AMAZON_VERSION, default latest
* AMAZON_KEY
* AMAZON_SECRET
* AMAZON_KEY_SES
* AMAZON_SECRET_SES
* AMAZON_KEY_SQS
* AMAZON_SECRET_SQS

Para crear uno de los objetos sin las variables de entorno se deben de especificar al menos las credenciales.

```php
$credentials = array(
    'key' => "MyKEY",
    'secret' => "MySecret",
);
$sesMail = new \ctala\AWS\SES($credentials);
```


# Simple Queue Service

En este caso la queue es creada en caso de no existir.

```php
        $sqs_client = new \ctala\AWS\SQS();
        $sqs_client->setQueueName('NOMBRE QUEUE');        
        $message = array(
            "idServicio" => $idServicio,
            "idOwner" => $idOwner,
            "idTrx" => $idTrx
        );
        try {
            $sqs_client->sendMessage($message);
        } catch (Exception $ex) {
            error_log("No se pudo enviar mensaje");
            error_log($ex->getTraceAsString());
        }
```
 


# Simple Email Service
Se pueden especificar los datos de manera directa en caso de no tener caracteres especiales.
En caso de que el subject y el sender los tengan se deben ocupar las funciones.

## Ejemplo de envío
```php

$sesMail = new \ctala\AWS\SES();
$sender = "Cristián Tala";
$subject = "Atención 3";


$sesMail->setSender("MIDIRECCIONDEMAIL", $sender);
$sesMail->setSubject($subject);

$sesMail->body = "HOLA y á";
$sesMail->toAddress = "MAIL";

$sesMail->sendMail();


```
