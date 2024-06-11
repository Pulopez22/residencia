<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\clientController;

    if (isset($_POST['modulo_cliente'])) {
        $insCliente = new clientController();

        if ($_POST['modulo_cliente'] == "registrar") {
            echo json_encode($insCliente->registrarClienteControlador());
        } elseif ($_POST['modulo_cliente'] == "eliminar") {
            echo json_encode($insCliente->eliminarClienteControlador());
        } elseif ($_POST['modulo_cliente'] == "actualizar") {
            echo json_encode($insCliente->actualizarClienteControlador());
        } else {
            throw new Exception('Acción no válida.');
        }
    } else {
        throw new Exception('Módulo de cliente no especificado.');
    }
?>
