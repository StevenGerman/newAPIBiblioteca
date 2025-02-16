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
        registerUser($pdo);
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

function registerUser($pdo) {

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
            

            error_log("All fields set: perNombre=$perNombre, perApellido=$perApellido, perDni=$perDni");

            // Cifrar contraseña
            $hashedPassword = password_hash($perContrasena, PASSWORD_BCRYPT);
            //error_log("Password hashed");

            // Verificar si el DNI ya existe
            $checkUser = $pdo->prepare("SELECT * FROM personas WHERE perDni = :perDni");
            $checkUser->bindParam(':perDni', $perDni);
            $checkUser->execute();
            //error_log("Email checked");

            // Si quieres activar la verificación de email repetido
            
            if ($checkUser->rowCount() > 0) {
                //error_log("Email already registered");
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(array("message" => "El usuario ya está registrado."));
                exit;
            }
           
            $rol = 2;
            // Insertar nuevo Usuario
            $sql = $pdo->prepare("INSERT INTO personas (perNombre, perApellido, perDni, perContrasena, rolID) 
                                  VALUES (:perNombre, :perApellido, :perMail, :perDni, :perContrasena, :rolID)");
            $sql->bindParam(':perNombre', $perNombre);
            $sql->bindParam(':perApellido', $perApellido);
            $sql->bindParam(':perDni', $perDni);
            $sql->bindParam(':perContrasena', $hashedPassword);
            $sql->bindParam(':rolID', $rol);
            $sql->execute();
            //error_log("User inserted successfully");

            header("HTTP/1.1 201 Created");
            echo json_encode(array("message" => "Usuario creado exitosamente"));
        } else {
            //error_log("Missing fields in request");
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("message" => "Debe proporcionar nombre, apellido, correo, DNI y contraseña."));
        }
}