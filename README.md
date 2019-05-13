# WebServiceMoodle
Web Service Moodle-Aplicación
## Instrucciones WS de certificado (válido para wscertificados.php)
1. Colocar el archivo wscertificado.php en la carpeta de simplecertificate de moodle (Ej. /opt/lampp/htdocs/moodle3.5/mod/simplecertificate).
1. Abrir el archivo wscertificado.php y modificar los parametros de nombre de base de datos, usuario, contraseña, y host.
1. Antes de probar, modificar el script SQL del SICECD, donde se hace una inserción de usuario luego de la creación de la tabla Certificado sustituir el nombre del curso por el nombre del curso del curso que en el laboratorio Moodle genera un certificado.
1. Correr el script SQL del SICECD antes de hacer alguna manipulación de datos en el SICECD.
1. Modificar las variables del archivo application-cert.properties, donde LOCAL guarda la ruta donde se guardarán los certificados, URL_RS la url del WS que sólo trae un certificado, URL_RSM la url del WS que trae más de un certificado y TEMP_ZIP que guarda de forma temporal un ZIP que contiene certificados, todo esto en el SICECD.
1. Además modificar la expresion cron en CertificadoMasivoController.java para probar, está función se ejecutará en el tiempo que se calendarice.
1. Correr el SICECD, para probar la traída de un certificado simple ir a la url localhost:8080/certificado, ésta url es accesible por cualquier persona.

