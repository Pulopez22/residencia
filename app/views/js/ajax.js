/* Enviar formularios via AJAX */
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

                let config = {
                    method: method,
                    mode: 'cors',
                    cache: 'no-cache',
                    body: data
                };

                fetch(action, config)
                    .then(respuesta => {
                        console.log('Estado de la respuesta:', respuesta.status); // Depuración
                        if (respuesta.ok) {
                            return respuesta.json(); // Intentar analizar la respuesta como JSON
                        } else {
                            return respuesta.text().then(text => {
                                throw new Error('La solicitud no pudo completarse correctamente. Código de estado: ' + respuesta.status + ' - ' + text);
                            });
                        }
                    })
                    .then(respuesta => {
                        console.log('Respuesta del servidor:', respuesta); // Depuración
                        alertas_ajax(respuesta);
                    })
                    .catch(error => {
                        console.error('Error en la solicitud fetch:', error); // Manejo de errores
                        // Mostrar un mensaje de error con SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al procesar la solicitud: ' + error.message,
                            confirmButtonText: 'Aceptar'
                        });
                    });
            }
        });
    });
});

function alertas_ajax(alerta) {
    if (alerta && alerta.tipo) {
        if (alerta.tipo === "simple") {
            Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            });
        } else if (alerta.tipo === "recargar") {
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
        } else if (alerta.tipo === "limpiar") {
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
        } else if (alerta.tipo === "redireccionar") {
            window.location.href = alerta.url;
        }
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error inesperado al procesar la respuesta del servidor.',
            confirmButtonText: 'Aceptar'
        });
        console.error('La respuesta no es válida:', alerta);
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