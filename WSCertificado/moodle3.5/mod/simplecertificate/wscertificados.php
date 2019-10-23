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

/*select * from mdl_simplecertificate_issues t1,mdl_user t2 where code='5cbf62b0-3384-44db-be48-110d7f000001' AND t1.userid=t2.id; fis = new FileInputStream(TEMP_ZIP);
por nombre y correo electronico
*/
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
header("Content-type: application/json; charset=utf-8");
$json_p = $_POST['json'];
$clave = $_POST['clave'];
$array_ini = parse_ini_file("webservice.ini");
if($clave != $array_ini['clave']){
    echo json_encode(array("mensaje"=> "Error"));
    return;
}
$lista = json_decode($json_p,true);
$cantidad = (int)$lista['cuenta'];
$i = 0;
$mysqli = new mysqli($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
$mysqli->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
$zip = new ZipArchive;
$r = $zip->open('test_folder_change.zip', ZipArchive::CREATE) === TRUE;
$arr = array();
while ($i < $cantidad){
    $a = "correo".$i;
    $c = "tiempo".$i;
    $d = "id_curso".$i;
    //echo "{$lista[$a]} "."{$lista[$b]} "."{$lista[$c]}";
    inicio($lista[$a],$lista[$c],$lista[$d]);
    $i++;
}

$zip->close();
$handle = fopen('test_folder_change.zip', 'rb');
$size = filesize('test_folder_change.zip');
$content = fread($handle,$size);
$content = base64_encode($content);
if(count($arr) == 0){
    json_encode(array("mensaje" => "No hay archivos"));
} else {
    echo json_encode(array("zip" => $content) + $arr + array("mensaje" => "NULL"));
}
fclose($handle);
unlink('test_folder_change.zip');
$mysqli->close();
/**
* Funcion que llama mete al arreglo un solo arreglo asociado a un certificado
* @param $email correo de usuario
* @param $nombre_curso nombre de curso
* @param $tiempo_t tiempo de creacion de archivo a actualizar
*/
function inicio($email,$tiempo_t,$id_curso){
    global $arr;
    if(!array_key_exists($id_curso, $arr)){
        $arr += array($id_curso => array());
    }
    global $mysqli;
    $resultado = $mysqli->query("SELECT * FROM mdl_user WHERE email = '{$email}';");
    if($resultado->num_rows == 0){
        return;
    }
    $id = $resultado->fetch_assoc()['id'];

    $resultado3 = $mysqli->query("SELECT * FROM mdl_course WHERE idnumber = '{$id_curso}' AND idnumber LIKE '%\_%';");
    if($resultado3->num_rows == 0){
        return;
    }
    $nc = $resultado3->fetch_assoc()['fullname'];

    $resultado2 = $mysqli->query("SELECT * FROM mdl_simplecertificate_issues WHERE userid = {$id} AND coursename = '{$nc}';");
    if($resultado2->num_rows == 0){
        return;
    }
    $row = $resultado2->fetch_assoc();
    $code = $row['code'];
    $tiempo = $row['timecreated'];
    $nombre_archivo = $row['certificatename'];
    $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $nombre_archivo = strtr( $nombre_archivo, $unwanted_array );

    if($tiempo_t >= $tiempo){
        return;
    }
    //echo "APLICO\n";
    aplicar($code,$email,$nombre_archivo,$tiempo,$id_curso);
}
/**
* Funcion que mete al arreglo un arreglo asociado a un certificado.
* @param $code codigo de certificado
* @param $email correo de usuario
* @param $nombre_curso nombre de curso
* @param $nombre_archivo nombre de certificado
* @param $arr arreglo que contendra la informacion de los archivos a enviar
*/
function aplicar($code,$email,$nombre_archivo,$tiempo,$id_curso){
    global $DB;
    $issuedcert = $DB->get_record("simplecertificate_issues", array('code' => $code));
    if (!$issuedcert) {
        print_error(get_string('issuedcertificatenotfound', 'simplecertificate'));
    } else {
        get_certificate_file($issuedcert,$email,$nombre_archivo,$tiempo,$id_curso);
    }
}

#$code = "5bfee0a2-1288-4412-badb-144e84f89644";
/**
* Funcion que envía un certificado codificado en un json
* @param $issuedcert clase asociada a un certificado expedido o por expedir
* @param $emal correo de usuario
* @param $certificate_name nombre de certificado
*/
function get_certificate_file(stdClass $issuedcert, $emal,$certificate_name,$tiempo,$id_curso) {
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
    //$base = basename($path);
    global $zip;
    $zip->addFile($path, "{$id_curso}/{$emal}/{$certificate_name}.pdf");
    global $arr;
    $arr[$id_curso] += array("{$emal}" => $tiempo);
    //echo "ADD to zip";
}
