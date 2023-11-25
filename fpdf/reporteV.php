<?php

require('fpdf.php'); // Reemplaza la ruta por la correcta si es diferente

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$sql = "SELECT * FROM vehiculos_icarplus"; // Reemplaza 'clientes' con el nombre de tu tabla
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "No se encontraron datos en la base de datos.";
    exit(); // Sale del script si no hay datos
}


class PDF extends FPDF
{
   // Cabecera de página
   function Header()
   {
      $this->Image('Icon.png', 260, 5, 20); // Reemplaza con la ruta correcta de tu logo
      $this->SetFont('Arial', 'B', 19);
      $this->Cell(45);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(175, 15, utf8_decode('iCar Plus'), 1, 1, 'C', 0);
      $this->Ln(3);
      $this->SetTextColor(103);

      /* UBICACIÓN */
      $this->Cell(180);
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(96, 10, utf8_decode("Ubicación : URBE "), 0, 0, '', 0);
      $this->Ln(5);

      /* TELÉFONO */
      $this->Cell(180);
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(59, 10, utf8_decode("Teléfono : 0412-555-5555 "), 0, 0, '', 0);
      $this->Ln(5);

      /* CORREO */
      $this->Cell(180);
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(85, 10, utf8_decode("Correo : iCarPlus@gmailcom"), 0, 0, '', 0);
      $this->Ln(10);

      $this->Ln(10);

      /* TÍTULO DE LA TABLA */
      $this->SetTextColor(0, 0, 0);
      $this->Cell(50);
      $this->SetFont('Arial', 'B', 17);
      $this->Cell(170, 10, utf8_decode("Reporte de Vehiculos "), 0, 1, 'C', 0);
      $this->Ln(10);


      /* ENCABEZADOS DE LA TABLA */
      $this->SetFillColor(229, 57, 53);
      $this->SetTextColor(255, 255, 255);
      $this->SetDrawColor(0, 0, 0);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(13, 10, utf8_decode('#'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Matricula'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Marca'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Modelo'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Tipo'), 1, 0, 'C', 1);
      $this->Cell(20, 10, utf8_decode('Año'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Clasificación'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Descripcion'), 1, 1, 'C', 1);
   }

   
   function Footer()
   {
      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)

      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, cursiva, tamañoTexto
      $hoy = date('d/m/Y');
      $this->Cell(355, 10, utf8_decode($hoy), 0, 0, 'C'); // pie de pagina(fecha de pagina)
   }
   
   
}

$pdf = new PDF('P', 'mm', 'A3');
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(0, 0, 0);

// Aquí deberías obtener los datos de la base de datos y agregarlos al PDF, similar al segundo código

// Por ejemplo, asumiendo que tienes un array de datos llamado $data
$N_contador = 1;
foreach ($data as $row) {
    $pdf->Cell(13, 10, $N_contador, 1, 0, 'C'); // Centra el número
    $pdf->Cell(40, 10, utf8_decode($row['matricula']), 1, 0, 'C'); // Centra la cédula
    $pdf->Cell(40, 10, utf8_decode($row['marca']), 1, 0, 'C'); // Centra el nombre
    $pdf->Cell(40, 10, utf8_decode($row['modelo']), 1, 0, 'C'); // Centra el apellido
    $pdf->Cell(40, 10, utf8_decode($row['tipo']), 1, 0, 'C');
    $pdf->Cell(20, 10, utf8_decode($row['ano']), 1, 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($row['clasificacion']), 1, 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($row['descripcion']), 1, 1, 'C');
    $N_contador++;
}

$pdf->Output();
