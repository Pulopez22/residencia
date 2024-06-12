<?php

	namespace app\controllers;
	use app\models\mainModel;

	class saleController extends mainModel{

		/*---------- Controlador buscar codigo de producto ----------*/
        public function buscarCodigoVentaControlador() {
			// Limpiar el valor del código de búsqueda
			$producto = $this->limpiarCadena($_POST['buscar_codigo']);
			
			// Verificar si el valor está vacío
			if ($producto == "") {
				// Devolver un mensaje de advertencia si el campo está vacío
				return '<article class="message is-warning mt-4 mb-4">
					<div class="message-header">
						<p>¡Ocurrió un error inesperado!</p>
					</div>
					<div class="message-body has-text-centered">
						<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						Debes introducir el Nombre, Marca o Modelo del producto
					</div>
				</article>';
				exit(); // Terminar la ejecución del script
			}
			
			// Realizar la consulta para buscar productos que coincidan con el término de búsqueda
			$datos_productos = $this->ejecutarConsulta("SELECT * FROM producto WHERE (producto_nombre LIKE '%$producto%' OR producto_marca LIKE '%$producto%') ORDER BY producto_nombre ASC");
		
			// Verificar si se encontraron productos
			if ($datos_productos->rowCount() >= 1) {
				$datos_productos = $datos_productos->fetchAll();
				$tabla = '<div class="table-container mb-6"><table class="table is-striped is-narrow is-hoverable is-fullwidth"><tbody>';
				foreach ($datos_productos as $rows) {
					$tabla .= '<tr class="has-text-left">
						<td><i class="fas fa-box fa-fw"></i> &nbsp; '.$rows['producto_nombre'].'</td>
						<td class="has-text-centered">
							<button type="button" class="button is-link is-rounded is-small" onclick="agregar_codigo(\''.$rows['producto_codigo'].'\')"><i class="fas fa-plus-circle"></i></button>
						</td>
					</tr>';
				}
				$tabla .= '</tbody></table></div>';
				return $tabla;
			} else {
				return '<article class="message is-warning mt-4 mb-4">
					<div class="message-header">
						<p>¡Ocurrió un error inesperado!</p>
					</div>
					<div class="message-body has-text-centered">
						<i class="fas fa-exclamation-triangle fa-2x"></i><br>
						No se ha encontrado ningún producto en el sistema que coincida con <strong>“'.$producto.'”</strong>
					</div>
				</article>';
				exit();
			}
		}
		

        /*---------- Controlador agregar producto a venta ----------*/
		public function agregarProductoCarritoControlador() {
			$codigo = $this->limpiarCadena($_POST['producto_codigo']);
		

            /*== Comprobando producto en la DB ==*/
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto con código de barras : '$codigo'",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }else{
                $campos=$check_producto->fetch();
            }

            /*== Codigo de producto ==*/
            $codigo=$campos['producto_codigo'];

            if(empty($_SESSION['datos_producto_venta'][$codigo])){

                $detalle_cantidad=1;

                $stock_total=$campos['producto_stock_total']+$detalle_cantidad;

                if($stock_total<0){
                    $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Lo sentimos, no hay existencias disponibles del producto seleccionado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
                }

                $detalle_total=$detalle_cantidad*$campos['producto_precio_compra'];
                $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');

                $_SESSION['datos_producto_venta'][$codigo]=[
                    "producto_id"=>$campos['producto_id'],
					"producto_codigo"=>$campos['producto_codigo'],
					"producto_stock_total"=>$stock_total,
					"producto_stock_total_old"=>$campos['producto_stock_total'],
                    "venta_detalle_precio_compra"=>$campos['producto_precio_compra'],
                    "venta_detalle_cantidad"=>1,
                    "venta_detalle_total"=>$detalle_total,
                    "venta_detalle_descripcion"=>$campos['producto_nombre']
                ];

                $_SESSION['alerta_producto_agregado']="Se agrego <strong>".$campos['producto_nombre']."</strong> a la venta";
            }else{
                $detalle_cantidad=($_SESSION['datos_producto_venta'][$codigo]['venta_detalle_cantidad'])+1;

                $stock_total=$campos['producto_stock_total']+$detalle_cantidad;

                if($stock_total<0){
                    $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Lo sentimos, no hay existencias disponibles del producto seleccionado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
                }

                $detalle_total=$detalle_cantidad*$campos['producto_precio_compra'];
                $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');

                $_SESSION['datos_producto_venta'][$codigo]=[
                    "producto_id"=>$campos['producto_id'],
					"producto_codigo"=>$campos['producto_codigo'],
					"producto_stock_total"=>$stock_total,
					"producto_stock_total_old"=>$campos['producto_stock_total'],
                    "venta_detalle_precio_compra"=>$campos['producto_precio_compra'],
                    "venta_detalle_cantidad"=>$detalle_cantidad,
                    "venta_detalle_total"=>$detalle_total,
                    "venta_detalle_descripcion"=>$campos['producto_nombre']
                ];

                $_SESSION['alerta_producto_agregado']="Se agrego +1 <strong>".$campos['producto_nombre']."</strong> a la venta. Total en carrito: <strong>$detalle_cantidad</strong>";
            }

            $alerta=[
				"tipo"=>"redireccionar",
				"url"=>APP_URL."saleNew/"
			];

			return json_encode($alerta);
        }


        /*---------- Controlador remover producto de venta ----------*/
        public function removerProductoCarritoControlador(){

            /*== Recuperando codigo del producto ==*/
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);

            unset($_SESSION['datos_producto_venta'][$codigo]);

            if(empty($_SESSION['datos_producto_venta'][$codigo])){
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"¡Producto removido!",
					"texto"=>"El producto se ha removido de la venta",
					"icono"=>"success"
				];
				
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido remover el producto, por favor intente nuevamente",
					"icono"=>"error"
				];
            }
            return json_encode($alerta);
        }


        /*---------- Controlador actualizar producto de venta ----------*/
        public function actualizarProductoCarritoControlador(){

            /*== Recuperando codigo & cantidad del producto ==*/
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            $cantidad=$this->limpiarCadena($_POST['producto_cantidad']);

          

            /*== comprobando cantidad de productos ==*/
            if($cantidad<=0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Debes de introducir una cantidad mayor a 0",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            /*== Comprobando producto en la DB ==*/
            $check_producto=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_codigo='$codigo'");
            if($check_producto->rowCount()<=0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto con código de barras : '$codigo'",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }else{
                $campos=$check_producto->fetch();
            }

            /*== comprobando producto en carrito ==*/
            if(!empty($_SESSION['datos_producto_venta'][$codigo])){

                if($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]==$cantidad){
                    $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"No has modificado la cantidad de productos",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
                }

                if($cantidad>$_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]){
                    $diferencia_productos="agrego +".($cantidad-$_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]);
                }else{
                    $diferencia_productos="quito -".($_SESSION['datos_producto_venta'][$codigo]["venta_detalle_cantidad"]-$cantidad);
                }


                $detalle_cantidad=$cantidad;

                $stock_total=$campos['producto_stock_total']+$detalle_cantidad;

                if($stock_total<0){
                    $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Lo sentimos, no hay existencias suficientes del producto seleccionado. Existencias disponibles: ".($stock_total+$detalle_cantidad)."",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
                }

                $detalle_total=$detalle_cantidad*$campos['producto_precio_compra'];
                $detalle_total=number_format($detalle_total,MONEDA_DECIMALES,'.','');

                $_SESSION['datos_producto_venta'][$codigo]=[
                    "producto_id"=>$campos['producto_id'],
					"producto_codigo"=>$campos['producto_codigo'],
					"producto_stock_total"=>$stock_total,
					"producto_stock_total_old"=>$campos['producto_stock_total'],
                    "venta_detalle_precio_compra"=>$campos['producto_precio_compra'],
                    "venta_detalle_cantidad"=>$detalle_cantidad,
                    "venta_detalle_total"=>$detalle_total,
                    "venta_detalle_descripcion"=>$campos['producto_nombre']
                ];

                $_SESSION['alerta_producto_agregado']="Se $diferencia_productos <strong>".$campos['producto_nombre']."</strong> a la venta. Total en carrito <strong>$detalle_cantidad</strong>";

                $alerta=[
					"tipo"=>"redireccionar",
					"url"=>APP_URL."saleNew/"
				];

				return json_encode($alerta);
            }else{
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto que desea actualizar en el carrito",
					"icono"=>"error"
				];
				return json_encode($alerta);
            }
        }

        /*---------- Controlador registrar venta ----------*/
        public function registrarVentaControlador(){
		
			// Obtén el nombre del usuario de la sesión
			$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Desconocido';

			 // Verificar si se han recibido los datos del formulario de venta
			 if($_SERVER["REQUEST_METHOD"] !== "POST") {
				// No se han recibido datos del formulario de venta, retornar un mensaje de error
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No se han recibido los datos del formulario de venta",
					"icono" => "error"
				];
				return json_encode($alerta);
			}
			
				/*== Comprobando integridad de los datos ==*/
				if ($_SESSION['venta_total'] <= 0 || (!isset($_SESSION['datos_producto_venta']) || count($_SESSION['datos_producto_venta']) <= 0)) {
					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "No ha agregado productos a esta venta",
						"icono" => "error"
					];
					return json_encode($alerta);
					exit();
				}
			
				/*== Formateando variables ==*/
				$venta_total = number_format($_SESSION['venta_total'], MONEDA_DECIMALES, '.', '');
			
				$venta_fecha = date("Y-m-d");
				$venta_hora = date("h:i a");
			
				$venta_total_final = $venta_total;
				$venta_total_final = number_format($venta_total_final, MONEDA_DECIMALES, '.', '');
			
				/*== Actualizando productos ==*/
				$errores_productos = 0;
				foreach($_SESSION['datos_producto_venta'] as $productos){
			
					/*== Obteniendo datos del producto ==*/
					$check_producto = $this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='".$productos['producto_id']."' AND producto_codigo='".$productos['producto_codigo']."'");
					if($check_producto->rowCount() < 1){
						$errores_productos = 1;
						break;
					} else {
						$datos_producto = $check_producto->fetch();
					}
			
					/*== Respaldando datos de BD para poder restaurar en caso de errores ==*/
					$_SESSION['datos_producto_venta'][$productos['producto_codigo']]['producto_stock_total'] = $datos_producto['producto_stock_total'] + $_SESSION['datos_producto_venta'][$productos['producto_codigo']]['venta_detalle_cantidad'];
			
					$_SESSION['datos_producto_venta'][$productos['producto_codigo']]['producto_stock_total_old'] = $datos_producto['producto_stock_total'];
			
					/*== Preparando datos para enviarlos al modelo ==*/
					$datos_producto_up = [
						[
							"campo_nombre" => "producto_stock_total",
							"campo_marcador" => ":Stock",
							"campo_valor" => $_SESSION['datos_producto_venta'][$productos['producto_codigo']]['producto_stock_total']
						]
					];
			
					$condicion = [
						"condicion_campo" => "producto_id",
						"condicion_marcador" => ":ID",
						"condicion_valor" => $productos['producto_id']
					];
			
					/*== Actualizando producto ==*/
					if(!$this->actualizarDatos("producto", $datos_producto_up, $condicion)){
						$errores_productos = 1;
						break;
					}
				}
			
				/*== Reestableciendo DB debido a errores ==*/
				if($errores_productos == 1){
			
					foreach($_SESSION['datos_producto_venta'] as $producto){
			
						$datos_producto_rs = [
							[
								"campo_nombre" => "producto_stock_total",
								"campo_marcador" => ":Stock",
								"campo_valor" => $producto['producto_stock_total_old']
							]
						];
			
						$condicion = [
							"condicion_campo" => "producto_id",
							"condicion_marcador" => ":ID",
							"condicion_valor" => $producto['producto_id']
						];
			
						$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
					}
			
					$alerta = [
						"tipo" => "simple",
						"titulo" => "Ocurrió un error inesperado",
						"texto" => "No hemos podido actualizar los productos en el sistema",
						"icono" => "error"
					];
					return json_encode($alerta);
					exit();
				}	
		
			/*== generando codigo de venta ==*/
			$correlativo = $this->ejecutarConsulta("SELECT venta_id FROM venta");
			$correlativo = ($correlativo->rowCount()) + 1;
			$codigo_venta = $this->generarCodigoAleatorio(10, $correlativo);
		
			// Convertir el ID del usuario a un entero para asegurarse de que sea un número válido
			$usuario_nombre = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;


			/*== Preparando datos para enviarlos al modelo ==*/
				$datos_venta_reg = [
					[
						"campo_nombre" => "venta_codigo",
						"campo_marcador" => ":Codigo",
						"campo_valor" => $codigo_venta
					],
					[
						"campo_nombre" => "venta_fecha",
						"campo_marcador" => ":Fecha",
						"campo_valor" => $venta_fecha
					],
					[
						"campo_nombre" => "venta_hora",
						"campo_marcador" => ":Hora",
						"campo_valor" => $venta_hora
					],
					[
						"campo_nombre" => "venta_total",
						"campo_marcador" => ":Total",
						"campo_valor" => $venta_total_final
					],
					[
						"campo_nombre" => "usuario_id",
						"campo_marcador" => ":Usuario",
						"campo_valor" => $usuario_nombre  // Cambiado de $_SESSION['id'] a $usuario_nombre
					]
				];


		
			/*== Agregando venta ==*/
			$agregar_venta = $this->guardarDatos("venta", $datos_venta_reg);
		
			if($agregar_venta->rowCount() != 1){
				foreach($_SESSION['datos_producto_venta'] as $producto){
		
					$datos_producto_rs = [
						[
							"campo_nombre" => "producto_stock_total",
							"campo_marcador" => ":Stock",
							"campo_valor" => $producto['producto_stock_total_old']
						]
					];
		
					$condicion = [
						"condicion_campo" => "producto_id",
						"condicion_marcador" => ":ID",
						"condicion_valor" => $producto['producto_id']
					];
		
					$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
				}
		
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No hemos podido registrar la venta, por favor intente nuevamente. Código de error: 001",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		
			/*== Agregando detalles de la venta ==*/
			$errores_venta_detalle = 0;
			foreach($_SESSION['datos_producto_venta'] as $venta_detalle){
		
				/*== Preparando datos para enviarlos al modelo ==*/
				$datos_venta_detalle_reg = [
					[
						"campo_nombre" => "venta_detalle_cantidad",
						"campo_marcador" => ":Cantidad",
						"campo_valor" => $venta_detalle['venta_detalle_cantidad']
					],
					[
						"campo_nombre" => "venta_detalle_precio_compra",
						"campo_marcador" => ":PrecioCompra",
						"campo_valor" => $venta_detalle['venta_detalle_precio_compra']
					],
					[
						"campo_nombre" => "venta_detalle_total",
						"campo_marcador" => ":Total",
						"campo_valor" => $venta_detalle['venta_detalle_total']
					],
					[
						"campo_nombre" => "venta_detalle_descripcion",
						"campo_marcador" => ":Descripcion",
						"campo_valor" => $venta_detalle['venta_detalle_descripcion']
					],
					[
						"campo_nombre" => "venta_codigo",
						"campo_marcador" => ":VentaCodigo",
						"campo_valor" => $codigo_venta
					],
					[
						"campo_nombre" => "producto_id",
						"campo_marcador" => ":Producto",
						"campo_valor" => $venta_detalle['producto_id']
					]
				];
		
				$agregar_detalle_venta = $this->guardarDatos("venta_detalle", $datos_venta_detalle_reg);
		
				if($agregar_detalle_venta->rowCount() != 1){
					$errores_venta_detalle = 1;
					break;
				}
			}
		
			if($errores_venta_detalle == 1){
				$this->eliminarDatos("venta", "venta_codigo", $codigo_venta);
				foreach($_SESSION['datos_producto_venta'] as $producto){
		
					$datos_producto_rs = [
						[
							"campo_nombre" => "producto_stock_total",
							"campo_marcador" => ":Stock",
							"campo_valor" => $producto['producto_stock_total_old']
						]
					];
		
					$condicion = [
						"condicion_campo" => "producto_id",
						"condicion_marcador" => ":ID",
						"condicion_valor" => $producto['producto_id']
					];
		
					$this->actualizarDatos("producto", $datos_producto_rs, $condicion);
				}
		
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No hemos podido registrar la venta, por favor intente nuevamente. Código de error: 002",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		
			// Reseteamos la variable de sesion
			unset($_SESSION['datos_producto_venta']);
			unset($_SESSION['venta_total']);
		
			$alerta = [
				"tipo" => "recargar",
				"titulo" => "¡Venta registrada!",
				"texto" => "La compra se registró con éxito",
				"icono" => "success"
			];
			return json_encode($alerta);
		}


        /*----------  Controlador listar venta  ----------*/
		public function listarVentaControlador($pagina,$registros,$url,$busqueda){

			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);

			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";

			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			$campos_tablas="venta.venta_id,venta.venta_codigo,venta.venta_fecha,venta.venta_hora,venta.venta_total,venta.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido";
			
			if(isset($busqueda) && $busqueda!=""){

				$consulta_datos="SELECT $campos_tablas FROM venta INNER JOIN cliente ON venta.cliente_id=cliente.cliente_id INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id WHERE (venta.venta_codigo='$busqueda') ORDER BY venta.venta_id DESC LIMIT $inicio,$registros";
			
				$consulta_total="SELECT COUNT(venta_id) FROM venta WHERE (venta.venta_codigo='$busqueda')";
			
			}else{
			
				// Eliminar la referencia a la columna caja_id
				$campos_tablas = str_replace("venta.caja_id,", "", $campos_tablas);
			
				// Modificar la consulta para que la unión sea correcta
				$consulta_datos="SELECT $campos_tablas FROM venta INNER JOIN usuario ON venta.usuario_id=usuario.usuario_id ORDER BY venta.venta_id DESC LIMIT $inicio,$registros";
			
				$consulta_total="SELECT COUNT(venta_id) FROM venta";
			
			}

			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();

			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();

			$numeroPaginas =ceil($total/$registros);

			$tabla.='
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr>
		                    <th class="has-text-centered">NRO.</th>
		                    <th class="has-text-centered">Codigo</th>
		                    <th class="has-text-centered">Fecha</th>
		                    <th class="has-text-centered">Quien realizo la compra</th>
		                    <th class="has-text-centered">Total</th>
		                    <th class="has-text-centered">Opciones</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

		    if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
					$tabla.='
						<tr class="has-text-centered" >
							<td>'.$rows['venta_id'].'</td>
							<td>'.$rows['venta_codigo'].'</td>
							<td>'.date("d-m-Y", strtotime($rows['venta_fecha'])).' '.$rows['venta_hora'].'</td>
							<td>'.$this->limitarCadena($rows['usuario_nombre'].' '.$rows['usuario_apellido'],30,"...").'</td>
							<td>'.MONEDA_SIMBOLO.number_format($rows['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR).' '.MONEDA_NOMBRE.'</td>
			                <td>

		

			                    <a href="'.APP_URL.'saleDetail/'.$rows['venta_codigo'].'/" class="button is-link is-rounded is-small" title="Informacion de venta Nro. '.$rows['venta_id'].'" >
			                    	<i class="fas fa-shopping-bag fa-fw"></i>
			                    </a>

			                	<form class="FormularioAjax is-inline-block" action="'.APP_URL.'app/ajax/ventaAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_venta" value="eliminar_venta">
			                		<input type="hidden" name="venta_id" value="'.$rows['venta_id'].'">

			                    	<button type="submit" class="button is-danger is-rounded is-small" title="Eliminar venta Nro. '.$rows['venta_id'].'" >
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                    </form>

			                </td>
						</tr>
					';
					$contador++;
				}
				$pag_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="7">
			                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
				}else{
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="7">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
				}
			}

			$tabla.='</tbody></table></div>';

			### Paginacion ###
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando ventas <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;
		}


		/*----------  Controlador eliminar venta  ----------*/
		public function eliminarVentaControlador(){

			$id=$this->limpiarCadena($_POST['venta_id']);

			# Verificando venta #
		    $datos=$this->ejecutarConsulta("SELECT * FROM venta WHERE venta_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado la venta en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Verificando detalles de venta #
		    $check_detalle_venta=$this->ejecutarConsulta("SELECT venta_detalle_id FROM venta_detalle WHERE venta_codigo='".$datos['venta_codigo']."'");
		    $check_detalle_venta=$check_detalle_venta->rowCount();

		    if($check_detalle_venta>0){

		        $eliminarVentaDetalle=$this->eliminarRegistro("venta_detalle","venta_codigo",$datos['venta_codigo']);

		        if($eliminarVentaDetalle->rowCount()!=$check_detalle_venta){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"No hemos podido eliminar la venta del sistema, por favor intente nuevamente",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
		        }

		    }


		    $eliminarVenta=$this->eliminarRegistro("venta","venta_id",$id);

		    if($eliminarVenta->rowCount()==1){

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Venta eliminada",
					"texto"=>"La venta ha sido eliminada del sistema correctamente",
					"icono"=>"success"
				];

		    }else{
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar la venta del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    }

		    return json_encode($alerta);
		}

	}