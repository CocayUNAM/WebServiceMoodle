# WebServiceMoodle
Web Service Moodle-Aplicación
## Instrucciones WS de certificado (válido para wscertificados.php)
1. Colocar el archivo wscertificado.php y webservice.ini en la carpeta de simplecertificate de moodle (Ej. /opt/lampp/htdocs/moodle3.5/mod/simplecertificate).
1. Abrir el archivo webservice.ini y modificar los parametros de nombre de base de datos, usuario, contraseña, host y clave, esta ultima debe ser coincidente con la clave del SICECD.
1. Antes de probar, modificar el script SQL del SICECD, donde se hace una inserción de usuario luego de la creación de la tabla Certificado sustituir el nombre del curso por el nombre del curso del curso que en el laboratorio Moodle genera un certificado.
1. Correr el script SQL del SICECD antes de hacer alguna manipulación de datos en el SICECD.
1. Modificar las variables del archivo application-cert.properties, donde LOCAL guarda la ruta donde se guardarán los certificados y TEMP_ZIP que guarda de forma temporal un ZIP que contiene certificados, todo esto en el SICECD.
1. Además modificar la expresion cron en CertificadoMasivoController.java para probar, está función se ejecutará en el tiempo que se calendarice.
1. Se tiene que insertar en la base de datos de Moodle en la tabla Url_ws, entradas, una de ellas debe contener lo siguiente : (< url que procesa 1 solo certificado>,false,true), la otra : (< url que procesa más de un certificado >,true,true).
1. Correr el SICECD, para probar la traída de un certificado simple ir a la url localhost:8080/certificado, ésta url es accesible por cualquier persona.

