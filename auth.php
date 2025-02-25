<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/vendor/autoload.php';

require_once 'conexion.php';

$pdo = new Conexion();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY'];


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Autorization
{

    private $claveSecreta;

    public function __construct($claveSecreta)
    {
        $this->claveSecreta = $claveSecreta; // Asigna la clave secreta correctamente
    }

    


function getToken()
{
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        http_response_code(403);
        echo json_encode(array("error" => "Unauthentizaded request"));
        return null;
    }
    $authorization = $headers['Authorization'];
    $authorization = explode(' ', $authorization);
    $token = $authorization[1];

    try {
        return JWT::decode($token, new Key($this->claveSecreta, 'HS256'));

    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(array("error" => "Expired Token"));
        return null;
    }


}

function validateToken($pdo)
{
    $info = getToken();
    if ($info == null) {
        return;
    } else {
        $sql = "SELECT idPersona FROM personas WHERE idPersona = :idPersona";
        $stmt = $pdo->prepare($sql);
        $idPersona = $info->data->idPersona;
        //echo json_encode($info->data->idPersona);
        $stmt->bindParam(':idPersona', $idPersona);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchColumn() ?? false;
        
    }


}

function generateToken($idPersona, $rolNombre)
{
    if (isset($idPersona) && isset($rolNombre)) {


        $now = time();
        $payload = [
            'iat' => $now,
            'exp' => $now + 3600,
            'data' => [
                'idPersona' => $idPersona,
                'rolNombre' => $rolNombre
            ]
        ];

        return JWT::encode($payload, $this->claveSecreta, 'HS256');
        
    }else{
        return null;
    }
    
}













}


