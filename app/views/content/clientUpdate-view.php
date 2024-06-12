<div class="container is-fluid mb-6">
	<h1 class="title">Proveedores</h1>
	<h2 class="subtitle"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar proveedor</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
	
		include "./app/views/inc/btn_back.php";

		$id=$insLogin->limpiarCadena($url[1]);

		$datos=$insLogin->seleccionarDatos("Unico","cliente","cliente_id",$id);

		if($datos->rowCount()==1){
			$datos=$datos->fetch();
	?>

	<h2 class="title has-text-centered"><?php echo $datos['cliente_nombre']." ".$datos['cliente_apellido']." "; ?></h2>

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_cliente" value="actualizar">
		<input type="hidden" name="cliente_id" value="<?php echo $datos['cliente_id']; ?>">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombres *</label>
				  	<input class="input" type="text" name="cliente_nombre" value="<?php echo $datos['cliente_nombre']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Apellidos </label>
				  	<input class="input" type="text" name="cliente_apellido" value="<?php echo $datos['cliente_apellido']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" >
				</div>
		  	</div>
		</div>
		<div class="columns">
		<div class="column">
		<div class="control">
			<label>Estado *</label>
			<select class="input" name="cliente_provincia" required>
				<option value="" disabled selected>Selecciona un estado</option>
				<option value="Aguascalientes">Aguascalientes</option>
				<option value="Baja California">Baja California</option>
				<option value="Baja California Sur">Baja California Sur</option>
				<option value="Campeche">Campeche</option>
				<option value="Chiapas">Chiapas</option>
				<option value="Chihuahua">Chihuahua</option>
				<option value="Coahuila">Coahuila</option>
				<option value="Colima">Colima</option>
				<option value="Ciudad de México">Ciudad de México</option>
				<option value="Durango">Durango</option>
				<option value="Guanajuato">Guanajuato</option>
				<option value="Guerrero">Guerrero</option>
				<option value="Hidalgo">Hidalgo</option>
				<option value="Jalisco">Jalisco</option>
				<option value="México">México</option>
				<option value="Michoacán">Michoacán</option>
				<option value="Morelos">Morelos</option>
				<option value="Nayarit">Nayarit</option>
				<option value="Nuevo León">Nuevo León</option>
				<option value="Oaxaca">Oaxaca</option>
				<option value="Puebla">Puebla</option>
				<option value="Querétaro">Querétaro</option>
				<option value="Quintana Roo">Quintana Roo</option>
				<option value="San Luis Potosí">San Luis Potosí</option>
				<option value="Sinaloa">Sinaloa</option>
				<option value="Sonora">Sonora</option>
				<option value="Tabasco">Tabasco</option>
				<option value="Tamaulipas">Tamaulipas</option>
				<option value="Tlaxcala">Tlaxcala</option>
				<option value="Veracruz">Veracruz</option>
				<option value="Yucatán">Yucatán</option>
				<option value="Zacatecas">Zacatecas</option>
				</select>
				</div>
			</div>
		  	<div class="column">
		    	<div class="control">
					<label>Ciudad *</label>
				  	<input class="input" type="text" name="cliente_ciudad" value="<?php echo $datos['cliente_ciudad']; ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}" maxlength="30" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Calle *</label>
				  	<input class="input" type="text" name="cliente_direccion" value="<?php echo $datos['cliente_direccion']; ?>" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,70}" maxlength="70" required >
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
			  	<div class="control">
					<label>Teléfono</label>
				  	<input class="input" type="text" name="cliente_telefono" value="<?php echo $datos['cliente_telefono']; ?>"  pattern="[0-9]{10}" required title="Debe contener 10 dígitos numéricos" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
				</div>
		  	</div>
		  	<div class="column">
			  	<div class="control">
					<label>RFC</label>
				  	<input class="input" type="text" name="cliente_email" value="<?php echo $datos['cliente_email']; ?>" pattern="[A-Z0-9]{13}" maxlength="13" required title="Debe contener 13 caracteres alfanuméricos" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
				</div>
		  	</div>
		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded"><i class="fas fa-sync-alt"></i> &nbsp; Actualizar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>
	<?php
		}else{
			include "./app/views/inc/error_alert.php";
		}
	?>
</div>