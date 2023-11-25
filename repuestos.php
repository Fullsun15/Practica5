<?php
session_start();

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo al formulario de inicio de sesión.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include('conexion.php');
// Funciones de manejo de repuestos
function agregarRepuesto($serial, $marca, $nombre, $cantidad, $imagen) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO repuestos_icarplus (serial, marca, nombre, cantidad, imagen) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $serial, $marca, $nombre, $cantidad, $imagen);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function obtenerRepuestos() {
    global $conn;
    $result = $conn->query("SELECT * FROM repuestos_icarplus");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function eliminarRepuesto($serial) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM repuestos_icarplus WHERE serial = ?");
    $stmt->bind_param("i", $serial);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Procesamiento de formulario para agregar repuesto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_repuesto"])) {
    $serial = $_POST["serial"];
    $marca = $_POST["marca"];
    $nombre = $_POST["nombre"];
    $cantidad = $_POST["cantidad"];

    // Manejo de imagen
    $imagen = "img/default.jpg"; // Establece una imagen por defecto si no se proporciona ninguna

    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $target_dir = "img_Vehi";
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen real o un archivo falso
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check !== false) {
            // Permitir solo ciertos formatos de archivo
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                $imagen = $target_file;
                move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
            } else {
                echo "<script>alert('Lo siento, solo se permiten archivos JPG, JPEG y PNG.');</script>";
            }
        } else {
            echo "<script>alert('El archivo no es una imagen válida.');</script>";
        }
    }

    // Agregar el nuevo repuesto
    if (agregarRepuesto($serial, $marca, $nombre, $cantidad, $imagen)) {
        echo "<script>alert('Repuesto agregado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al agregar el repuesto: ');</script>" . $conn->error;
    }
}


// Procesamiento de formulario para eliminar repuesto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_repuesto"])) {
    $serialEliminar = $_POST["serial"];

    if (eliminarRepuesto($serialEliminar)) {
        echo "<script>alert('Repuesto eliminado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el repuesto: ');</script>" . $conn->error;
    }
}

// Obtener y mostrar la lista de repuestos
$repuestos = obtenerRepuestos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Repuestos</title>
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
    <h3 class="center-align card-panel blue lighten-2">Repuestos</h3>

    <!-- Formulario para agregar repuesto -->
    <div class="row">
        <div class="col l8 s12 m6 offset-l2">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Agregar Repuesto</span>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                        <div class="input-field">
                            <input id="serial" type="text" name="serial" required>
                            <label for="serial">Serial</label>
                        </div>
                        <div class="input-field">
                            <input id="marca" type="text" name="marca" required>
                            <label for="marca">Marca</label>
                        </div>
                        <div class="input-field">
                            <input id="nombre" type="text" name="nombre" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="input-field">
                            <input id="cantidad" type="number" name="cantidad" required>
                            <label for="cantidad">Cantidad</label>
                        </div>
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Imagen</span>
                                <input type="file" name="imagen" accept="image/*">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="agregar_repuesto">Agregar Repuesto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de repuestos -->
    <div class="row">
        <div class="col s12">
        <div class="right">
        <a href="fpdf\reporteR.php"target="_blank"><i class="material-icons left">picture_as_pdf</i>Reporte PDF</a><hr>
        </div>
            <table class="striped">
                <thead>
                <tr>
                    <th class='text-center'>#</th>
                    <th class='text-center'>Serial</th>
                    <th class='text-center'>Marca</th>
                    <th class='text-center'>Nombre</th>
                    <th class='text-center'>Cantidad</th>
                    <th class=''>Imagen</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $N_contador = 1;
                foreach ($repuestos as $repuesto) {
                    echo "<tr>";
                    echo "<td class='text-center'>" . $N_contador . "</td>";
                    echo "<td class='text-center'>" . $repuesto['serial'] . "</td>";
                    echo "<td class='text-center'>" . $repuesto['marca'] . "</td>";
                    echo "<td class='text-center'>" . $repuesto['nombre'] . "</td>";
                    echo "<td class='text-center'>" . $repuesto['cantidad'] . "</td>";
                    echo "<td class=''><img class='materialboxed center' src='" . $repuesto['imagen'] . "' alt='Imagen de repuesto' style='max-width: 100px; max-height: 100px;'></td>";
                    echo "<td class='text-center'>
                            <form action='editarR.php' method='get'>
                                <input type='hidden' name='serial' value='" . $repuesto['serial'] . "'>
                                <button class='btn waves-effect waves-light' type='submit' name='editar_repuesto'>Editar</button>
                            </form>

                        </td>";
                    echo "<td class='text-center'>
                        <form action='" . $_SERVER['PHP_SELF'] . "' method='post'>
                            <input type='hidden' name='serial' value='" . $repuesto['serial'] . "'>
                            <button class='btn red waves-effect waves-light' type='submit' name='eliminar_repuesto'>Eliminar</button>
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
<script>

    document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.materialboxed');
    var instances = M.Materialbox.init(elems, );
  });
</script>
</body>
</html>
