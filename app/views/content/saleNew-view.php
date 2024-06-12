<div class="container is-fluid mb-6">
	<h1 class="title">Compras</h1>
	<h2 class="subtitle"><i class="fas fa-cart-plus fa-fw"></i> &nbsp; Nueva compra</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        $check_empresa=$insLogin->seleccionarDatos("Normal","empresa LIMIT 1","*",0);

        if($check_empresa->rowCount()==1){
            $check_empresa=$check_empresa->fetch();
    ?>
    <div class="columns">

        <div class="column pb-6">

            <p class="has-text-centered pt-6 pb-6">
                <small>Para agregar productos debe de digitar el código de barras en el campo "Código de barras" y luego presionar &nbsp; <strong class="is-uppercase" ><i class="far fa-check-circle"></i> &nbsp; Agregar producto</strong>. También puede agregar el producto mediante la opción &nbsp; <strong class="is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto</strong>. Ademas puede escribir el código de barras y presionar la tecla <strong class="is-uppercase">enter</strong></small>
            </p>
            <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
                <div class="columns">
                    <div class="column is-one-quarter">
                        <button type="button" class="button is-link is-light js-modal-trigger" data-target="modal-js-product" ><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
                    </div>
                    <div class="column">
                        <div class="field is-grouped">
                            <p class="control is-expanded">
                                <input class="input" type="text" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70"  autofocus="autofocus" placeholder="Código de barras" id="sale-barcode-input" >
                            </p>
                            <a class="control">
                                <button type="submit" class="button is-info">
                                    <i class="far fa-check-circle"></i> &nbsp; Agregar producto
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <?php
                if(isset($_SESSION['alerta_producto_agregado']) && $_SESSION['alerta_producto_agregado']!=""){
                    echo '
                    <div class="notification is-success is-light">
                      '.$_SESSION['alerta_producto_agregado'].'
                    </div>
                    ';
                    unset($_SESSION['alerta_producto_agregado']);
                }

                if(isset($_SESSION['venta_codigo_factura']) && $_SESSION['venta_codigo_factura']!=""){
            ?>
            <div class="notification is-info is-light mb-2 mt-2">
                <h4 class="has-text-centered has-text-weight-bold">Venta realizada</h4>
                <p class="has-text-centered mb-2">La compra se realizó con éxito. ¿Que desea hacer a continuación? </p>
                <br>
                <div class="container">
                    <div class="columns">
                        <div class="column has-text-centered">
                            <button type="button" class="button is-link is-light" onclick="print_ticket('<?php echo APP_URL."app/pdf/ticket.php?code=".$_SESSION['venta_codigo_factura']; ?>')" >
                                <i class="fas fa-receipt fa-2x"></i> &nbsp;
                                Imprimir ticket de compra
                            </buttona>
                        </div>
                        <div class="column has-text-centered">
                            <button type="button" class="button is-link is-light" onclick="print_invoice('<?php echo APP_URL."app/pdf/invoice.php?code=".$_SESSION['venta_codigo_factura']; ?>')" >
                                <i class="fas fa-file-invoice-dollar fa-2x"></i> &nbsp;
                                Imprimir factura de compra
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    unset($_SESSION['venta_codigo_factura']);
                }
            ?>
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th class="has-text-centered">#</th>
                            <th class="has-text-centered">Código de barras</th>
                            <th class="has-text-centered">Producto</th>
                            <th class="has-text-centered">Cant.</th>
                            <th class="has-text-centered">Precio</th>
                            <th class="has-text-centered">Subtotal</th>
                            <th class="has-text-centered">Actualizar</th>
                            <th class="has-text-centered">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta'])>=1){

                                $_SESSION['venta_total']=0;
                                $cc=1;

                                foreach($_SESSION['datos_producto_venta'] as $productos){
                        ?>
                        <tr class="has-text-centered" >
                            <td><?php echo $cc; ?></td>
                            <td><?php echo $productos['producto_codigo']; ?></td>
                            <td><?php echo $productos['venta_detalle_descripcion']; ?></td>
                            <td>
                                <div class="control">
                                    <input class="input sale_input-cant has-text-centered" value="<?php echo $productos['venta_detalle_cantidad']; ?>" id="sale_input_<?php echo str_replace(" ", "_", $productos['producto_codigo']); ?>" type="text" style="max-width: 80px;">
                                </div>
                            </td>
                            <td><?php echo MONEDA_SIMBOLO.number_format($productos['venta_detalle_precio_compra'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></td>
                            <td><?php echo MONEDA_SIMBOLO.number_format($productos['venta_detalle_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></td>
                            <td>
                                <button type="button" class="button is-success is-rounded is-small" onclick="actualizar_cantidad('#sale_input_<?php echo str_replace(" ", "_", $productos['producto_codigo']); ?>','<?php echo $productos['producto_codigo']; ?>')" >
                                    <i class="fas fa-redo-alt fa-fw"></i>
                                </button>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off">

                                    <input type="hidden" name="producto_codigo" value="<?php echo $productos['producto_codigo']; ?>">
                                    <input type="hidden" name="modulo_venta" value="remover_producto">

                                    <button type="submit" class="button is-danger is-rounded is-small" title="Remover producto">
                                        <i class="fas fa-trash-restore fa-fw"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                                $cc++;
                                $_SESSION['venta_total']+=$productos['venta_detalle_total'];
                            }
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="4"></td>
                            <td class="has-text-weight-bold">
                                TOTAL
                            </td>
                            <td class="has-text-weight-bold">
                                <?php echo MONEDA_SIMBOLO.number_format($_SESSION['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <?php
                            }else{
                                    $_SESSION['venta_total']=0;
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="8">
                                No hay productos agregados
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="column is-one-quarter">
            <h2 class="title has-text-centered">Datos de la compra</h2>
            <hr>

            <?php if($_SESSION['venta_total']>0){ ?>
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off" name="formsale">
                <input type="hidden" name="modulo_venta" value="registrar_venta">
            <?php }else { ?>
            <form name="formsale">
            <?php } ?>

                <div class="control mb-5">
                    <label>Fecha</label>
                    <input class="input" type="date" value="<?php echo date("Y-m-d"); ?>" readonly >
                </div>

               
                <?php } ?>

                <h4 class="subtitle is-5 has-text-centered has-text-weight-bold mb-5"><small>TOTAL A PAGAR: <?php echo MONEDA_SIMBOLO.number_format($_SESSION['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></small></h4>

                <?php if($_SESSION['venta_total']>0){ ?>
                <p class="has-text-centered">
                    <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar venta</button>
                </p>
                <?php } ?>
                <p class="has-text-centered pt-6">
                    <small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small>
                </p>
                <input type="hidden" value="<?php echo number_format($_SESSION['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,""); ?>" id="venta_total_hidden">
            </form>
        </div>


<!-- Modal buscar producto -->
<div class="modal" id="modal-js-product">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title is-uppercase"><i class="fas fa-search"></i> &nbsp; Buscar producto</p>
          <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <div class="field mt-6 mb-6">
                <label class="label">¿Que deseas compras?</label>
                <div class="control">
                    <input class="input" type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" name="input_codigo" id="input_codigo" maxlength="30" >
                </div>
            </div>
            <div class="container" id="tabla_productos"></div>
            <p class="has-text-centered">
                <button type="button" class="button is-link is-light" onclick="buscar_codigo()" ><i class="fas fa-search"></i> &nbsp; Buscar</button>
            </p>
        </section>
    </div>
</div>

<script>

    /* Detectar cuando se envia el formulario para agregar producto */
    let sale_form_barcode = document.querySelector("#sale-barcode-form");
    sale_form_barcode.addEventListener('submit', function(event){
        event.preventDefault();
        setTimeout('agregar_producto()',100);
    });


    /* Detectar cuando escanea un codigo en formulario para agregar producto */
    let sale_input_barcode = document.querySelector("#sale-barcode-input");
    sale_input_barcode.addEventListener('paste',function(){
        setTimeout('agregar_producto()',100);
    });


 /* Agregar producto */
function agregar_producto() {
    // Obtener el código del producto ingresado
    let codigo_producto = document.querySelector('#sale-barcode-input').value.trim();

    // Crear un objeto FormData para enviar los datos al servidor
    let datos = new FormData();
    datos.append("producto_codigo", codigo_producto);
    datos.append("modulo_venta", "agregar_producto");

    // Realizar la solicitud al servidor utilizando fetch
    fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php', {
        method: 'POST',
        body: datos
    })
    .then(respuesta => respuesta.json())
    .then(respuesta => {
        // Mostrar las alertas retornadas por el servidor
        return alertas_ajax(respuesta);
    });
}




    /*----------  Buscar codigo  ----------*/
    function buscar_codigo(){
        let input_codigo=document.querySelector('#input_codigo').value;

        input_codigo=input_codigo.trim();

        if(input_codigo!=""){

            let datos = new FormData();
            datos.append("buscar_codigo", input_codigo);
            datos.append("modulo_venta", "buscar_codigo");

            fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php',{
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.text())
            .then(respuesta =>{
                let tabla_productos=document.querySelector('#tabla_productos');
                tabla_productos.innerHTML=respuesta;
            });

        }else{
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir el Nombre, Marca o Modelo del producto',
                confirmButtonText: 'Aceptar'
            });
        }
    }


    /*----------  Agregar codigo  ----------*/
    function agregar_codigo($codigo){
        document.querySelector('#sale-barcode-input').value=$codigo;
        setTimeout('agregar_producto()',100);
    }


    /* Actualizar cantidad de producto */
    function actualizar_cantidad(id,codigo){
        let cantidad=document.querySelector(id).value;

        cantidad=cantidad.trim();
        codigo.trim();

        if(cantidad>0){

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Desea actualizar la cantidad de productos",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, actualizar',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed){

                    let datos = new FormData();
                    datos.append("producto_codigo", codigo);
                    datos.append("producto_cantidad", cantidad);
                    datos.append("modulo_venta", "actualizar_producto");

                    fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php',{
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta =>{
                        return alertas_ajax(respuesta);
                    });
                }
            });
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir una cantidad mayor a 0',
                confirmButtonText: 'Aceptar'
            });
        }
    }


    /*----------  Calcular cambio  ----------*/
    let venta_abono_input = document.querySelector("#venta_abono");
    venta_abono_input.addEventListener('keyup', function(e){
        e.preventDefault();

        let abono=document.querySelector('#venta_abono').value;
        abono=abono.trim();
        abono=parseFloat(abono);

        let total=document.querySelector('#venta_total_hidden').value;
        total=total.trim();
        total=parseFloat(total);

        if(abono>=total){
            cambio=abono-total;
            cambio=parseFloat(cambio).toFixed(<?php echo MONEDA_DECIMALES; ?>);
            document.querySelector('#venta_cambio').value=cambio;
        }else{
            document.querySelector('#venta_cambio').value="0.00";
        }
    });

</script>

<?php
    include "./app/views/inc/print_invoice_script.php";
?>