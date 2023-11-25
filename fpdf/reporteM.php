<?php

require('fpdf.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$sql = "SELECT * FROM mecanicos_icarplus"; // Reemplaza 'tu_tabla' con el nombre de tu tabla
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "No se encontraron datos en la base de datos.";
    exit();
}

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('Icon.png', 185, 5, 20); // Reemplaza con la ruta correcta de tu logo
        $this->SetFont('Arial', 'B', 19);
        $this->Cell(45);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(110, 15, utf8_decode('iCar Plus'), 1, 1, 'C', 0);
        $this->Ln(3);
        $this->SetTextColor(103);

        $this->Cell(110);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(96, 10, utf8_decode("Ubicación : URBE "), 0, 0, '', 0);
        $this->Ln(5);

        $this->Cell(110);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(59, 10, utf8_decode("Teléfono : 0412-555-5555 "), 0, 0, '', 0);
        $this->Ln(5);

        $this->Cell(110);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(85, 10, utf8_decode("Correo : iCarPlus@gmailcom"), 0, 0, '', 0);
        $this->Ln(10);

        $this->Ln(10);

        $this->SetTextColor(0, 0, 0);
        $this->Cell(50);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(100, 10, utf8_decode("Reporte de Mecanicos "), 0, 1, 'C', 0);
        $this->Ln(10);

        $this->SetFillColor(229, 57, 53);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(13, 10, utf8_decode('N°'), 1, 0, 'C', 1);
        $this->Cell(28, 10, utf8_decode('Cedula'), 1, 0, 'C', 1);
        $this->Cell(35, 10, utf8_decode('Nombre'), 1, 0, 'C', 1);
        $this->Cell(35, 10, utf8_decode('Apellido'), 1, 0, 'C', 1);
        $this->Cell(15, 10, utf8_decode('Edad'), 1, 0, 'C', 1);
        $this->Cell(30, 10, utf8_decode('Licencia'), 1, 0, 'C', 1);
        $this->Cell(33, 10, utf8_decode('Especialidad'), 1, 1, 'C', 1);
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



$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(0, 0, 0);

$N_contador = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(13, 10, $N_contador, 1, 0, 'C'); // Centra el número
    $pdf->Cell(28, 10, utf8_decode($row['cedula']), 1, 0, 'C');
    $pdf->Cell(35, 10, utf8_decode($row['nombre']), 1, 0, 'C');
    $pdf->Cell(35, 10, utf8_decode($row['apellido']), 1, 0, 'C');
    $pdf->Cell(15, 10, utf8_decode($row['edad']), 1, 0, 'C');
    $pdf->Cell(30, 10, utf8_decode($row['licencia']), 1, 0, 'C');
    $pdf->Cell(33, 10, utf8_decode($row['especialidad']), 1, 1, 'C');
    $N_contador++;
}

$pdf->Output();
