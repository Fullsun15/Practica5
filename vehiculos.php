<?php
include('conexion.php');

// Funciones de manejo de vehículos
function agregarVehiculo($matricula, $marca, $modelo, $tipo, $ano, $clasificacion, $descripcion, $imagen, $cedulaCliente) {
    global $conn;

    // Validar que la cédula del cliente exista en el sistema
    $stmtCedula = $conn->prepare("SELECT cedula FROM clientes_icarplus WHERE cedula = ?");
    $stmtCedula->bind_param("s", $cedulaCliente);
    $stmtCedula->execute();
    $stmtCedula->store_result();

    if ($stmtCedula->num_rows > 0) {
        // La cédula del cliente existe, proceder con el registro del vehículo
        $stmt = $conn->prepare("INSERT INTO vehiculos_icarplus (matricula, marca, modelo, tipo, ano, clasificacion, descripcion, imagen, cedula_cliente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissss", $matricula, $marca, $modelo, $tipo, $ano, $clasificacion, $descripcion, $imagen, $cedulaCliente);

        if ($stmt->execute()) {
            echo "Vehículo registrado con éxito.";
            return true;
        } else {
            echo "Error al registrar el vehículo: " . $conn->error;
            return false;
        }
    } else {
        // La cédula del cliente no existe, mostrar mensaje de error
        echo "La cédula del cliente no existe en el sistema. Por favor, verifique.";
        return false;
    }
}

function obtenerVehiculos() {
    global $conn;
    $result = $conn->query("SELECT * FROM vehiculos_icarplus");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function eliminarVehiculo($matricula) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM vehiculos_icarplus WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Procesamiento de formulario para agregar vehículo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_vehiculo"])) {
    $matricula = $_POST["matricula"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $tipo = $_POST["tipo"];
    $ano = $_POST["ano"];
    $clasificacion = $_POST["clasificacion"];
    $descripcion = $_POST["descripcion"];

    // Manejo de imagen (guardar en el servidor)
    $imagen = "img_Vehi/default.jpg"; // Imagen por defecto

    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $target_dir = "img_Vehi/";
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen real o un archivo falso
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check !== false) {
            // Permitir solo ciertos formatos de archivo
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                $imagen = $target_file;
                move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
            } else {
                echo "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
            }
        } else {
            echo "El archivo no es una imagen válida.";
        }
    }

    $cedulaCliente = $_POST["cedula_cliente"];

    agregarVehiculo($matricula, $marca, $modelo, $tipo, $ano, $clasificacion, $descripcion, $imagen, $cedulaCliente);
}

// Procesamiento de formulario para eliminar vehículo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_vehiculo"])) {
    $matriculaEliminar = $_POST["matricula"];

    eliminarVehiculo($matriculaEliminar);
}

// Obtener y mostrar la lista de vehículos
$vehiculos = obtenerVehiculos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Vehículos</title>
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
<br>
<div class="container">
    <h3 class="center-align card-panel blue lighten-2">Vehículos</h3>

    <!-- Formulario para agregar vehículo -->
    <div class="row">
        <div class="col l12 s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Agregar Vehículo</span>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                        <div class="input-field">
                            <input id="matricula" type="text" name="matricula" required>
                            <label for="matricula">Matrícula</label>
                        </div>
                        <div class="input-field">
                            <input id="marca" type="text" name="marca" required>
                            <label for="marca">Marca</label>
                        </div>
                        <div class="input-field">
                            <input id="modelo" type="text" name="modelo" required>
                            <label for="modelo">Modelo</label>
                        </div>
                        <div class="input-field">
                            <select id="tipo" name="tipo" required>
                                <option value="" disabled selected>Seleccione el tipo</option>
                                <option value="automatico">Automático</option>
                                <option value="sincronico">Sincrónico</option>
                            </select>
                            <label for="tipo">Tipo</label>
                        </div>
                        <div class="input-field">
                            <input id="ano" type="number" name="ano" required>
                            <label for="ano">Año</label>
                        </div>
                        <div class="input-field">
                            <select id="clasificacion" name="clasificacion" required>
                                <option value="" disabled selected>Seleccione la clasificación</option>
                                <option value="Segmento micro">Segmento micro</option>
                                <option value="Segmento A">Segmento A</option>
                                <option value="Segmento B">Segmento B</option>
                                <option value="Segmento C">Segmento C</option>
                                <option value="Segmento D">Segmento D</option>
                                <option value="Segmento E">Segmento E</option>
                                <option value="Segmento F">Segmento F</option>
                                <option value="Segmento J">Segmento J</option>
                                <option value="Segmento M">Segmento M</option>
                                <option value="Segmento S">Segmento S</option>
                            </select>
                            <label for="clasificacion">Clasificación</label>
                        </div>
                        <div class="input-field">
                            <input id="descripcion" type="text" name="descripcion" required>
                            <label for="descripcion">Descripción</label>
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
                        <div class="input-field">
                            <input id="cedula_cliente" type="text" name="cedula_cliente" required>
                            <label for="cedula_cliente">Cédula Cliente</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="agregar_vehiculo">Agregar Vehículo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
</div>
<!-- Lista de vehículos -->
<div class="row">
    <div class="col s12">
        <table class="striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Matrícula</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Tipo</th>
                <th>Año</th>
                <th>Clasificación</th>
                <th>Descripción</th>
                <th>Imagen</th>
                <th>Cédula Cliente</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $N_contador = 1;
            foreach ($vehiculos as $vehiculo) {
                echo "<tr>";
                echo "<td class='text-center'>" . $N_contador . "</td>";
                echo "<td class='text-center'>" . $vehiculo['matricula'] . "</td>";
                echo "<td class='text-center'>" . $vehiculo['marca'] . "</td>";
                echo "<td class='text-center'>" . $vehiculo['modelo'] . "</td>";
                echo "<td class='text-center'>" . $vehiculo['tipo'] . "</td>";
                echo "<td class='text-center'>" . $vehiculo['ano'] . "</td>";
                echo "<td class='text-center'>" . $vehiculo['clasificacion'] . "</td>";
                echo "<td class='text-center'>" . $vehiculo['descripcion'] . "</td>";
                echo "<td class='text-center'><img class='materialboxed' src='" . $vehiculo['imagen'] . "' alt='Imagen de vehículo' style='max-width: 100px; max-height: 100px;'></td>";
                echo "<td class='text-center'>" . $vehiculo['cedula_cliente'] . "</td>";
                echo "<td class='text-center'>
                        <form action='editarV.php' method='get'>
                            <input type='hidden' name='matricula' value='" . $vehiculo['matricula'] . "'>
                            <button class='btn waves-effect waves-light' type='submit' name='editar_vehiculo'>Editar</button>
                        </form>
                    </td>";
                echo "<td class='text-center'>
                        <form action='" . $_SERVER['PHP_SELF'] . "' method='post'>
                            <input type='hidden' name='matricula' value='" . $vehiculo['matricula'] . "'>
                            <button class='btn red waves-effect waves-light' type='submit' name='eliminar_vehiculo'>Eliminar</button>
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

<!-- Incluye los archivos JavaScript de Materialize -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="assets\js\init.js" ></script>
<script>

    document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.materialboxed');
    var instances = M.Materialbox.init(elems, );
  });
</script>
</body>
</html>
