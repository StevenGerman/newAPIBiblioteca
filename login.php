<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/vendor/autoload.php';

require 'conexion.php';

$pdo = new Conexion();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY']; 
//$auth = new Authentication($key);

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Autorization{
    
    private $claveSecreta;

    public function __construct($claveSecreta) {
        $this->claveSecreta = $claveSecreta; // Asigna la clave secreta correctamente
    }
}



function getToken($key){
    $headers = apache_request_headers();
    if(!isset($headers['Authorization'])){
        http_response_code(403);
        echo json_encode(array("error" => "Unauthentizaded request"));
        return;
    }
    $authorization = $headers['Authorization'];
    $authorization = explode(' ', $authorization);
    $token = $authorization[1];

    try{
        return JWT::decode($token, new Key($key, 'HS256'));

    }catch(Exception $e){
        http_response_code(403);
        echo json_encode(array("error" => "Expired Token"));
        return;
    }

    
}

function validateToken($pdo,$key){
    $info = getToken($key);
    if($info == null){
        return;
    }else{
        $sql = "SELECT * FROM personas WHERE idPersona = :idPersona";
        $stmt = $pdo->prepare($sql);
        $idPersona = $info->data->idPersona;
        //echo json_encode($info->data->idPersona);
        $stmt->bindParam(':idPersona', $idPersona);
        $stmt -> execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $dataValidada = $stmt->fetch();
        if($dataValidada == null){
            return false;
        }
        return $dataValidada['idPersona'];
    }
    
    
}



if(isset($_SERVER['REQUEST_METHOD']) == 'POST'){

    $data = json_decode(file_get_contents('php://input'),true);

    //echo json_encode($data);
    if(isset($data['perDni']) && $data['perContrasena']){
        $perDni = $data['perDni'];
        $perContrasena = $data['perContrasena'];
    }else{
        echo 'Credenciales invalidas';
        return;
    }
    

    $sql = $pdo->prepare("SELECT p.idPersona, p.perDni,p.perContrasena,r.idRol,r.rolNombre FROM personas as p INNER JOIN roles as r ON p.rolID = r.idRol WHERE p.perDni = :perDni && p.perContrasena = :perContrasena");
        
        $sql->bindValue(':perDni', $perDni);
        $sql->bindValue(':perContrasena', $perContrasena);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        $datos = $sql->fetchAll();

        if(!empty($datos)){
            
            //echo json_encode($datos);

            $now = time();
            $key = 'example_key';
            $payload = [
            'iat' => $now,
            'exp' => $now + 3600,
            'data' => [
                "isSuccess" => true,
                'idPersona' => $datos[0]['idPersona'],
                'idRol' => $datos[0]['idRol'],
                'rolNombre' => $datos[0]['rolNombre']
            ]
                
            
            
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');  
            //return $jwt;
            print_r($jwt);  
            


        }else{
            json_encode([
                "isSuccess" => false,
                "message" => 'Credenciales invalidas'
            ]);
            
        }


        


    
}