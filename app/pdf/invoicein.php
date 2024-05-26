<?php
	
	$peticion_ajax=true;
	$code=(isset($_GET['code'])) ? $_GET['code'] : 0;

	/*---------- Incluyendo configuraciones ----------*/
	require_once "../../config/app.php";
    require_once "../../autoload.php";

	/*---------- Instancia al controlador venta ----------*/
	use app\controllers\ProductController;
    $productController = new ProductController();

    // Obtener productos por categoría
    $productsByCategory = $productController->getProductsByCategory();

    // Generar PDF
    generatePDF($productsByCategory);


    function generatePDF($productsByCategory) {
        require "./code128.php";


        $pdf = new FPDF();
        $pdf->AddPage();
    
        // Agrega los productos por categoría al PDF
        foreach ($productsByCategory as $category => $products) {
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, $category, 0, 1, 'C');
            $pdf->Ln(5);
    
            foreach ($products as $product) {
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 10, $product, 0, 1);
            }
    
            $pdf->Ln(10);
        }
    
        // Guarda el PDF en el servidor
        $pdf->Output("products_report.pdf", "F");
    }
    
    ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title><?php echo APP_NAME; ?></title>
	<?php include '../views/inc/head.php'; ?>
</head>
<body>
	<div class="main-container">
        <section class="hero-body">
            <div class="hero-body">
                <p class="has-text-centered has-text-white pb-3">
                    <i class="fas fa-rocket fa-5x"></i>
                </p>
                <p class="title has-text-white">¡Ocurrió un error!</p>
                <p class="subtitle has-text-white">No hemos encontrado datos de la venta</p>
            </div>
        </section>
    </div>
	<?php include '../views/inc/script.php'; ?>
</body>
</html>
<?php } ?>