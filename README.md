# WebServiceMoodle
Web Service Moodle-Aplicación
## Instrucciones WS de certificado (válido para wscertificados.php)
1. Colocar el archivo wscertificado.php y webservice.ini en la carpeta de simplecertificate de moodle (Ej. /opt/lampp/htdocs/moodle3.5/mod/simplecertificate).
1. Modificar las variables del archivo application-cert.properties, donde LOCAL guarda la ruta donde se guardarán los certificados y TEMP_ZIP que guarda de forma temporal un ZIP que contiene certificados, todo esto en el SICECD.
1. Además modificar la expresion cron en CertificadoMasivoController.java para probar, está función se ejecutará en el tiempo que se calendarice.
1. Se tiene que insertar en la base de datos de Moodle en la tabla Url_ws, entradas, una de ellas debe contener lo siguiente : (< url que procesa 1 solo certificado>,false,true), la otra : (< url que procesa más de un certificado >,true,true).
1. Correr el SICECD, para probar la traída de un certificado simple ir a la url localhost:8080/certificado, ésta url es accesible por cualquier persona.

## Instrucciones WS de profesores y calificaciones
1. Agregar la carpeta WS a la carpeta de la Aplicación Moodle "htdocs"
1. Abrir el archivo Database.php que se encuentra en la ruta WB/config y modificar los parametros de nombre de base de datos, usuario, contraseña, puerto, host.
1. las url disponibles por ahora son las siguentes:
#http://localhost:8888/WB/api/users/read.php
#http://localhost:8888/WB/api/users/grades.php
Considera que se debe de cambiar el puerto en las urls.
1. Agregar a la tablas de la base de datos de SICECD Url_ws_profesor y Url_ws_inscripcion sus respectivos url
## Ejemplo


insert into Url_ws_inscripcion (url,nombre,activa) values ('http://localhost:8888/WB/api/grades/grades.php','calificaciones',true)


1. En la base de datos de Moodle actualizar el siguiente curso Biología COSDAC 2018.

Correr el siguiente query en la base de datos de Moodle UPDATE `mdl_course` SET `idnumber`='3' WHERE shortname='Biología COSDAC 2018'
