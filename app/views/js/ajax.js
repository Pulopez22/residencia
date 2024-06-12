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
                        if (!respuesta.ok) {
                            return respuesta.text().then(text => {
                                throw new Error('La solicitud no pudo completarse correctamente. Código de estado: ' + respuesta.status + ' - ' + text);
                            });
                        }
                        return respuesta.json();
                    })
                    .then(respuesta => {
                        console.log('Respuesta del servidor:', respuesta); // Depuración
                        alertas_ajax(respuesta);
                    })
                    .catch(error => {
                        console.error('Error en la solicitud fetch:', error); 
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
    console.log('Tipo de dato de la alerta:', typeof alerta); // Depuración
    console.log('Alerta recibida:', alerta); // Añadir depuración

    if (typeof alerta === 'string') {
        try {
            alerta = JSON.parse(alerta);
        } catch (error) {
            console.error('Error al parsear JSON:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la respuesta del servidor: ' + error.message,
                confirmButtonText: 'Aceptar'
            });
            return;
        }
    }

    if (alerta && alerta.tipo) {
        switch (alerta.tipo) {
            case "simple":
                Swal.fire({
                    icon: alerta.icono,
                    title: alerta.titulo,
                    text: alerta.texto,
                    confirmButtonText: 'Aceptar'
                });
                break;
            case "limpiar":
                Swal.fire({
                    icon: 'success',
                    title: alerta.titulo,
                    text: alerta.texto,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    document.querySelector(".FormularioAjax").reset();
                });
                break;
            case "recargar":
                Swal.fire({
                    icon: 'success',
                    title: alerta.titulo,
                    text: alerta.texto,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    location.reload();
                });
                break;
            case "redireccionar":
                Swal.fire({
                    icon: 'success',
                    title: alerta.titulo,
                    text: alerta.texto,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = alerta.url;
                });
                break;
            default:
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error inesperado al procesar la respuesta del servidor.',
                    confirmButtonText: 'Aceptar'
                });
                console.error('Tipo de alerta desconocido:', alerta.tipo); // Añadir depuración
        }
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La respuesta del servidor no tiene el formato esperado.',
            confirmButtonText: 'Aceptar'
        });
        console.error('Alerta malformada:', alerta); // Añadir depuración
    }
}