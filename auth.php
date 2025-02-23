<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/vendor/autoload.php';

require 'conexion.php';

$pdo = new Conexion();


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;



if(isset($_SERVER['REQUEST_METHOD']) == 'GET'){

    $perDni = $_GET['perDni'];
    $perContrasena = $_GET['perContrasena'];
    

    $sql = $pdo->prepare("SELECT p.idPersona, p.perDni,p.perContrasena,r.idRol,r.rolNombre FROM personas as p INNER JOIN roles as r ON p.rolID = r.idRol WHERE p.perDni = :perDni && p.perContrasena = :perContrasena");
        
        $sql->bindValue(':perDni', $perDni);
        $sql->bindValue(':perContrasena', $perContrasena);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        $datos = $sql->fetchAll();

        if(!empty($datos)){
            
            echo json_encode($datos);

            $now = time();
            $key = 'example_key';
            $payload = [
            'iat' => $now,
            'exp' => $now + 3600,
            'data' => [
                'idPersona' => $datos[0]['idPersona'],
                'idRol' => $datos[0]['idRol'],
                'rolNombre' => $datos[0]['rolNombre']
            ]
                
            
            
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');  
            print_r($jwt);  
            /*try {
                $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
                
            } catch (Exception $e) {
                echo 'An error occurred: ' . $e->getMessage();
            }*/


        }else{
            echo 'Credenciales invalidas';
        }


        


    
}