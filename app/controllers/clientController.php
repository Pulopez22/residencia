<?php

	namespace app\controllers;
	use app\models\mainModel;

	class clientController extends mainModel{

		/*----------  Controlador registrar cliente  ----------*/
		public function validarRFC($rfc) {
			// Ejemplo de expresión regular para RFC (ajusta según tus necesidades)
			$pattern = '/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/';
			return !preg_match($pattern, $rfc);
		}
		
		public function verificarDatos($filtro, $cadena) {
			if (preg_match("/^" . $filtro . "$/", $cadena)) {
				return false;
			} else {
				return true;
			}
		}		

		public function registrarClienteControlador(){
			// Almacenando datos
			$nombre = $this->limpiarCadena($_POST['cliente_nombre']);
			$apellido = isset($_POST['cliente_apellido']) ? $this->limpiarCadena($_POST['cliente_apellido']) : ''; // Si no se proporciona, asigna una cadena vacía
			
			$provincia = $this->limpiarCadena($_POST['cliente_provincia']);
			$ciudad = $this->limpiarCadena($_POST['cliente_ciudad']);
			$direccion = $this->limpiarCadena($_POST['cliente_direccion']);
			$telefono = $this->limpiarCadena($_POST['cliente_telefono']);
			$email = $this->limpiarCadena($_POST['cliente_email']);
			
			// Verificando campos obligatorios
			if ($nombre == "" || $provincia == "" || $ciudad == "" || $direccion == "" || $email == "") {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No has llenado todos los campos que son obligatorios",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			// Verificando integridad de los datos
			if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El NOMBRE no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			// Validación del apellido solo si se proporciona
			if (!empty($apellido) && $this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El APELLIDO no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}", $provincia)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La PROVINCIA no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}", $ciudad)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La CIUDAD no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,70}", $direccion)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La DIRECCION no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if (!empty($telefono) && $this->verificarDatos("[0-9()+]{8,20}", $telefono)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El TELEFONO no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
	
			if ($this->validarRFC($email)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El RFC no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			$check_email = $this->ejecutarConsulta("SELECT cliente_email FROM cliente WHERE cliente_email='$email'");
			if ($check_email->rowCount() > 0) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El RFC que acaba de ingresar ya se encuentra registrado en el sistema, por favor verifique e intente nuevamente",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			$cliente_datos_reg = [
				[
					"campo_nombre" => "cliente_nombre",
					"campo_marcador" => ":Nombre",
					"campo_valor" => $nombre
				],
				[
					"campo_nombre" => "cliente_apellido",
					"campo_marcador" => ":Apellido",
					"campo_valor" => $apellido
				],
				[
					"campo_nombre" => "cliente_provincia",
					"campo_marcador" => ":Provincia",
					"campo_valor" => $provincia
				],
				[
					"campo_nombre" => "cliente_ciudad",
					"campo_marcador" => ":Ciudad",
					"campo_valor" => $ciudad
				],
				[
					"campo_nombre" => "cliente_direccion",
					"campo_marcador" => ":Direccion",
					"campo_valor" => $direccion
				],
				[
					"campo_nombre" => "cliente_telefono",
					"campo_marcador" => ":Telefono",
					"campo_valor" => $telefono
				],
				[
					"campo_nombre" => "cliente_email",
					"campo_marcador" => ":Email",
					"campo_valor" => $email
				]
			];
		
			$registrar_cliente = $this->guardarDatos("cliente", $cliente_datos_reg);
		
			if ($registrar_cliente->rowCount() == 1) {
				$alerta = [
					"tipo" => "limpiar",
					"titulo" => "Proveedor registrado",
					"texto" => "El cliente $nombre $apellido se registró con éxito",
					"icono" => "success"
				];
			} else {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No se pudo registrar el cliente, por favor intente nuevamente",
					"icono" => "error"
				];
			}
		
			echo json_encode($alerta);
			exit();
		}		

		/*----------  Controlador listar cliente  ----------*/
		public function listarClienteControlador($pagina,$registros,$url,$busqueda){

			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);

			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";

			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			if(isset($busqueda) && $busqueda!="") {
				$consulta_datos = "SELECT * FROM cliente WHERE cliente_nombre LIKE '%$busqueda%' OR cliente_apellido LIKE '%$busqueda%' OR cliente_email LIKE '%$busqueda%' OR cliente_provincia LIKE '%$busqueda%' OR cliente_ciudad LIKE '%$busqueda%' ORDER BY cliente_nombre LIMIT $inicio, $registros";
			
				$consulta_total = "SELECT COUNT(cliente_id) FROM cliente WHERE cliente_id != '1' AND (cliente_nombre LIKE '%$busqueda%' OR cliente_apellido LIKE '%$busqueda%' OR cliente_email LIKE '%$busqueda%' OR cliente_provincia LIKE '%$busqueda%' OR cliente_ciudad LIKE '%$busqueda%')";
			}else{

				$consulta_datos="SELECT * FROM cliente WHERE cliente_id!='1' ORDER BY cliente_nombre ASC LIMIT $inicio,$registros";

				$consulta_total="SELECT COUNT(cliente_id) FROM cliente WHERE cliente_id!='1'";

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
		                    <th class="has-text-centered">#</th>
		                    <th class="has-text-centered">Nombre</th>
		                    <th class="has-text-centered">RFC</th>
		                    <th class="has-text-centered">Actualizar</th>
		                    <th class="has-text-centered">Eliminar</th>
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
							<td>'.$contador.'</td>
							<td>'.$rows['cliente_nombre'].' '.$rows['cliente_apellido'].'</td>
							<td>'.$rows['cliente_email'].'</td>
			                <td>
			                    <a href="'.APP_URL.'clientUpdate/'.$rows['cliente_id'].'/" class="button is-success is-rounded is-small">
			                    	<i class="fas fa-sync fa-fw"></i>
			                    </a>
			                </td>
			                <td>
			                	<form class="FormularioAjax" action="'.APP_URL.'app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_cliente" value="eliminar">
			                		<input type="hidden" name="cliente_id" value="'.$rows['cliente_id'].'">

			                    	<button type="submit" class="button is-danger is-rounded is-small">
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
			                <td colspan="6">
			                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
			            </tr>
					';
				}else{
					$tabla.='
						<tr class="has-text-centered" >
			                <td colspan="6">
			                    No hay registros en el sistema
			                </td>
			            </tr>
					';
				}
			}

			$tabla.='</tbody></table></div>';

			### Paginacion ###
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando clientes <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;
		}


		/*----------  Controlador eliminar cliente  ----------*/
		public function eliminarClienteControlador(){

			$id=$this->limpiarCadena($_POST['cliente_id']);

			if($id==1){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos eliminar el cliente principal del sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			# Verificando cliente #
		    $datos=$this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el cliente en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Verificando ventas #
		    $check_ventas=$this->ejecutarConsulta("SELECT usuario_id FROM venta WHERE usuario_id='$id' LIMIT 1");
		    if($check_ventas->rowCount()>0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No podemos eliminar el proveedor del sistema ya que tiene ventas asociadas",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    $eliminarCliente=$this->eliminarRegistro("cliente","cliente_id",$id);

		    if($eliminarCliente->rowCount()==1){

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Cliente eliminado",
					"texto"=>"El proveedor ".$datos['cliente_nombre']." ".$datos['cliente_apellido']." ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				];

		    }else{
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el cliente ".$datos['cliente_nombre']." ".$datos['cliente_apellido']." del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    }

		    return json_encode($alerta);
		}


		/*----------  Controlador actualizar cliente  ----------*/
		public function actualizarClienteControlador(){
		
			$id=$this->limpiarCadena($_POST['cliente_id']);

			# Verificando cliente #
		    $datos=$this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el cliente en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		   // Almacenando datos
			$nombre = $this->limpiarCadena($_POST['cliente_nombre']);
			$apellido = isset($_POST['cliente_apellido']) ? $this->limpiarCadena($_POST['cliente_apellido']) : ''; // Si no se proporciona, asigna una cadena vacía
			
			$provincia = $this->limpiarCadena($_POST['cliente_provincia']);
			$ciudad = $this->limpiarCadena($_POST['cliente_ciudad']);
			$direccion = $this->limpiarCadena($_POST['cliente_direccion']);
			$telefono = $this->limpiarCadena($_POST['cliente_telefono']);
			$email = $this->limpiarCadena($_POST['cliente_email']);
			
			// Verificando campos obligatorios
			if ($nombre == "" || $provincia == "" || $ciudad == "" || $direccion == "" || $email == "") {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No has llenado todos los campos que son obligatorios",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			// Verificando integridad de los datos
			if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El NOMBRE no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			// Validación del apellido solo si se proporciona
			if (!empty($apellido) && $this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El APELLIDO no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}", $provincia)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La PROVINCIA no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}", $ciudad)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La CIUDAD no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,70}", $direccion)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "La DIRECCION no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if (!empty($telefono) && $this->verificarDatos("[0-9()+]{8,20}", $telefono)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El TELEFONO no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
	
			if ($this->validarRFC($email)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El RFC no coincide con el formato solicitado",
					"icono" => "error"
				];
				echo json_encode($alerta);
				exit();
			}

		
			$cliente_datos_up = [
				[
					"campo_nombre" => "cliente_nombre",
					"campo_marcador" => ":Nombre",
					"campo_valor" => $nombre
				],
				[
					"campo_nombre" => "cliente_apellido",
					"campo_marcador" => ":Apellido",
					"campo_valor" => $apellido
				],
				[
					"campo_nombre" => "cliente_provincia",
					"campo_marcador" => ":Provincia",
					"campo_valor" => $provincia
				],
				[
					"campo_nombre" => "cliente_ciudad",
					"campo_marcador" => ":Ciudad",
					"campo_valor" => $ciudad
				],
				[
					"campo_nombre" => "cliente_direccion",
					"campo_marcador" => ":Direccion",
					"campo_valor" => $direccion
				],
				[
					"campo_nombre" => "cliente_telefono",
					"campo_marcador" => ":Telefono",
					"campo_valor" => $telefono
				],
				[
					"campo_nombre" => "cliente_email",
					"campo_marcador" => ":Email",
					"campo_valor" => $email
				]
			];

			$condicion=[
				"condicion_campo"=>"cliente_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("cliente",$cliente_datos_up,$condicion)){
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Cliente actualizado",
					"texto"=>"Los datos del proveedor ".$datos['cliente_nombre']." ".$datos['cliente_apellido']." se actualizaron correctamente",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido actualizar los datos del proveedor ".$datos['cliente_nombre']." ".$datos['cliente_apellido'].", por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
		}

	}