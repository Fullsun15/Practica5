<?php
session_start();

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo al formulario de inicio de sesión.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include('conexion.php');
// Obtener el ID del registro de la URL
if (isset($_GET['id'])) {
    $idRegistro = $_GET['id'];

    // Función para obtener la información de un registro por ID
    function obtenerRegistroPorID($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM registros_icarplus WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Obtener detalles del registro
    $registro = obtenerRegistroPorID($idRegistro);

    // Verificar si el registro existe
    if (!$registro) {
        echo "Registro no encontrado.";
        exit();
    }

    // Función para obtener la información de un vehículo por matrícula
function obtenerVehiculoPorMatricula($matricula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM vehiculos_icarplus WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener la información de un cliente por cédula
function obtenerClientePorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientes_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener la información de un mecánico por cédula
function obtenerMecanicoPorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM mecanicos_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para obtener la información de un repuesto por serial
function obtenerRepuestoPorSerial($serial) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM repuestos_icarplus WHERE serial = ?");
    $stmt->bind_param("s", $serial);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


    // Obtener información adicional
    $vehiculo = obtenerVehiculoPorMatricula($registro['matricula_vehiculos']);
    $cliente = obtenerClientePorCedula($vehiculo['cedula_cliente']);
    $mecanico = obtenerMecanicoPorCedula($registro['cedula_mecanicos']);
    $repuesto = obtenerRepuestoPorSerial($registro['serial_repuestos']);
} else {
    echo "ID de registro no proporcionado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Detalles del Registro</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <style>
        body{
      background-color: ghostwhite;
    }

    </style>
</head>
<body>

<!-- Encabezado -->
<nav class="red darken-2">
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo left">iCar Plus</a>
      <ul id="nav-mobile" class="right">
        <li><a href="pagina_principal.php">Inicio</a></li>
        <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
      </ul>
    </div>
  </nav>

<!-- Contenido -->
<div class="container">

    <h3 class="center-align card-panel blue lighten-2">Detalles del Registro</h3>
    <div class="right">
    <a href="reporteRE_I.php?id=<?php echo $idRegistro; ?>" target="_blank"><i class="material-icons left">picture_as_pdf</i>Reporte PDF</a><hr>


    </div>

    <!-- Información del Vehículo -->
    <div class="row">
        <div class="col l8 offset-l2 s12">
            <div class="card">
                <div class="card-content">
                    <table class="responsive-table">
                        <caption>INFORMACIÓN DEL VEHÍCULO:</caption>
                        <tr>
                            <th>Matrícula Vehículo:</th>
                            <td><?php echo $vehiculo['matricula']; ?></td>
                        </tr>
                        <tr>
                            <th>Modelo Vehículo:</th>
                            <td><?php echo $vehiculo['modelo']; ?></td>
                        </tr>
                        <tr>
                            <th>Cédula Cliente Vehículo:</th>
                            <td><?php echo $cliente['cedula']; ?></td>
                        </tr>
                        <tr>
                            <th>Foto Vehículo:</th>
                            <td><img src="<?php echo $vehiculo['imagen']; ?>" alt="Foto del Vehículo" style="max-width: 200px;"></td>
                        </tr>
                        <tr>
                            <th>Descripción del Vehículo:</th>
                            <td><?php echo $vehiculo['descripcion']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Mecánico -->
    <div class="row">
        <div class="col l8 offset-l2 s12">
            <div class="card">
                <div class="card-content">
                    <table class="responsive-table">
                        <caption>INFORMACIÓN DEL MECÁNICO:</caption>
                        <tr>
                            <th>Nombre Mecánico:</th>
                            <td><?php echo $mecanico['nombre'] . ' ' . $mecanico['apellido']; ?></td>
                        </tr>
                        <tr>
                            <th>Cédula Mecánico:</th>
                            <td><?php echo $mecanico['cedula']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Repuesto -->
    <div class="row">
        <div class="col l8 offset-l2 s12">
            <div class="card">
                <div class="card-content">
                    <table class="responsive-table">
                        <caption>INFORMACIÓN DEL REPUESTO:</caption>
                        <tr>
                            <th>Serial Repuestos:</th>
                            <td><?php echo $repuesto['serial']; ?></td>
                        </tr>
                        <tr>
                            <th>Nombre Repuesto:</th>
                            <td><?php echo $repuesto['nombre']; ?></td>
                        </tr>
                        <tr>
                            <th>Cantidad Repuestos:</th>
                            <td><?php echo $registro['cantidad_repuestos']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Fecha de Ingreso -->
    <div class="row">
        <div class="col l8 offset-l2 s12">
            <div class="card">
                <div class="card-content">
                    <p class="center"><strong>Fecha de Ingreso:</strong> <?php echo $registro['fecha_ingreso']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" >

    <div class="left">
             <a href="registros.php"><i class="material-icons left">arrow_back</i>Regresar</a><hr>
         </div>
</div>

<!-- Incluye los archivos JavaScript de Materialize -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="assets\js\init.js"></script>
</body>
</html>
