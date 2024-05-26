const formularios_ajax = document.querySelectorAll(".FormularioAjax");

formularios_ajax.forEach(formulario => {

    formulario.addEventListener("submit", function(e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Quieres realizar la acción solicitada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, realizar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                let data = new FormData(this);
                let method = this.getAttribute("method");
                let action = this.getAttribute("action");

                let encabezados = new Headers();
                encabezados.append('Accept', 'application/json');

                let config = {
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no-cache',
                    body: data
                };

                fetch(action, config)
                .then(async respuesta => {
                    // Mostrar código de estado y tipo de contenido
                    console.log('Estado de la respuesta:', respuesta.status);
                    console.log('Tipo de contenido:', respuesta.headers.get('content-type'));

                    let contentType = respuesta.headers.get('content-type');
                    if (respuesta.ok && contentType && contentType.indexOf('application/json') !== -1) {
                        return await respuesta.json();
                    } else {
                        let texto = await respuesta.text();
                        console.error('Respuesta no es JSON:', texto);
                        throw new Error(`Respuesta no es JSON. Estado: ${respuesta.status}, Cuerpo: ${texto}`);
                    }
                })
                .then(respuesta => {
                    return alertas_ajax(respuesta);
                })
                .catch(error => {
                    console.error('Error en la solicitud fetch:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `Ocurrió un error en la solicitud:<br>${error.message}`,
                        confirmButtonText: 'Aceptar'
                    });
                });
            }
        });

    });

});

function alertas_ajax(alerta) {
    console.log('Alerta recibida:', alerta);
    if (alerta.tipo == "simple") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.tipo == "recargar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    } else if (alerta.tipo == "limpiar") {
        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector(".FormularioAjax").reset();
            }
        });
    } else if (alerta.tipo == "redireccionar") {
        window.location.href = alerta.url;
    }
}

/* Boton cerrar sesion */
let btn_exit = document.querySelectorAll(".btn-exit");

btn_exit.forEach(exitSystem => {
    exitSystem.addEventListener("click", function(e) {
        e.preventDefault();

        Swal.fire({
            title: '¿Quieres salir del sistema?',
            text: "La sesión actual se cerrará y saldrás del sistema",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = this.getAttribute("href");
                window.location.href = url;
            }
        });

    });
});