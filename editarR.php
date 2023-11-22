<?php
include('conexion.php');

// Función para obtener un repuesto por serial
function obtenerRepuestoPorSerial($serial) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM repuestos_icarplus WHERE serial = ?");
    $stmt->bind_param("s", $serial);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para editar un repuesto
function editarRepuesto($serial, $marca, $nombre, $cantidad, $imagen) {
    global $conn;
    $stmt = $conn->prepare("UPDATE repuestos_icarplus SET marca = ?, nombre = ?, cantidad = ?, imagen = ? WHERE serial = ?");
    $stmt->bind_param("ssiss", $marca, $nombre, $cantidad, $imagen, $serial);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_repuesto"])) {
    $serial = $_POST["serial"];
    $marca = isset($_POST["marca"]) ? $_POST["marca"] : '';
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
    $cantidad = isset($_POST["cantidad"]) ? $_POST["cantidad"] : '';

    // Obtener la imagen actual del repuesto
    $repuestoActual = obtenerRepuestoPorSerial($serial);
    $imagenActual = $repuestoActual['imagen'];

    // Subir nueva imagen si se proporciona
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $target_dir = "img_Repu/";
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen real o un archivo falso
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check !== false) {
            // Permitir solo ciertos formatos de archivo
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                $imagenNueva = $target_file;
                move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
            } else {
                echo "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
            }
        } else {
            echo "El archivo no es una imagen válida.";
        }
    } else {
        // Conservar la imagen actual si no se proporciona una nueva
        $imagenNueva = $imagenActual;
    }

    // Editar el repuesto
    if (editarRepuesto($serial, $marca, $nombre, $cantidad, $imagenNueva)) {
        echo "Repuesto editado con éxito.";
    } else {
        echo "Error al editar el repuesto.";
    }
}

// Obtener y mostrar el repuesto editado
if (isset($_GET['serial'])) {
    $serialEditar = $_GET['serial'];
    $repuestoEditar = obtenerRepuestoPorSerial($serialEditar);
} else {
    // Redirigir a la página principal si no se proporciona un serial
    header("Location: repuestos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Editar Repuesto</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
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
    <h3 class="center-align card-panel blue lighten-2">Editar Repuesto</h3>

    <!-- Formulario para editar repuesto -->
    <div class="row">
        <div class="col l8 s12 m6 offset-l2">
            <div class="card">
                <div class="card-content">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="serial" value="<?php echo $repuestoEditar['serial']; ?>">
                        <div class="input-field">
                            <input id="marca" type="text" name="marca" value="<?php echo $repuestoEditar['marca']; ?>" required>
                            <label for="marca">Marca</label>
                        </div>
                        <div class="input-field">
                            <input id="nombre" type="text" name="nombre" value="<?php echo $repuestoEditar['nombre']; ?>" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="input-field">
                            <input id="cantidad" type="number" name="cantidad" value="<?php echo $repuestoEditar['cantidad']; ?>" required>
                            <label for="cantidad">Cantidad</label>
                        </div>
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Imagen</span>
                                <input type="file" name="imagen">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="editar_repuesto">Editar Repuesto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="left">
         <a href="repuestos.php"><i class="material-icons left">arrow_back</i>Regresar</a><hr>
     </div>
</div>


<!-- Incluye los archivos JavaScript de Materialize -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<!-- Agrega tus propios scripts si es necesario -->
</body>
</html>
