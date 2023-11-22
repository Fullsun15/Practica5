<?php
include('conexion.php');

// Establecer la zona horaria
date_default_timezone_set('America/Caracas');

// Función para obtener todos los registros
function obtenerRegistros() {
    global $conn;
    $result = $conn->query("SELECT * FROM registros_icarplus");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener un registro por ID
function obtenerRegistroPorId($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM registros_icarplus WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para insertar un nuevo registro
function insertarRegistro($matricula_vehiculos, $cedula_mecanicos, $serial_repuestos, $cantidad_repuestos, $fecha_ingreso) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO registros_icarplus (matricula_vehiculos, cedula_mecanicos, serial_repuestos, cantidad_repuestos, fecha_ingreso) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $matricula_vehiculos, $cedula_mecanicos, $serial_repuestos, $cantidad_repuestos, $fecha_ingreso);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para actualizar un registro
function actualizarRegistro($id, $matricula_vehiculos, $cedula_mecanicos, $serial_repuestos, $cantidad_repuestos) {
    global $conn;
    $stmt = $conn->prepare("UPDATE registros_icarplus SET matricula_vehiculos = ?, cedula_mecanicos = ?, serial_repuestos = ?, cantidad_repuestos = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $matricula_vehiculos, $cedula_mecanicos, $serial_repuestos, $cantidad_repuestos, $id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para eliminar un registro
function eliminarRegistro($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM registros_icarplus WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Procesamiento del formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registrar"])) {
    $matricula_vehiculos = $_POST["matricula_vehiculos"];
    $cedula_mecanicos = $_POST["cedula_mecanicos"];
    $serial_repuestos = $_POST["serial_repuestos"];
    $cantidad_repuestos = $_POST["cantidad_repuestos"];
    $fecha_ingreso = date('Y-m-d H:i:s');

    if (insertarRegistro($matricula_vehiculos, $cedula_mecanicos, $serial_repuestos, $cantidad_repuestos, $fecha_ingreso)) {
        echo "Registro insertado con éxito.";
    } else {
        echo "Error al insertar el registro: " . $conn->error;
    }
}

// Procesamiento del formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_registro"])) {
    $idEditar = $_POST["id"];
    $matricula_vehiculosEditar = $_POST["matricula_vehiculos"];
    $cedula_mecanicosEditar = $_POST["cedula_mecanicos"];
    $serial_repuestosEditar = $_POST["serial_repuestos"];
    $cantidad_repuestosEditar = $_POST["cantidad_repuestos"];

    if (actualizarRegistro($idEditar, $matricula_vehiculosEditar, $cedula_mecanicosEditar, $serial_repuestosEditar, $cantidad_repuestosEditar)) {
        echo "Registro actualizado con éxito.";
    } else {
        echo "Error al actualizar el registro: " . $conn->error;
    }
}

// Procesamiento del formulario de eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_registro"])) {
    $idEliminar = $_POST["id_eliminar"];

    if (eliminarRegistro($idEliminar)) {
        echo "Registro eliminado con éxito.";
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }
}

function getVerEnlace($id) {
    return "ver_registro.php?id=$id";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Registros</title>
    <!-- Incluye los archivos CSS de Materialize -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Agrega tus propios estilos si es necesario -->
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
    <h3 class="center-align card-panel blue lighten-2">Registros</h3>

    <!-- Formulario para registrar un nuevo registro -->
    <div class="row">
        <div class="col l12 s12 m6">
            <div class="card">
                <div class="card-content">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="input-field">
                            <input id="matricula_vehiculos" type="text" name="matricula_vehiculos" required>
                            <label for="matricula_vehiculos">Matrícula Vehículo</label>
                        </div>
                        <div class="input-field">
                            <input id="cedula_mecanicos" type="text" name="cedula_mecanicos" required>
                            <label for="cedula_mecanicos">Cédula Mecánico</label>
                        </div>
                        <div class="input-field">
                            <input id="serial_repuestos" type="text" name="serial_repuestos" required>
                            <label for="serial_repuestos">Serial Repuestos</label>
                        </div>
                        <div class="input-field">
                            <input id="cantidad_repuestos" type="number" name="cantidad_repuestos" required>
                            <label for="cantidad_repuestos">Cantidad Repuestos</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="registrar">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <table class="striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Matrícula Vehículo</th>
                        <th>Cédula Mecánico</th>
                        <th>Serial Repuesto</th>
                        <th>Cantidad Repuesto</th>
                        <th>Fecha Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $registros = obtenerRegistros();
                    foreach ($registros as $registro) {
                        echo "<tr>";
                        echo "<td>{$registro['id']}</td>";
                        echo "<td>{$registro['matricula_vehiculos']}</td>";
                        echo "<td>{$registro['cedula_mecanicos']}</td>";
                        echo "<td>{$registro['serial_repuestos']}</td>";
                        echo "<td>{$registro['cantidad_repuestos']}</td>";
                        echo "<td>{$registro['fecha_ingreso']}</td>";
                       /* echo "<td>
                                <a class='btn waves-effect waves-light ' href='editar_registro.php?id={$registro['id']}'>Editar</a></td>";
                        echo "<td>
                                <form style='display:inline;' method='post' action='{$_SERVER['PHP_SELF']}'>
                                    <input type='hidden' name='id_eliminar' value='{$registro['id']}'>
                                    <button type='submit' class='btn red waves-effect waves-light' name='eliminar_registro'>Eliminar</button>
                                </form></td>";*/
                        echo "<td>
                                <a class='waves-effect waves-light btn' href='" . getVerEnlace($registro['id']) . "'>Ver</a></td>";
                        echo "</tr>";
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
<script src="assets\js\init.js"></script>
</body>
</html>
