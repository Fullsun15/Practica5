<?php
session_start();

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo al formulario de inicio de sesión.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include('conexion.php');
function agregarMecanico($cedula, $nombre, $apellido, $edad, $licencia, $especialidad) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO mecanicos_icarplus (cedula, nombre, apellido, edad, licencia, especialidad) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssis", $cedula, $nombre, $apellido, $edad, $licencia, $especialidad);
    return $stmt->execute();
}

function obtenerMecanicoPorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM mecanicos_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function eliminarMecanico($cedula) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM mecanicos_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    return $stmt->execute();
}

function obtenerMecanicos() {
    global $conn;
    $result = $conn->query("SELECT * FROM mecanicos_icarplus");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$mecanicoEditar = null;

// Obtener datos del mecánico para prellenar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_mecanico"])) {
    $cedulaEditar = $_POST["cedula"];
    $mecanicoEditar = obtenerMecanicoPorCedula($cedulaEditar);
}

// Ejemplo de uso: Agregar mecánico
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_mecanico"])) {
    $cedula = $_POST["cedula"];
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
    $apellido = isset($_POST["apellido"]) ? $_POST["apellido"] : '';
    $edad = isset($_POST["edad"]) ? $_POST["edad"] : '';
    $licencia = isset($_POST["licencia"]) ? $_POST["licencia"] : '';
    $especialidad = isset($_POST["especialidad"]) ? $_POST["especialidad"] : '';

    if (agregarMecanico($cedula, $nombre, $apellido, $edad, $licencia, $especialidad)) {
        echo "<script>alert('Mecánico agregado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al agregar el mecánico.');</script>";
    }
}

// Ejemplo de uso: Eliminar mecánico
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_mecanico"])) {
    $cedula = $_POST["cedula"];

    if (eliminarMecanico($cedula)) {
        echo "<script>alert('Mecánico eliminado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el mecánico.');</script>";
    }
}

// Obtener y mostrar la lista de mecánicos
$mecanicos = obtenerMecanicos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Mecánicos</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <style>
        body{
      background-color: ghostwhite;

      table, thead, tr{
            border: 1px solid black; 
        }

        .text-center {
        text-align: center;
        }
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
<br>
<div class="container">
    <h3 class="center-align card-panel blue lighten-2">Mecánicos</h3>

    <!-- Formulario para agregar mecánico -->
    <div class="row">
        <div class="col l8 s12 m6 offset-l2">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Agregar Mecánico</span>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="input-field">
                            <input id="cedula" type="number" name="cedula" required>
                            <label for="cedula">Cedula</label>
                        </div>
                        <div class="input-field">
                            <input id="nombre" type="text" name="nombre" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="input-field">
                            <input id="apellido" type="text" name="apellido" required>
                            <label for="apellido">Apellido</label>
                        </div>
                        <div class="input-field">
                            <input id="edad" type="number" name="edad" min="18" max="75" required>
                            <label for="edad">Edad</label>
                        </div>
                        <div class="input-field">
                            <input id="licencia" type="number" name="licencia" min="0" max="9999999999999" required>
                            <label for="licencia">Licencia (máx. 13 dígitos)</label>
                        </div>
                        <div class="input-field">
                            <input id="especialidad" type="text" name="especialidad" required>
                            <label for="especialidad">Especialidad</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="agregar_mecanico">Agregar Mecánico</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de mecánicos -->
    <div class="row">
        <div class="col s12">
        <div class="right">
        <a href="fpdf\reporteM.php"target="_blank"><i class="material-icons left">picture_as_pdf</i>Reporte PDF</a><hr>
        </div>
        <table class="striped">
                <thead>
                <tr>
                    <th class='text-center'>#</th>
                    <th class='text-center'>Cedula</th>
                    <th class='text-center'>Nombre</th>
                    <th class='text-center'>Apellido</th>
                    <th class='text-center'>Edad</th>
                    <th class='text-center'>Licencia</th>
                    <th class='text-center'>Especialidad</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $N_contador = 1;
                foreach ($mecanicos as $mecanico) {
                    echo "<tr>";
                    echo "<td class='text-center'>" . $N_contador . "</td>";
                    echo "<td class='text-center'>" . $mecanico['cedula'] . "</td>";
                    echo "<td class='text-center'>" . $mecanico['nombre'] . "</td>";
                    echo "<td class='text-center'>" . $mecanico['apellido'] . "</td>";
                    echo "<td class='text-center'>" . $mecanico['edad'] . "</td>";
                    echo "<td class='text-center'>" . $mecanico['licencia'] . "</td>";
                    echo "<td class='text-center'>" . $mecanico['especialidad'] . "</td>";
                    echo "<td class='text-center'>
                            <form action='editarM.php' method='get'>
                                <input type='hidden' name='cedula' value='" . $mecanico['cedula'] . "'>
                                <button class='btn waves-effect waves-light' type='submit' name='editar_mecanico'>Editar</button>
                            </form>
                        </td>";
                    echo "<td class='text-center'>
                        <form action='" . $_SERVER['PHP_SELF'] . "' method='post'>
                            <input type='hidden' name='cedula' value='" . $mecanico['cedula'] . "'>
                            <button class='btn red waves-effect waves-light' type='submit' name='eliminar_mecanico'>Eliminar</button>
                        </form>
                    </td>";
                    
                    echo "</tr>";

                    $N_contador++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Incluye los archivos JavaScript de Materialize -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="assets/js/init.js"></script>
</body>
</html>
