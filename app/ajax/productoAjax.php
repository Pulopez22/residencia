<?php
	
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\productController;

	// Asegurémonos de que estamos devolviendo JSON
	header('Content-Type: application/json');

	if(isset($_POST['modulo_producto'])){

		$insProducto = new productController();
		$response = null;

		switch($_POST['modulo_producto']){
			case "registrar":
				$response = $insProducto->registrarProductoControlador();
				break;
			case "eliminar":
				$response = $insProducto->eliminarProductoControlador();
				break;
			case "actualizar":
				$response = $insProducto->manejarActualizacionProducto();
				break;
			case "eliminarFoto":
				$response = $insProducto->eliminarFotoProductoControlador();
				break;
			case "actualizarFoto":
				$response = $insProducto->actualizarFotoProductoControlador();
				break;
			default:
				$response = array("status" => "error", "message" => "Módulo de producto desconocido");
				break;
		}

		echo json_encode($response);
		
	}else{
		$response = array("status" => "error", "message" => "Solicitud no válida");
		echo json_encode($response);
	}
?>
