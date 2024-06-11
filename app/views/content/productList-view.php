<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de productos</h2>
</div>
<div class="container pb-6 pt-6">
    <div class="form-rest mb-6 mt-6"></div>


    <!-- Botones para generar PDF -->
    <form action="<?php echo APP_URL; ?>app/pdf/generar_pdf.php" method="GET" class="box">
        <div class="field">
            <label class="label" for="categoria">Seleccionar categoría:</label>
            <div class="control">
                <div class="select is-primary">
                    <select name="categoria" id="categoria">
                        <option value="all">Todo el inventario</option>
                        <option value="Aceites">Aceites</option>
                        <option value="Lacteos">Lacteos</option>
                        <option value="Carnes">Carnes</option>
                        <option value="Pescados">Pescados</option>
                        <option value="Cereales">Cereales</option>
                        <option value="Aguas Frescas">Aguas Frescas</option>
                        <option value="Aperitivos">Aperitivos</option>
                        <option value="Salsas">Salsas</option>
                        <option value="Suministros">Suministros</option>
                        <option value="Productos de limpieza">Productos de limpieza</option>
                        <option value="Capon">Capon</option>
                        <option value="Desechables">Desechables</option>
                        <option value="Frutas y verduras">Frutas y verduras</option>
                        <option value="Papeleria">Papeleria</option>
                        <option value="Especies">Especies</option>
                        <option value="Alcohol">Alcohol</option>
                        <option value="Bebidas">Bebidas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Generar PDF</button>
            </div>
        </div>
    </form>
    
    <?php
        use app\controllers\productController;

        $insProducto = new productController();
        echo $insProducto->listarProductoControlador($url[1],10,$url[0],"",0);
    ?>

    


</div>