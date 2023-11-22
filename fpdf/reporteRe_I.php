<?php

require('fpdf.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "botellones";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$sql = "SELECT * FROM pedidos"; // Reemplaza 'tu_tabla' con el nombre de tu tabla
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
        $this->Cell(110, 15, utf8_decode('Embotelladora Thomsom'), 1, 1, 'C', 0);
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
        $this->Cell(85, 10, utf8_decode("Correo : EmbotelladoraThomsom@gmailcom"), 0, 0, '', 0);
        $this->Ln(10);

        $this->Ln(10);

        $this->SetTextColor(0, 0, 0);
        $this->Cell(50);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(100, 10, utf8_decode("Reporte de Clientes "), 0, 1, 'C', 0);
        $this->Ln(10);

        $this->SetFillColor(0, 128, 255);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 10, utf8_decode('ID'), 1, 0, 'C', 1);
        $this->Cell(40, 10, utf8_decode('Cedula Cliente'), 1, 0, 'C', 1);
        $this->Cell(60, 10, utf8_decode('Cantidad de botellas'), 1, 0, 'C', 1);
        $this->Cell(60, 10, utf8_decode('Fecha del llenado'), 1, 1, 'C', 1);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(0, 0, 0);

$N_contador = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(20, 10, utf8_decode($row['id']), 1, 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($row['cedula_cliente']), 1, 0, 'C');
    $pdf->Cell(60, 10, utf8_decode($row['cantidad_bote']), 1, 0, 'C');
    $pdf->Cell(60, 10, utf8_decode($row['fecha_llenado']), 1, 1, 'C');
    $N_contador++;
}

$pdf->Output();
