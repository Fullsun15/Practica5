<?php
include('conexion.php');

function obtenerClientePorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientes_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function editarCliente($cedula, $nuevaCedula, $nombre, $apellido, $edad, $licencia) {
    global $conn;
    $stmt = $conn->prepare("UPDATE clientes_icarplus SET cedula = ?, nombre = ?, apellido = ?, edad = ?, licencia = ? WHERE cedula = ?");
    $stmt->bind_param("isssii", $nuevaCedula, $nombre, $apellido, $edad, $licencia, $cedula);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_cliente"])) {
    $cedula = $_POST["cedula"];
    $nuevaCedula = $_POST["nuevaCedula"];
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
    $apellido = isset($_POST["apellido"]) ? $_POST["apellido"] : '';
    $edad = isset($_POST["edad"]) ? $_POST["edad"] : '';
    $licencia = isset($_POST["licencia"]) ? $_POST["licencia"] : '';

    if (editarCliente($cedula, $nuevaCedula, $nombre, $apellido, $edad, $licencia)) {
        echo "Cliente editado con éxito.";
        header("Location: clientes.php");
        exit();
    } else {
        echo "Error al editar el cliente.";
    }
}

if (isset($_GET['cedula'])) {
    $cedulaEditar = $_GET['cedula'];
    $clienteEditar = obtenerClientePorCedula($cedulaEditar);

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar Plus - Editar Cliente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>

<div class="container">
    <h3 class="center-align">Editar Cliente</h3>

    <div class="row">
        <div class="col l8 s12 m6 offset-l2">
            <div class="card">
                <div class="card-content">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <input type="hidden" name="cedula" value="<?php echo $clienteEditar['cedula']; ?>">
                        <div class="input-field">
                            <input id="nuevaCedula" type="text" name="nuevaCedula" value="<?php echo $clienteEditar['cedula']; ?>" required>
                            <label for="nuevaCedula">Nueva Cedula</label>
                        </div>
                        <div class="input-field">
                            <input id="nombre" type="text" name="nombre" value="<?php echo $clienteEditar['nombre']; ?>" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="input-field">
                            <input id="apellido" type="text" name="apellido" value="<?php echo $clienteEditar['apellido']; ?>" required>
                            <label for="apellido">Apellido</label>
                        </div>
                        <div class="input-field">
                            <input id="edad" type="number" name="edad" value="<?php echo $clienteEditar['edad']; ?>" required>
                            <label for="edad">Edad</label>
                        </div>
                        <div class="input-field">
                            <input id="licencia" type="text" name="licencia" value="<?php echo $clienteEditar['licencia']; ?>" required>
                            <label for="licencia">Licencia</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="editar_cliente">Editar Cliente</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
