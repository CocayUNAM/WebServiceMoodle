<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Add Watermark and send files
 *
 * @package mod
 * @subpackage simplecertificate
 * @copyright 2014 © Carlos Alexandre Soares da Fonseca
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*select * from mdl_simplecertificate_issues t1,mdl_user t2 where code='5cbf62b0-3384-44db-be48-110d7f000001' AND t1.userid=t2.id;
por nombre y correo electronico
*/
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
#$code = required_param('code', PARAM_TEXT); // Issued Code.
/* Este es mi código */
//$_GET['variable']
//$email = required_param('email',PARAM_TEXT);
//$nombre_curso = required_param('nc',PARAM_TEXT);
$clave = $_POST['clave'];
$array_ini = parse_ini_file("webservice.ini");
if($clave != $array_ini['clave']){
    echo json_encode(array("mensaje" => "Error"));
    return;
}
$nombre_curso = $_POST['nc'];
$email = $_POST['email'];
$mysqli = new mysqli($CFG->dbhost,$CFG->dbuser ,$CFG->dbpass,$CFG->dbname);

$resultado = $mysqli->query("SELECT * FROM mdl_user WHERE email = '{$email}';");
if($resultado->num_rows == 0){
    echo json_encode(array("mensaje" => "No existe usuario"));
}
$id = $resultado->fetch_assoc()['id'];
$codigo = explode("|",$nombre_curso);

$resultado3 = $mysqli->query("SELECT * FROM mdl_course WHERE idnumber = '{$codigo[0]}';");
if($resultado3->num_rows == 0){
    echo json_encode(array("mensaje" => "No existe curso"));
}
$nc = $resultado3->fetch_assoc()['fullname'];

$resultado2 = $mysqli->query("SELECT * FROM mdl_simplecertificate_issues WHERE userid = {$id} AND coursename = '{$nc}';");
if($resultado2->num_rows == 0){
    echo json_encode(array("mensaje" => "No existe constancia"));
}
$row = $resultado2->fetch_assoc();
$code = $row['code'];
$tiempo = $row['timecreated'];
$mysqli->close();

#$code = "5bfee0a2-1288-4412-badb-144e84f89644";

$issuedcert = $DB->get_record("simplecertificate_issues", array('code' => $code));
if (!$issuedcert) {
    print_error(get_string('issuedcertificatenotfound', 'simplecertificate'));
} else {
    send_certificate_file($issuedcert,$email,$nombre_curso,$tiempo);
}
/**
* Funcion que devuelve un json que contiene un certificado de un usuario codificado en base 64
* @param $path ruta del archivo
* @param $emal correo de usuario
* @param $course_name nombre de curso
* @param $certificate_name nombre de certificado 
* @return json con un certificado de usuario codificado en base 64
*/
function json_pdf_from_path($path = '',$emal,$course_name,$tiempo){
    if($path == ''){
        return json_encode(array("mensaje"=>"Archivo no encontrado"));
    }
    $handle = fopen($path,"rt");
    $size = filesize($path);
    $content = fread($handle,$size);
    $content = base64_encode($content);
    $array = array("mensaje" => "NULL","correo" => $emal,"nombre_curso" =>$course_name,"bytespdf" => $content, "tiempo" => $tiempo);
    return json_encode($array);
}
/**
* Funcion que envía un certificado codificado en un json
* @param $issuedcert clase asociada a un certificado expedido o por expedir
* @param $emal correo de usuario
* @param $course_name nombre de curso
* @param $certificate_name nombre de certificado
*/
function send_certificate_file(stdClass $issuedcert, $emal,$course_name,$tiempo) {
    global $CFG, $USER, $DB, $PAGE;

    if ($issuedcert->haschange) {
        // This issue have a haschange flag, try to reissue.
        if (empty($issuedcert->timedeleted)) {
            require_once($CFG->dirroot . '/mod/simplecertificate/locallib.php');
            try {
                // Try to get cm.
                $cm = get_coursemodule_from_instance('simplecertificate', $issuedcert->certificateid, 0, false, MUST_EXIST);

                $context = context_module::instance($cm->id);

                // Must set a page context to issue .
                $PAGE->set_context($context);
                $simplecertificate = new simplecertificate($context, null, null);
                $file = $simplecertificate->get_issue_file($issuedcert);

            } catch (moodle_exception $e) {
                // Only debug, no errors.
                debugging($e->getMessage(), DEBUG_DEVELOPER, $e->getTrace());
            }
        } else {
            // Have haschange and timedeleted, somehting wrong, it will be impossible to reissue
            // add wraning.
            debugging("issued certificate [$issuedcert->id], have haschange and timedeleted");
        }
        $issuedcert->haschange = 0;
        $DB->update_record('simplecertificate_issues', $issuedcert);
    }

    if (empty($file)) {
        $fs = get_file_storage();
        if (!$fs->file_exists_by_hash($issuedcert->pathnamehash)) {
            print_error(get_string('filenotfound', 'simplecertificate', ''));
        }

        $file = $fs->get_file_by_hash($issuedcert->pathnamehash);
    }

    //copy_content_to_temp esta en stored file
    $path = $file->copy_content_to_temp("/SICECD");
    chmod($path,777);
    echo json_pdf_from_path($path,$emal,$course_name,$tiempo);
    /*manipular el archivo(cambiar el nombre y permisos) y enviarlo*/
}