<?php
session_start();

// Incluye el archivo de conexión.
require_once "conexion.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    $sql = "SELECT id, User, Pass FROM usuarios WHERE User = ? and Pass = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $username, $password);
        $stmt->fetch();

        $_SESSION['user_id'] = $user_id;
        echo "1"; // Respuesta exitosa
    } else {
        echo "Usuario o contraseña incorrectos."; // Respuesta de error
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión - iCar Plus</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <style>
        body{
            background-color: #b2ebf2;
        }

        #carta{
            margin-top: 80px;
        }
    </style>
</head>
<body>

    <div id="carta" >
    <h1 class="center">Bienvenido!</h1><br>
    <div class="container mt-5">
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title center" style="font-weight: bold; ">Iniciar Sesión</span>
                        <form id="login-form">
                        <div class="input-field">
                            <label for="user">
                                <i class="material-icons" style="vertical-align: middle;">perm_identity</i>
                                Usuario:
                            </label>
                            <input type="text" id="user" name="user" class="validate" required>
                        </div>
                        <div class="input-field">
                            <label for="pass">
                                <i class="material-icons" style="vertical-align: middle;">lock_outline</i>
                                Contraseña:
                            </label>
                            <input type="password" id="pass" name="pass" class="validate" required>
                            </div>
                            <br>
                            <div class="center">
                            <button class="btn waves-effect waves-light" type="submit">Iniciar Sesión
                                <i class="material-icons right">send</i>
                            </button>
                            </div>
                        </form>
                    </div>
                    <div id="resultado" style="color: red;"></div>
                </div>
            </div>
        </div>
    </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="assets/js/init.js"></script>
</body>
</html>
