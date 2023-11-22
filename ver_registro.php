<?php
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
    <!-- Incluye los archivos CSS de Materialize -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Agrega tus propios estilos si es necesario -->
</head>
<body>

<!-- Encabezado -->
<nav class="red darken-2">
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo left">iCar Plus</a>
      <ul id="nav-mobile" class="right">
        <li><a href="pagina_principal.php">Inicio</a></li>
        <!-- Agrega otros enlaces si es necesario -->
      </ul>
    </div>
</nav>

<!-- Contenido -->
<div class="container">
    <h3 class="center-align card-panel blue lighten-2">Detalles del Registro</h3>

    <div class="row">
        <div class="col l8 offset-l2 s12">
            <div class="card">
                <div class="card-content">
                    <p><strong>Matrícula Vehículo:</strong> <?php echo $registro['matricula_vehiculos']; ?></p>
                    <p><strong>Cédula Mecánico:</strong> <?php echo $registro['cedula_mecanicos']; ?></p>
                    <p><strong>Serial Repuestos:</strong> <?php echo $registro['serial_repuestos']; ?></p>
                    <p><strong>Cantidad Repuestos:</strong> <?php echo $registro['cantidad_repuestos']; ?></p>
                    <p><strong>Fecha Ingreso:</strong> <?php echo $registro['fecha_ingreso']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluye los archivos JavaScript de Materialize -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="assets\js\init.js"></script>
</body>
</html>
