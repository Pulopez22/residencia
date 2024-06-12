<div class="container is-fluid mb-6">
	<h1 class="title">Proveedor</h1>
	<h2 class="subtitle"><i class="fas fa-male fa-fw"></i> &nbsp; Nuevo proveedor</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_cliente" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombres *</label>
				  	<input class="input" type="text" name="cliente_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Apellidos </label>
				  	<input class="input" type="text" name="cliente_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" >
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
				  	<input class="input" type="text" name="cliente_ciudad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}" maxlength="30" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Calle *</label>
				  	<input class="input" type="text" name="cliente_direccion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,70}" maxlength="70" required >
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Teléfono *</label>
				  	<input class="input" type="text" name="cliente_telefono"  pattern="[0-9]{10}" required title="Debe contener 10 dígitos numéricos" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
				</div>
		  	</div>
		  	<div class="column">
			  <div class="control">
    <label>RFC *</label>
    <input 
        class="input" 
        type="text" 
        name="cliente_email" 
        pattern="^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$" 
        maxlength="13" 
        required 
        title="Debe contener 3 o 4 letras iniciales, 6 dígitos y 3 caracteres alfanuméricos al final" 
        placeholder="Formato: ABCD123456XYZ"
        style="text-transform: uppercase;" 
        oninput="this.value = this.value.toUpperCase()">
</div>

		  	</div>
		</div>
		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con * son obligatorios</small>
        </p>
	</form>
</div>
