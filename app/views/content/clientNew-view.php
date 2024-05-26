<div class="container is-fluid mb-6">
	<h1 class="title">Clientes</h1>
	<h2 class="subtitle"><i class="fas fa-male fa-fw"></i> &nbsp; Nuevo cliente</h2>
</div>

<div class="container pb-6 pt-6">

	<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off" >

		<input type="hidden" name="modulo_cliente" value="registrar">

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Nombres <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="cliente_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Apellidos <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="cliente_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
				</div>
		  	</div>
		</div>
		<div class="columns">
			<div class="column">
    			<div class="control">
        			<label>RFC <?php echo CAMPO_OBLIGATORIO; ?></label>
        			<input class="input" type="text" name="cliente_provincia" pattern="[A-Z0-9]{13}" maxlength="13" required>
    			</div>
			</div>
		  	<div class="column">
			  	<div class="control">
   					<label>Telefono <?php echo CAMPO_OBLIGATORIO; ?></label>
    				<input class="input" type="text" name="cliente_ciudad" maxlength="10" required>
				</div>

				<script>
    				document.addEventListener('DOMContentLoaded', function() {
        			var inputTelefono = document.querySelector('input[name="cliente_ciudad"]');
        			inputTelefono.addEventListener('input', function() {
           			this.value = this.value.replace(/[^\d]/g, ''); // Eliminar todo excepto los dígitos
        			});
   				 });
				</script>

		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Ciudad <?php echo CAMPO_OBLIGATORIO; ?></label>
				  	<input class="input" type="text" name="cliente_direccion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()]{4,70}" maxlength="70" required >
				</div>
		  	</div>
		</div>
		<div class="columns">
    		<div class="column">
        		<div class="control">
            		<label>Calle</label>
           			<input class="input" type="text" name="cliente_telefono" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]{4,70}">
        		</div>
    		</div>
		</div>

		<p class="has-text-centered">
			<button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
			<button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
		</p>
		<p class="has-text-centered pt-6">
            <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
        </p>
	</form>
</div>