<?php
session_start();

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo al formulario de inicio de sesión.
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}
include('conexion.php');
function agregarCliente($cedula, $nombre, $apellido, $edad, $licencia) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO clientes_icarplus (cedula, nombre, apellido, edad, licencia) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $cedula, $nombre, $apellido, $edad, $licencia);
    return $stmt->execute();
}

function obtenerClientePorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientes_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function eliminarCliente($cedula) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM clientes_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    return $stmt->execute();
}

function obtenerClientes() {
    global $conn;
    $result = $conn->query("SELECT * FROM clientes_icarplus");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$clienteEditar = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_cliente"])) {
    $cedulaEditar = $_POST["cedula"];
    $clienteEditar = obtenerClientePorCedula($cedulaEditar);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_cliente"])) {
    $cedula = $_POST["cedula"];
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
    $apellido = isset($_POST["apellido"]) ? $_POST["apellido"] : '';
    $edad = isset($_POST["edad"]) ? $_POST["edad"] : '';
    $licencia = isset($_POST["licencia"]) ? $_POST["licencia"] : '';

    if (agregarCliente($cedula, $nombre, $apellido, $edad, $licencia)) {
        echo "<script>alert('Cliente agregado con éxito');</script>";
    } else {
        echo "<script>alert('Error al agregar el cliente.');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_cliente"])) {
    $cedula = $_POST["cedula"];

    if (eliminarCliente($cedula)) {
        echo "<script>alert('Cliente eliminado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el cliente.');</script>";
    }
}

$clientes = obtenerClientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Clientes</title>
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
    <h3 class="center-align card-panel blue lighten-2">Clientes</h3>

    <div class="row">
        <div class="col l8 s12 m6 offset-l2">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Agregar Cliente</span>
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
                            <input id="edad" type="number" name="edad" min="18" max="99" required>
                            <label for="edad">Edad</label>
                        </div>
                        <div class="input-field">
                            <input id="licencia" type="number" name="licencia" min="0" max="9999999999999" required>
                            <label for="licencia">Campo Numérico (máx. 13 dígitos)</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="agregar_cliente">Agregar
                            Cliente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s12 l12"><br>
        <div class="right">
        <a href="fpdf\reporteC.php"target="_blank"><i class="material-icons left">picture_as_pdf</i>Reporte PDF</a><hr>
        </div>
        <table class="striped">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Cedula</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Apellido</th>
                    <th class="text-center">Edad</th>
                    <th class="text-center">Licencia</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $N_contador = 1;
                foreach ($clientes as $cliente) {
                    echo "<tr>";
                    echo "<td class='text-center'>" . $N_contador . "</td>";
                    echo "<td class='text-center'>" . $cliente['cedula'] . "</td>";
                    echo "<td class='text-center'>" . $cliente['nombre'] . "</td>";
                    echo "<td class='text-center'>" . $cliente['apellido'] . "</td>";
                    echo "<td class='text-center'>" . $cliente['edad'] . "</td>";
                    echo "<td class='text-center'>" . $cliente['licencia'] . "</td>";
                    echo "<td class='text-center'>
                            <form action='editarC.php' method='get'>
                                <input type='hidden' name='cedula' value='" . $cliente['cedula'] . "'>
                                <button class='btn waves-effect waves-light' type='submit' name='editar_cliente'>Editar</button>
                            </form>
                        </td>";
                    echo "<td class='text-center'>
                            <form action='" . $_SERVER['PHP_SELF'] . "' method='post'>
                                <input type='hidden' name='cedula' value='" . $cliente['cedula'] . "'>
                                <button class='btn red waves-effect waves-light' type='submit' name='eliminar_cliente'>Eliminar</button>
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

<footer class="page-footer red darken-2">
        <div class="footer-copyright">
            <div class="container">
                <p>Copyright © 2023 rubilopez.site</p>
              </div>
              <div></div>
              <a class="center" href="https://github.com/Fullsun15/Practica4.git" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" width="30px" height="30px"></a>
        </div>
    </footer>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="assets/js/init.js"></script>
</body>
</html>
