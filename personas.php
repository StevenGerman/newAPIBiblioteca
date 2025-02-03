<?php

// Habilita el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Habilita los métodos HTTP permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Establece el tipo de contenido de la respuesta como JSON
header("Content-Type: application/json; charset=UTF-8");
// Habilita las cabeceras permitidas
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include 'conexion.php';

$pdo = new Conexion();


// Maneja la solicitud según el método HTTP


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getRequest($pdo);
        break;
    case 'POST':
        postRequest($pdo);
        break;
    case 'PUT':
        putRequest($pdo);
        break;
    case 'DELETE':
        deleteRequest($pdo);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}

function getRequest($pdo){
    $sql = $pdo->prepare("SELECT * FROM personas");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    exit;
}

function postRequest($pdo){

    $data = json_decode(file_get_contents("php://input"));
    if(isset($data->perNombre)){
        $sql = "INSERT INTO personas (perNombre,perApellido,perDni,perContrasena,rolID) values ((:perNombre),(:perApellido),(:perDni),(:perContrasena),(:rolID));";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':perNombre', $data->perNombre);
        $stmt->bindParam(':perApellido', $data->perApellido);
        $stmt->bindParam(':perDni', $data->perDni);
        $stmt->bindParam(':perContrasena', $data->perContrasena);
        $stmt->bindParam(':rolID', $data->rolID);

        if($stmt->execute()){
            $idPost = $pdo->lastInsertId();
            header("HTPP/1.1 201 Created");
            echo json_encode($idPost);//Restorna el ID de la persona creada.
        }else{
            header("HTPP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo crear la persona']);
        }
    }else{
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
        exit;
    }
    
}


function putRequest($pdo){
    $data = json_decode(file_get_contents("php://input"));



    if(isset($data->perNombre)){
        $sql = "UPDATE personas set perNombre=(:perNombre), perApellido=(:perApellido), perDni=(:perDni), perContrasena=(:perContrasena), rolID=(:rolID) where idPersona = (:idPersona)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':perNombre', $data->perNombre);
        $stmt->bindParam(':perApellido',$data->perApellido);
        $stmt->bindParam(':perDni', $data->perDni);
        $stmt->bindParam(':perContrasena', $data->perContrasena);
        $stmt->bindParam(':rolID', $data->rolID);
        $stmt->bindParam(':idPersona', $data->idPersona);
        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Actualización exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar la persona']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;

}


function deleteRequest($pdo){
 // Verifica si se proporciona 'idPersona'
 if (isset($_GET['idPersona'])) {
    $sql = "DELETE FROM Personas WHERE idPersona=:idPersona";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':idPersona', $_GET['idPersona']);
    if ($stmt->execute()) {
        header("HTTP/1.1 200 OK");
        echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['error' => 'No se pudo eliminar la persona']);
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Entrada inválida']);
}
exit;
}


?>