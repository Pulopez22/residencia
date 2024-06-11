<?php
// Incluir las configuraciones y el archivo de autoloading
require_once "../../config/app.php";
require_once "../../autoload.php";

// Importar la clase ProductController
use app\controllers\ProductController;

// Obtener el parámetro de categoría (si está presente en la solicitud GET)
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

// Verificar si se especificó una categoría
if ($categoria !== null) {
    // Crear una instancia del controlador de productos
    $productController = new ProductController();

    // Crear una instancia de la clase mysqli para conectarse a la base de datos
    $conexion = new \mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    // Verificar si la conexión fue exitosa
    if ($conexion->connect_error) {
        die("Error en la conexión: " . $conexion->connect_error);
    }

    // Mostrar la categoría que se está pasando (para propósitos de depuración)
    echo "Categoría seleccionada: " . $categoria . "<br>";

    // Obtener el inventario por categoría y pasar la conexión como argumento
    $inventoryByCategory = $productController->getInventoryByCategory($conexion, $categoria);

    // Mostrar el inventario obtenido (para propósitos de depuración)
    echo "Inventario por categoría:<br>";
    print_r($inventoryByCategory);
    echo "<br>";

    // Cerrar la conexión a la base de datos después de usarla
    $conexion->close();

    // Verificar si se obtuvo el inventario por categoría correctamente
    if ($inventoryByCategory) {
        // Generar el PDF con el inventario por categoría
        generatePDF("inventory_report_" . $categoria . ".pdf", $inventoryByCategory);
    } else {
        // Si no se obtuvo el inventario por categoría, mostrar un mensaje de error
        echo "No se pudo obtener el inventario por categoría.";
    }
} else {
    // Si no se especificó ninguna categoría, mostrar un mensaje de error
    echo "No se especificó ninguna categoría.";
}

// Función para generar el PDF
function generatePDF($filename, $data) {
    // Incluir la clase FPDF
    require "./fpdf.php";

    // Crear una instancia de FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Encabezado del reporte
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Inventario por categoría', 0, 1, 'C');
    $pdf->Ln(10);

    // Crear la tabla para mostrar el inventario por categoría
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(70, 10, 'Categoria', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Stock Actual', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    foreach ($data as $category => $total) {
        $pdf->Cell(70, 10, utf8_decode($category), 1, 0, 'L');
        $pdf->Cell(50, 10, $total, 1, 1, 'C');
    }

    // Guardar el PDF en el servidor
    $pdf->Output($filename, "D");
}
?>
