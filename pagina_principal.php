<?php
session_start();

// Verificar si el usuario ha iniciado sesión. Si no, redirigirlo al formulario de inicio de sesión.
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}
include('conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>iCar Plus - Inicio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <link href="assets\css\estilo.css" type="text/css" rel="stylesheet" media="screen,projection"/>

  <style>
    body{
      background-color: gainsboro;
    }

    #parallax-inicio p {
      font-family: arial black;
      font-size: 35px;
      -webkit-text-stroke: 1.5px black;
      text-align: center;
    }

    /* Agregar bordes blancos a la tarjeta */
    .card.with-border {
      border: 4px solid white;
      border-radius: 7px; /* Ajusta según sea necesario */
    }


    /* Estilo para la imagen dentro de la carta */
    .card-image img {
      max-height: 100%;
      max-width: 100%;
      width: auto;
      height: auto;
    }

    /* Ajusta la altura de las cartas de "Bienvenido a iCar Plus" */
    .welcome-cards {
      height: 400px; /* Ajusta la altura según sea necesario */
    }
  </style>
</head>
<body>

  <nav class="red darken-2">
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo left">iCar Plus</a>
      <ul id="nav-mobile" class="right">
        <li><a href="#">Inicio</a></li>
        <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
      </ul>
    </div>
  </nav>

  <div id="parallax-inicio" class="parallax-container">
    <div class="parallax"><img src="assets\img\parallax1.jpg"></div>

    <div class="container">
      <div class="row">
        <div class="col s12">
          <p class="parallax-text-left white-text">El concesionario experto en reparación y mantenimiento</p>
        </div>
      </div>
      <!-- Nueva fila para la carta con la imagen dentro del parallax -->
      <div class="row">
        <div class="col l5 s12 m8 right">
          <div class="card with-border">
            <div class="card-image">
              <img src="assets\img\img1.jpg" alt="Descripción de la imagen">
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="container">
    <h3 class="center-align">Bienvenido a iCar Plus</h3>
    <div class="row">
      <!-- Cartas para la primera fila -->
      <div class="col s12 m4">
        <div class="card  welcome-cards">
          <div class="card-image">
            <a href="clientes.php"><img src="assets\img\clientes.png" alt="Descripción 1"></a>
          </div>
          <div class="card-content black-text blue lighten-2">
            <span class="card-title center">Gestionar Clientes</span>
          </div>
        </div>
      </div>

      <div class="col s12 m4">
        <div class="card  welcome-cards">
          <div class="card-image">
            <a href="mecanicos.php"><img src="assets\img\mecanicos.png" alt="Descripción 2"></a>
          </div>
          <div class="card-content black-text blue lighten-2">
            <span class="card-title center ">Gestionar Mecanicos</span>
          </div>
        </div>
      </div>

      <div class="col s12 m4">
        <div class="card welcome-cards">
          <div class="card-image">
            <a href="repuestos.php"><img src="assets\img\repuestos.png" alt="Descripción 3"></a>
          </div>
          <div class="card-content black-text blue lighten-2">
            <span class="card-title center">Gestionar Repuestos</span>
          </div>
        </div>
      </div>

      <!-- Cartas para la segunda fila -->
      <div class="col s12 m4 offset-l2">
        <div class="card welcome-cards">
          <div class="card-image">
            <a href="vehiculos.php"><img src="assets\img\vehiculos.png" alt="Descripción 4"></a>
          </div>
          <div class="card-content black-text blue lighten-2">
            <span class="card-title center">Gestionar Vehiculos</span>
          </div>
        </div>
      </div>


      <div class="col s12 m4">
        <div class="card welcome-cards">
          <div class="card-image">
            <a href="registros.php"><img src="assets\img\registros.png" alt="Descripción 6"></a>
          </div>
          <div class="card-content black-text blue lighten-2">
            <span class="card-title center">Registros</span>
          </div>
        </div>
      </div>
    </div>
  </div>
<br><br>
  <footer class="page-footer red darken-2">
        <div class="footer-copyright">
            <div class="container">
                <p>Copyright © 2023 rubilopez.site</p>
              </div>
              <div></div>
              <a class="center" href="https://github.com/Fullsun15/Practica5.git" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" width="30px" height="30px"></a>
        </div>
    </footer>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <script src="assets\js\init.js" ></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var elems = document.querySelectorAll('.parallax');
      var instances = M.Parallax.init(elems);
    });
  </script>
</body>
</html>
