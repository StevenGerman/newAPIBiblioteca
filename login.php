<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/vendor/autoload.php';

require_once 'conexion.php';

require 'auth.php';
require 'vendor/autoload.php'; // Autoload de Composer para JWT

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
//Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY']; 

$pdo = new Conexion();
$auth = new Autorization($key);


if(isset($_SERVER['REQUEST_METHOD']) == 'POST'){

    $data = json_decode(file_get_contents('php://input'),true);

    //echo json_encode($data);
    if(isset($data['perDni']) && isset($data['perContrasena'])){
        $perDni = $data['perDni'];
        $perContrasena = $data['perContrasena'];
    }
        $sql = "SELECT p.idPersona, p.perDni,p.perContrasena,r.idRol,r.rolNombre FROM personas as p INNER JOIN roles as r ON p.rolID = r.idRol WHERE p.perDni = :perDni && p.perContrasena = :perContrasena";

        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':perDni', $perDni);
        $stmt->bindParam(':perContrasena', $perContrasena);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        $datos = $stmt->fetch();
        if(!empty($datos)){

            $token = $auth->generateToken($datos['idPersona'], $datos['rolNombre']);
            echo json_encode([
                "isSuccess" => true,
                "token" => $token]);
            //return $auth->generateToken($datos[0]['idPersona'],$datos[0]['rolNombre']);
        }else{
            echo json_encode([
                "isSuccess" => false,
                 "message" => "Credenciales inválidas"]);

        }
    
}