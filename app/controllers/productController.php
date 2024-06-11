<?php

	namespace app\controllers;
	use app\models\mainModel;

	class productController extends mainModel{

		/*----------  Controlador registrar producto  ----------*/
		public function registrarProductoControlador(){

			# Almacenando datos#
		    $codigo=$this->limpiarCadena($_POST['producto_codigo']);
		    $nombre=$this->limpiarCadena($_POST['producto_nombre']);

		    $precio_compra=$this->limpiarCadena($_POST['producto_precio_compra']);
		    $stock=$this->limpiarCadena($_POST['producto_stock']);

		    $marca=$this->limpiarCadena($_POST['producto_marca']);
		    $unidad=$this->limpiarCadena($_POST['producto_unidad']);
		    $categoria=$this->limpiarCadena($_POST['producto_categoria']);
			$fecha_registro = date('Y-m-d H:i:s');  // Obtener la fecha y hora actuales

		    # Verificando campos obligatorios #
            if($codigo == "" || $nombre == "" || $precio_compra == "" || $stock == "" || $categoria == "" || $unidad == ""){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado todos los campos que son obligatorios",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9]{1,77}",$codigo)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El CODIGO no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()]{1,100}",$nombre)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($this->verificarDatos("[0-9.]{1,25}",$precio_compra)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO DE COMPRA no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }	

		    if($this->verificarDatos("[0-9]{1,22}",$stock)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El STOCK O EXISTENCIAS no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($marca!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}",$marca)){
			    	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La MARCA no coincide con el formato solicitado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
			    }
		    }

		    # Comprobando presentacion del producto #
			if(!in_array($unidad, PRODUCTO_UNIDAD)){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La PRESENTACION DEL PRODUCTO no es correcta o no la ha seleccionado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			# Verificando categoria #
		    $check_categoria=$this->ejecutarConsulta("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
		    if($check_categoria->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La categoría seleccionada no existe en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    # Verificando stock total o existencias #
            if($stock<=0){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No puedes registrar un producto con stock o existencias en 0, debes de agregar al menos una unidad",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Comprobando precio de compra del producto #
            $precio_compra=number_format($precio_compra,MONEDA_DECIMALES,'.','');
            if($precio_compra<=0){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO DE COMPRA no puede ser menor o igual a 0",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

			# Comprobando codigo de producto #
		    $check_codigo=$this->ejecutarConsulta("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
		    if($check_codigo->rowCount()>=1){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El código de producto que ha ingresado ya se encuentra registrado en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    # Comprobando nombre de producto #
		    $check_nombre=$this->ejecutarConsulta("SELECT producto_nombre FROM producto WHERE producto_codigo='$codigo' AND producto_nombre='$nombre'");
		    if($check_nombre->rowCount()>=1){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Ya existe un producto registrado con el mismo nombre y código de barras",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    # Directorios de imagenes #
			$img_dir='../views/productos/';

			# Comprobar si se selecciono una imagen #
    		if($_FILES['producto_foto']['name']!="" && $_FILES['producto_foto']['size']>0){

    			# Creando directorio #
		        if(!file_exists($img_dir)){
		            if(!mkdir($img_dir,0777)){
		            	$alerta=[
							"tipo"=>"simple",
							"titulo"=>"Ocurrió un error inesperado",
							"texto"=>"Error al crear el directorio",
							"icono"=>"error"
						];
						return json_encode($alerta);
		                exit();
		            } 
		        }

		        # Verificando formato de imagenes #
		        if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La imagen que ha seleccionado es de un formato no permitido",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

		        # Verificando peso de imagen #
		        if(($_FILES['producto_foto']['size']/1024)>5120){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La imagen que ha seleccionado supera el peso permitido",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

		        # Nombre de la foto #
		        $foto=$codigo."_".rand(0,100);

		        # Extension de la imagen #
		        switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
		            case 'image/jpeg':
		                $foto=$foto.".jpg";
		            break;
		            case 'image/png':
		                $foto=$foto.".png";
		            break;
		        }

		        chmod($img_dir,0777);

		        # Moviendo imagen al directorio #
		        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"No podemos subir la imagen al sistema en este momento",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

    		}else{
    			$foto="";
    		}

    		$producto_datos_reg=[
				[
					"campo_nombre"=>"producto_codigo",
					"campo_marcador"=>":Codigo",
					"campo_valor"=>$codigo
				],
				[
					"campo_nombre"=>"producto_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"producto_stock_total",
					"campo_marcador"=>":Stock",
					"campo_valor"=>$stock
				],
				[
					"campo_nombre"=>"producto_tipo_unidad",
					"campo_marcador"=>":Unidad",
					"campo_valor"=>$unidad
				],
				[
					"campo_nombre"=>"producto_precio_compra",
					"campo_marcador"=>":PrecioCompra",
					"campo_valor"=>$precio_compra
				],
				[
					"campo_nombre"=>"producto_marca",
					"campo_marcador"=>":Marca",
					"campo_valor"=>$marca
				],
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				],
				[
					"campo_nombre"=>"categoria_id",
					"campo_marcador"=>":Categoria",
					"campo_valor"=>$categoria
				],
				[
					"campo_nombre"=>"fecha_registro",
					"campo_marcador"=>":FechaRegistro",
					"campo_valor"=>$fecha_registro
				]
			];

			$registrar_producto=$this->guardarDatos("producto",$producto_datos_reg);

			if($registrar_producto->rowCount()==1){
				$alerta=[
					"tipo"=>"limpiar",
					"titulo"=>"Producto registrado",
					"texto"=>"El producto se registro con exito",
					"icono"=>"success"
				];
			}else{
				
				if(is_file($img_dir.$foto)){
		            chmod($img_dir.$foto,0777);
		            unlink($img_dir.$foto);
		        }

				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No se pudo registrar el producto, por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
		}
		
		public function getInventoryByCategory($conexion) {
			// Consulta para obtener todo el inventario por categorías
			$sql = "SELECT categoria.categoria_nombre, SUM(producto.producto_stock_total) AS total_stock 
					FROM producto 
					INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id 
					GROUP BY categoria.categoria_nombre";
		
			// Ejecutar la consulta SQL
			$resultado = $conexion->query($sql);
		
			// Verificar si la consulta fue exitosa
			if (!$resultado) {
				die("Error en la consulta: " . $conexion->error);
			}
		
			// Inicializar un array para almacenar el inventario por categoría
			$inventoryByCategory = [];
		
			// Procesar el resultado de la consulta
			while ($fila = $resultado->fetch_assoc()) {
				$category = $fila['categoria_nombre'];
				$totalStock = $fila['total_stock'];
				$inventoryByCategory[$category] = $totalStock;
			}
		
			// Devolver el inventario por categoría
			return $inventoryByCategory;
		}
		
		public function getInventoryDetailsByCategory($conexion, $categoria) {
			// Consulta para obtener los detalles de los productos por categoría
			$sql = "SELECT producto_nombre, producto_stock_total 
					FROM producto 
					WHERE categoria_id = (SELECT categoria_id FROM categoria WHERE categoria_nombre = '$categoria')";
			
			// Ejecutar la consulta SQL
			$resultado = $conexion->query($sql);
		
			// Verificar si la consulta fue exitosa
			if (!$resultado) {
				die("Error en la consulta: " . $conexion->error);
			}
		
			// Inicializar un array para almacenar los detalles de los productos por categoría
			$inventoryDetailsByCategory = [];
		
			// Procesar el resultado de la consulta
			while ($fila = $resultado->fetch_assoc()) {
				$producto_nombre = $fila['producto_nombre'];
				$producto_stock_total = $fila['producto_stock_total'];
				$inventoryDetailsByCategory[$producto_nombre] = $producto_stock_total;
			}
		
			// Devolver los detalles de los productos por categoría
			return $inventoryDetailsByCategory;
		}
		
		

		/*----------  Controlador listar producto  ----------*/
		public function listarProductoControlador($pagina, $registros, $url, $busqueda, $categoria) {
			// Iniciar la sesión si no está ya iniciada
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
		
			// Obtener el nombre del usuario de la sesión   
			$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Desconocido';
		
			// Limpiar las entradas
			$pagina = $this->limpiarCadena($pagina);
			$registros = $this->limpiarCadena($registros);
			$categoria = $this->limpiarCadena($categoria);
			$url = $this->limpiarCadena($url);
		
			// Construir URL según la categoría
			if ($categoria > 0) {
				$url = APP_URL . $url . "/" . $categoria . "/";
			} else {
				$url = APP_URL . $url . "/";
			}
		
			$busqueda = $this->limpiarCadena($busqueda);
			$tabla = '<div class="table-container"><table class="table is-striped is-hoverable is-narrow">
						<thead>
							<tr>
								<th>Imagen</th>
								<th>Nombre</th>
								<th>Código</th>
								<th>Stock</th>
								<th>Categoría</th>
								<th>Modificado Por</th>
								<th>Último Movimiento</th>';
			// Añadir encabezado de acciones si hay una búsqueda
			if (isset($busqueda) && $busqueda != "") {
				$tabla .= '<th>Acciones</th>';
			}
			$tabla .= '</tr>
					</thead>
					<tbody>';
		
			// Calcular el inicio de la paginación
			$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
			$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
		
			// Definir los campos a seleccionar
			$campos = "producto.producto_id, producto.producto_codigo, producto.producto_nombre, producto_stock_total, producto.producto_foto, categoria.categoria_nombre, producto.fecha_registro AS fechas_registro, producto.usuario_modificacion";
		
			// Construir consultas según la búsqueda y categoría
			if (isset($busqueda) && $busqueda != "") {
				$consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%' OR producto_marca LIKE '%$busqueda%' ORDER BY producto_nombre ASC LIMIT $inicio, $registros";
				$consulta_total = "SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%' OR producto_marca LIKE '%$busqueda%'";
			} elseif ($categoria > 0) {
				$consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id WHERE producto.categoria_id='$categoria' ORDER BY producto.producto_nombre ASC LIMIT $inicio, $registros";
				$consulta_total = "SELECT COUNT(producto_id) FROM producto WHERE categoria_id='$categoria'";
			} else {
				$consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id ORDER BY producto_nombre ASC LIMIT $inicio, $registros";
				$consulta_total = "SELECT COUNT(producto_id) FROM producto";
			}
		
			// Ejecutar consultas
			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();
		
			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();
		
			// Calcular número de páginas
			$numeroPaginas = ceil($total / $registros);
		
			if ($total >= 1 && $pagina <= $numeroPaginas) {
				$contador = $inicio + 1;
				$pag_inicio = $inicio + 1;
				foreach ($datos as $rows) {
					$usuario_modificacion = ($rows['fechas_registro'] != $rows['usuario_modificacion']) ? $rows['usuario_modificacion'] : $usuario_nombre;
		
					$tabla .= '<tr>
									<td><img src="' . (is_file("./app/views/productos/" . $rows['producto_foto']) ? (APP_URL . 'app/views/productos/' . $rows['producto_foto']) : (APP_URL . 'app/views/productos/default.png')) . '" class="product-image is-rounded" style="max-width: 80px;"></td>
									<td>' . $rows['producto_nombre'] . '</td>
									<td>' . $rows['producto_codigo'] . '</td>
									<td>' . $rows['producto_stock_total'] . '</td>
									<td>' . $rows['categoria_nombre'] . '</td>
									<td>' . $usuario_modificacion . '</td>
									<td>' . $rows['fechas_registro'] . '</td>';
					// Agregar botones de acciones solo si hay una
					if (isset($busqueda) && $busqueda != "") {
						$tabla .= '<td>
										<div class="buttons">
											<a href="' . APP_URL . 'productPhoto/' . $rows['producto_id'] . '/" class="button is-info is-rounded is-small">
												<i class="far fa-image fa-fw"></i>
											</a>
											<a href="' . APP_URL . 'productUpdate/' . $rows['producto_id'] . '/" class="button is-success is-rounded is-small">
												<i class="fas fa-sync fa-fw"></i>
											</a>
											<form class="FormularioAjax" action="' . APP_URL . 'app/ajax/productoAjax.php" method="POST" autocomplete="off">
												<input type="hidden" name="modulo_producto" value="eliminar">
												<input type="hidden" name="producto_id" value="' . $rows['producto_id'] . '">
												<button type="submit" class="button is-danger is-rounded is-small">
													<i class="far fa-trash-alt fa-fw"></i>
												</button>
											</form>
										</div>
									</td>';
					}
					$tabla .= '</tr>';
					
					$contador++;
					}
					$pag_final = $contador - 1;
					} else {
					if ($total >= 1) {
						$tabla .= '
									<tr>
										<td colspan="8" class="has-text-centered"><i class="far fa-hand-point-down fa-5x"></i></td>
									</tr>
									<tr>
										<td colspan="8" class="has-text-centered">
											<a href="' . $url . '1/" class="button is-link is-rounded is-small mt-4 mb-4">
												Haga clic acá para recargar el listado
											</a>
										</td>
									</tr>';
					} else {
						$tabla .= '
									<tr>
										<td colspan="8" class="has-text-centered"><i class="far fa-grin-beam-sweat fa-5x"></i></td>
									</tr>
									<tr>
										<td colspan="8" class="has-text-centered">No hay productos registrados en esta categoría</td>
									</tr>';
					}
					}
					
					$tabla .= '</tbody></table></div>';

					$tabla = '<div class="table-container" style="margin: 0 auto; width: 80%;">' . $tabla . '</div>';

					### Paginacion ###
					if ($total > 0 && $pagina <= $numeroPaginas) {
    				$tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
    				$tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 7);
						}

					return $tabla;

					}		
	

		/*----------  Controlador eliminar producto  ----------*/
		public function eliminarProductoControlador(){

			$id=$this->limpiarCadena($_POST['producto_id']);

			# Verificando producto #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Verificando ventas #
		    $check_ventas=$this->ejecutarConsulta("SELECT producto_id FROM venta_detalle WHERE producto_id='$id' LIMIT 1");
		    if($check_ventas->rowCount()>0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos eliminar el producto del sistema ya que tiene ventas asociadas",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    $eliminarProducto=$this->eliminarRegistro("producto","producto_id",$id);

		    if($eliminarProducto->rowCount()==1){

		    	if(is_file("../views/productos/".$datos['producto_foto'])){
		            chmod("../views/productos/".$datos['producto_foto'],0777);
		            unlink("../views/productos/".$datos['producto_foto']);
		        }

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Producto eliminado",
					"texto"=>"El producto '".$datos['producto_nombre']."' ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				];

		    }else{
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el producto '".$datos['producto_nombre']."' del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    }

		    return json_encode($alerta);
		}


		/*----------  Controlador actualizar producto  ----------*/
		public function actualizarProductoControlador() {
			// Asegúrate de que la sesión esté iniciada
			session_start();
		
			// Obtén el nombre del usuario de la sesión
			$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Desconocido';
		
			$id = $this->limpiarCadena($_POST['producto_id']);
		
			// Verifica el producto
			$datos = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
			if ($datos->rowCount() <= 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No hemos encontrado el producto en el sistema",
					"icono" => "error"
				];
				return json_encode($alerta);
			} else {
				$datos = $datos->fetch();
			}
		
			// Almacena los nuevos datos del producto
			$nombre = $this->limpiarCadena($_POST['producto_nombre']);
			$precio_compra = $this->limpiarCadena($_POST['producto_precio_compra']);
			$stock = $this->limpiarCadena($_POST['producto_stock']);
		
			# Verificando campos obligatorios #
			if($nombre == "" || $precio_compra == "" || $stock == ""){
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No has llenado todos los campos que son obligatorios",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
		
			# Verificando integridad de los datos #
			if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}", $nombre)){
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El NOMBRE no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
		
			if($this->verificarDatos("[0-9.]{1,25}", $precio_compra)){
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El PRECIO DE COMPRA no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
		
			if($this->verificarDatos("[0-9]{1,22}", $stock)){
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El STOCK O EXISTENCIAS no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
		
			# Verificando stock total o existencias #
			if($stock <= 0){
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No puedes registrar un producto con stock o existencias en 0, debes de agregar al menos una unidad",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
		
			# Comprobando precio de compra del producto #
			$precio_compra = number_format($precio_compra, MONEDA_DECIMALES, '.', '');
			if($precio_compra <= 0){
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El PRECIO DE COMPRA no puede ser menor o igual a 0",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
		
			# Comprobando nombre de producto #
			if($datos['producto_nombre'] != $nombre){
				$check_nombre = $this->ejecutarConsulta("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
				if($check_nombre->rowCount() >= 1){
					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "Ya existe un producto registrado con el mismo nombre",
						"icono" => "error"
					];
					return json_encode($alerta);
				}
			}
		
			# Aquí debes definir $fecha_registro, 
			$fecha_registro = date("Y-m-d H:i:s"); 
		
			$producto_datos_up = [
				[
					"campo_nombre" => "producto_nombre",
					"campo_marcador" => ":Nombre",
					"campo_valor" => $nombre
				],
				[
					"campo_nombre" => "producto_stock_total",
					"campo_marcador" => ":Stock",
					"campo_valor" => $stock
				],
				[
					"campo_nombre" => "producto_precio_compra",
					"campo_marcador" => ":PrecioCompra",
					"campo_valor" => $precio_compra
				],
				[
					"campo_nombre" => "fecha_registro",
					"campo_marcador" => ":FechaRegistro",
					"campo_valor" => $fecha_registro
				],
				[
					"campo_nombre" => "usuario_modificacion",
					"campo_marcador" => ":UsuarioModificacion",
					"campo_valor" => $usuario_nombre
				]
			];
		
			$condicion = [
				"condicion_campo" => "producto_id",
				"condicion_marcador" => ":ID",
				"condicion_valor" => $id
			];
		
			if ($this->actualizarDatos("producto", $producto_datos_up, $condicion)) {
				$alerta = [
					"tipo" => "recargar",
					"titulo" => "Producto actualizado",
					"texto" => "Los datos del producto '".$datos['producto_nombre']."' se actualizaron correctamente",
					"icono" => "success"
				];
			} else {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No hemos podido actualizar los datos del producto '".$datos['producto_nombre']."', por favor intente nuevamente",
					"icono" => "error"
				];
			}
		
			return json_encode($alerta);
		}

		/*----------  Controlador eliminar foto producto  ----------*/
		public function eliminarFotoProductoControlador(){

			$id=$this->limpiarCadena($_POST['producto_id']);

			# Verificando producto #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Directorio de imagenes #
    		$img_dir="../views/productos/";

    		chmod($img_dir,0777);

    		if(is_file($img_dir.$datos['producto_foto'])){

		        chmod($img_dir.$datos['producto_foto'],0777);

		        if(!unlink($img_dir.$datos['producto_foto'])){
		            $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Error al intentar eliminar la foto del producto, por favor intente nuevamente",
						"icono"=>"error"
					];
					return json_encode($alerta);
		        	exit();
		        }
		    }else{
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado la foto del producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    $producto_datos_up=[
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>""
				]
			];

			$condicion=[
				"condicion_campo"=>"producto_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("producto",$producto_datos_up,$condicion)){
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto eliminada",
					"texto"=>"La foto del producto '".$datos['producto_nombre']."' se elimino correctamente",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto eliminada",
					"texto"=>"No hemos podido actualizar algunos datos del producto '".$datos['producto_nombre']."', sin embargo la foto ha sido eliminada correctamente",
					"icono"=>"warning"
				];
			}

			return json_encode($alerta);
		}


		/*----------  Controlador actualizar foto producto  ----------*/
		public function actualizarFotoProductoControlador(){

			$id=$this->limpiarCadena($_POST['producto_id']);

			# Verificando producto #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Directorio de imagenes #
    		$img_dir="../views/productos/";

    		# Comprobar si se selecciono una imagen #
    		if($_FILES['producto_foto']['name']=="" && $_FILES['producto_foto']['size']<=0){
    			$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No ha seleccionado una foto para el producto",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
    		}

    		# Creando directorio #
	        if(!file_exists($img_dir)){
	            if(!mkdir($img_dir,0777)){
	                $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Error al crear el directorio",
						"icono"=>"error"
					];
					return json_encode($alerta);
	                exit();
	            } 
	        }

	        # Verificando formato de imagenes #
	        if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
	            $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La imagen que ha seleccionado es de un formato no permitido",
					"icono"=>"error"
				];
				return json_encode($alerta);
	            exit();
	        }

	        # Verificando peso de imagen #
	        if(($_FILES['producto_foto']['size']/1024)>5120){
	            $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La imagen que ha seleccionado supera el peso permitido",
					"icono"=>"error"
				];
				return json_encode($alerta);
	            exit();
	        }

	        # Nombre de la foto #
	        if($datos['producto_foto']!=""){
		        $foto=explode(".", $datos['producto_foto']);
		        $foto=$foto[0];
	        }else{
	        	$foto=$datos['producto_codigo']."_".rand(0,100);
	        }
	        

	        # Extension de la imagen #
	        switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
	            case 'image/jpeg':
	                $foto=$foto.".jpg";
	            break;
	            case 'image/png':
	                $foto=$foto.".png";
	            break;
	        }

	        chmod($img_dir,0777);

	        # Moviendo imagen al directorio #
	        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
	            $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos subir la imagen al sistema en este momento",
					"icono"=>"error"
				];
				return json_encode($alerta);
	            exit();
	        }

	        # Eliminando imagen anterior #
	        if(is_file($img_dir.$datos['producto_foto']) && $datos['producto_foto']!=$foto){
		        chmod($img_dir.$datos['producto_foto'], 0777);
		        unlink($img_dir.$datos['producto_foto']);
		    }

		    $producto_datos_up=[
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				]
			];

			$condicion=[
				"condicion_campo"=>"producto_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("producto",$producto_datos_up,$condicion)){
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"La foto del producto '".$datos['producto_nombre']."' se actualizo correctamente",
					"icono"=>"success"
				];
			}else{

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"No hemos podido actualizar algunos datos del producto '".$datos['producto_nombre']."', sin embargo la foto ha sido actualizada",
					"icono"=>"warning"
				];
			}

			return json_encode($alerta);
		}
	}