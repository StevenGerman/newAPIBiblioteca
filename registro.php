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

        // Verificar si el método HTTP es POST para registrar un nuevo usuario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //error_log("POST request received"); // Log para verificar que llegó la petición POST
        registrarUsuario($pdo);
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            // Manejar solicitudes de preflight (CORS)
            http_response_code(200);
            exit(); // Detener el script después de responder la solicitud OPTIONS
        }
        // Responder con un error si el método no es permitido
        //error_log("Method not allowed"); 
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(array("message" => "Método no permitido. Use POST para esta operación."));
    }

    /* // Capturar cualquier excepción inesperada
    error_log("Unexpected error: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("message" => "Ha ocurrido un error inesperado: " . $e->getMessage()));
    exit(); */

function registrarUsuario($pdo) {

        $data = json_decode(file_get_contents("php://input"), true);
        //error_log("Data received: " . json_encode($data)); // Log para mostrar los datos recibidos

        if (
            isset($data['perNombre']) &&
            isset($data['perApellido']) &&
            isset($data['perDni']) &&
            isset($data['perContrasena'])
                    
        ) {
            $perNombre = $data['perNombre'];
            $perApellido = $data['perApellido'];
            $perDni = $data['perDni'];
            $perContrasena = $data['perContrasena'];
            

            //error_log("All fields set: perNombre=$perNombre, perApellido=$perApellido, perDni=$perDni");

            // Cifrar contraseña
            //$hashedPassword = password_hash($perContrasena, PASSWORD_BCRYPT);
            //error_log("Password hashed");

            // Verificar si el DNI ya existe
            $stmtUsuarioExistente = $pdo->prepare("SELECT idPersona FROM personas WHERE perDni = :perDni");
            $stmtUsuarioExistente->bindParam(':perDni', $perDni);
            $stmtUsuarioExistente->execute();
            $IDusuario = $stmtUsuarioExistente->fetchColumn() ?? false;

            if ($IDusuario) {
                //error_log("Email already registered");
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(array("message" => "El usuario ya está registrado."));
                exit;
            }
           
            $rol = 2;
            // Insertar nuevo Usuario
            $sql = "INSERT INTO personas (perNombre, perApellido, perDni, perContrasena, rolID) 
                                  VALUES (:perNombre, :perApellido, :perDni, :perContrasena, :rolID)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':perNombre', $perNombre);
            $stmt->bindParam(':perApellido', $perApellido);
            $stmt->bindParam(':perDni', $perDni);
            $stmt->bindParam(':perContrasena', $perContrasena);
            $stmt->bindParam(':rolID', $rol);


            if($stmt->execute()){
                header("HTTP/1.1 201 Created");
                echo json_encode(array("message" => "Usuario creado exitosamente"));
            }else{
                header("HTTP/1.1 500 Error Server");
                echo json_encode(array("message" => "Error Server"));
            }
            
            //error_log("User inserted successfully");

            
        } else {
            //error_log("Missing fields in request");
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("message" => "Debe proporcionar nombre, apellido, correo, DNI y contraseña."));
        }
}