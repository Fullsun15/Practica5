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

$sql = "SELECT * FROM cliente"; // Reemplaza 'clientes' con el nombre de tu tabla
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
      $this->Image('Icon.png', 185, 5, 20); // Reemplaza con la ruta correcta de tu logo
      $this->SetFont('Arial', 'B', 19);
      $this->Cell(45);
      $this->SetTextColor(0, 0, 0);
      $this->Cell(110, 15, utf8_decode('Embotelladora Thomsom'), 1, 1, 'C', 0);
      $this->Ln(3);
      $this->SetTextColor(103);

      /* UBICACIÓN */
      $this->Cell(110);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(96, 10, utf8_decode("Ubicación : URBE "), 0, 0, '', 0);
      $this->Ln(5);

      /* TELÉFONO */
      $this->Cell(110);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(59, 10, utf8_decode("Teléfono : 0412-555-5555 "), 0, 0, '', 0);
      $this->Ln(5);

      /* CORREO */
      $this->Cell(110);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(85, 10, utf8_decode("Correo : EmbotelladoraThomsom@gmailcom"), 0, 0, '', 0);
      $this->Ln(10);

      $this->Ln(10);

      /* TÍTULO DE LA TABLA */
      $this->SetTextColor(0, 0, 0);
      $this->Cell(50);
      $this->SetFont('Arial', 'B', 15);
      $this->Cell(100, 10, utf8_decode("Reporte de Clientes "), 0, 1, 'C', 0);
      $this->Ln(10);

      /* ENCABEZADOS DE LA TABLA */
      $this->SetFillColor(0, 128, 255);
      $this->SetTextColor(255, 255, 255);
      $this->SetDrawColor(0, 0, 0);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(29, 10, utf8_decode('#'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Cédula'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Nombre'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Apellido'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('Zona/País'), 1, 1, 'C', 1);
   }
   
   
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(0, 0, 0);

// Aquí deberías obtener los datos de la base de datos y agregarlos al PDF, similar al segundo código

// Por ejemplo, asumiendo que tienes un array de datos llamado $data
$N_contador = 1;
foreach ($data as $row) {
    $pdf->Cell(29, 10, $N_contador, 1, 0, 'C'); // Centra el número
    $pdf->Cell(40, 10, utf8_decode($row['cedula']), 1, 0, 'C'); // Centra la cédula
    $pdf->Cell(40, 10, utf8_decode($row['nombre']), 1, 0, 'C'); // Centra el nombre
    $pdf->Cell(40, 10, utf8_decode($row['apellido']), 1, 0, 'C'); // Centra el apellido
    $pdf->Cell(40, 10, utf8_decode($row['zona_pais']), 1, 1, 'C'); // Centra la zona/país y salta de línea
    $N_contador++;
}

$pdf->Output();
