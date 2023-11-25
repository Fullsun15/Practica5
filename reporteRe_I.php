<?php
require('fpdf\fpdf.php');
require('phpqrcode\qrlib.php');
include('conexion.php'); // Asegúrate de que esta línea esté presente para incluir el archivo de conexión
// Obtener datos del cliente
function obtenerClientePorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM clientes_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener datos del mecánico
function obtenerMecanicoPorCedula($cedula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM mecanicos_icarplus WHERE cedula = ?");
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener datos del vehículo
function obtenerVehiculoPorMatricula($matricula) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM vehiculos_icarplus WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener datos del repuesto
function obtenerRepuestoPorSerial($serial) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM repuestos_icarplus WHERE serial = ?");
    $stmt->bind_param("s", $serial);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener datos del registro
function obtenerRegistroPorID($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM registros_icarplus WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener el ID del registro de la URL
if (isset($_GET['id'])) {
    $idRegistro = $_GET['id'];

    // Obtener detalles del registro
    $registro = obtenerRegistroPorID($idRegistro);

    // Verificar si el registro existe
    if (!$registro) {
        echo "Registro no encontrado.";
        exit();
    }

    // Obtener información adicional
    $vehiculo = obtenerVehiculoPorMatricula($registro['matricula_vehiculos']);
    $cliente = obtenerClientePorCedula($vehiculo['cedula_cliente']);
    $mecanico = obtenerMecanicoPorCedula($registro['cedula_mecanicos']);
    $repuesto = obtenerRepuestoPorSerial($registro['serial_repuestos']);

    // Crear la factura con FPDF
    class PDF extends FPDF
    {
        private $fechaIngreso;
        function SetFechaIngreso($fechaIngreso)
        {
            $this->fechaIngreso = $fechaIngreso;
        }
        function Header()
        {
            // Encabezado de la factura
            $this->Image('fpdf\icon.png', 185, 5, 20); // Reemplaza con la ruta correcta de tu logo
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
    
            $this->Ln(4);

                // Agregar la fecha de ingreso al encabezado
            $this->Cell(0, 10, utf8_decode('Fecha de Ingreso: ' . $this->fechaIngreso), 0, 1, '');
            $this->SetTextColor(103);
            $this->Ln(0);
        }
    
        function Footer()
        {
            // Pie de la factura
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo()), 0, 0, 'C');
        }
    
        function ChapterTitle($title)
        {
            // Título del capítulo
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, utf8_decode($title), 0, 1, 'L');
            $this->Ln(2);
        }
    
        function ChapterBody($body)
        {
            // Contenido del capítulo
            $this->SetFont('Arial', '', 12);
            $this->MultiCell(0, 10, utf8_decode($body), 0, 'C');
            $this->Ln(1);
        }

        function AddQRCode($text)
        {
            // Añadir un código QR al PDF en el lado derecho
            $this->Ln(2);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, utf8_decode('Código QR del Registro:'), 0, 1, 'R');
        
            // Calcular la posición X para centrar la imagen en el lado derecho
            $imageX = $this->GetX() + 154; // Ajusta el valor según sea necesario
            $imageY = $this->GetY() + -2;
        
            $this->Image($this->GenerateQRCode($text), $imageX, $imageY, 30, 30);
            $this->Ln(2);
        }
    
        function GenerateQRCode($text)
        {
            // Generar el código QR y devolver la ruta de la imagen generada
            $tempDir = 'temp/';  // Ajusta la ruta según tu configuración
            $fileName = 'qrcode.png';
            $filePath = $tempDir . $fileName;
    
            // Asegurarse de que el directorio temporal exista
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
    
            QRcode::png($text, $filePath, QR_ECLEVEL_L, 3, 2);
    
            return $filePath;
        }
    }

    // Crear instancia de PDF
    $pdf = new PDF();
    $pdf->SetFechaIngreso($registro['fecha_ingreso']); // Establecer la fecha de ingreso
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(50);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(100, 10, utf8_decode('Detalles del Registro: #' . $registro['id']), 0, 1, 'C', 0);
    $pdf->Ln(0);
    $pdf->Cell(0, 10, '-----------------------------------------------------------------------------------------------------------', 0, 1, 'C');

    // Agregar código QR
    $pdf->AddQRCode('Detalles del Registro: #' . $registro['id'] . "\nCliente: " . $cliente['nombre'] . ' ' . $cliente['apellido'] . "\nCédula: " . $cliente['cedula'] . "\nMatrícula Vehículo: " . $vehiculo['matricula'] . "\nModelo Vehículo: " . $vehiculo['modelo'] . "\nDescripción: " . $vehiculo['descripcion'] . "\nNombre Mecánico: " . $mecanico['nombre'] . ' ' . $mecanico['apellido'] . "\nCédula Mecánico: " . $mecanico['cedula'] . "\nSerial Repuesto: " . $repuesto['serial'] . "\nNombre Repuesto: " . $repuesto['nombre'] . "\nCantidad de Repuestos: " . $registro['cantidad_repuestos'] . "\nFecha de Ingreso: " . $registro['fecha_ingreso']);

    // Agregar contenido a la factura
    $pdf->ChapterTitle('Información del Cliente:');
    $pdf->ChapterBody('Nombre: ' . $cliente['nombre'] . ' ' . $cliente['apellido'] . "\nCédula: " . $cliente['cedula']);
    $pdf->Cell(0, 10, '-----------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->ChapterTitle('Información del Vehículo:');
    $pdf->ChapterBody('Matrícula: ' . $vehiculo['matricula'] . "\nModelo: " . $vehiculo['modelo']);
    $pdf->Cell(0, 10, '-----------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->ChapterTitle('Información del Mecánico:');
    $pdf->ChapterBody('Nombre: ' . $mecanico['nombre'] . ' ' . $mecanico['apellido'] . "\nCédula: " . $mecanico['cedula']);
    $pdf->Cell(0, 10, '-----------------------------------------------------------------------------------------------------------', 0, 1, 'C');
    $pdf->ChapterTitle('Información del Repuesto:');
    $pdf->ChapterBody('Serial: ' . $repuesto['serial'] . "\nNombre: " . $repuesto['nombre'] . "\nCantidad de Repuestos: " . $registro['cantidad_repuestos'] );


    // Generar PDF
    $pdf->Output();
} else {
    echo "ID de registro no proporcionado.";
    exit();
}
?>
