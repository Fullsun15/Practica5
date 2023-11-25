<?php
session_start();
// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo al formulario de inicio de sesión.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include('conexion.php');

// Función para obtener un mecánico por cedula
function obtenerMecanicoPorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM mecanicos_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para editar un mecánico
function editarMecanico($cedula, $nuevaCedula, $nombre, $apellido, $edad, $licencia, $especialidad) {
    global $conn;
    $stmt = $conn->prepare("UPDATE mecanicos_icarplus SET cedula = ?, nombre = ?, apellido = ?, edad = ?, licencia = ?, especialidad = ? WHERE cedula = ?");
    $stmt->bind_param("isssisi", $nuevaCedula, $nombre, $apellido, $edad, $licencia, $especialidad, $cedula);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_mecanico"])) {
    $cedula = $_POST["cedula"];
    $nuevaCedula = $_POST["nuevaCedula"];
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
    $apellido = isset($_POST["apellido"]) ? $_POST["apellido"] : '';
    $edad = isset($_POST["edad"]) ? $_POST["edad"] : '';
    $licencia = isset($_POST["licencia"]) ? $_POST["licencia"] : '';
    $especialidad = isset($_POST["especialidad"]) ? $_POST["especialidad"] : '';

    if (editarMecanico($cedula, $nuevaCedula, $nombre, $apellido, $edad, $licencia, $especialidad)) {
        echo "<script>alert('Mecánico editado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al editar el mecánico.');</script>";
    }
}

if (isset($_GET['cedula'])) {
    $cedulaEditar = $_GET['cedula'];
    $mecanicoEditar = obtenerMecanicoPorCedula($cedulaEditar);
} else {
    header("Location: mecanicos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Editar Mecánico</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        body{
      background-color: ghostwhite;
    }

    </style>
</head>
<body>

<nav class="red darken-2">
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo left">iCar Plus</a>
      <ul id="nav-mobile" class="right">
        <li><a href="pagina_principal.php">Inicio</a></li>
        <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
      </ul>
    </div>
  </nav>

<div class="container">
    <h3 class="center-align card-panel blue lighten-2">Editar Mecánico</h3>

    <div class="row">
        <div class="col l8 s12 m6 offset-l2">
            <div class="card">
                <div class="card-content">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <input type="hidden" name="cedula" value="<?php echo $mecanicoEditar['cedula']; ?>">
                        <div class="input-field">
                            <input id="nuevaCedula" type="text" name="nuevaCedula" value="<?php echo $mecanicoEditar['cedula']; ?>" required>
                            <label for="nuevaCedula">Nueva Cedula</label>
                        </div>
                        <div class="input-field">
                            <input id="nombre" type="text" name="nombre" value="<?php echo $mecanicoEditar['nombre']; ?>" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="input-field">
                            <input id="apellido" type="text" name="apellido" value="<?php echo $mecanicoEditar['apellido']; ?>" required>
                            <label for="apellido">Apellido</label>
                        </div>
                        <div class="input-field">
                            <input id="edad" type="number" name="edad" value="<?php echo $mecanicoEditar['edad']; ?>" required>
                            <label for="edad">Edad</label>
                        </div>
                        <div class="input-field">
                            <input id="licencia" type="text" name="licencia" value="<?php echo $mecanicoEditar['licencia']; ?>" required>
                            <label for="licencia">Licencia</label>
                        </div>
                        <div class="input-field">
                            <input id="especialidad" type="text" name="especialidad" value="<?php echo $mecanicoEditar['especialidad']; ?>" required>
                            <label for="especialidad">Especialidad</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="editar_mecanico">Editar Mecánico</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="left">
         <a href="mecanicos.php"><i class="material-icons left">arrow_back</i>Regresar</a><hr>
     </div>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="assets/js/init.js"></script>
</body>
</html>
