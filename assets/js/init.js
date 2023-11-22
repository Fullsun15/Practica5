$(document).ready(function () {
    $("#login-form").submit(function (event) {
        event.preventDefault(); // Evita el envío tradicional del formulario
        loginTh();
    });
});

function loginTh() {
    var user = $("#user").val();
    var pass = $("#pass").val();

    if (user && pass) {
        var parametros = {
            "user": user,
            "pass": pass
        };

        $.ajax({
            url: "index.php",
            type: "POST",
            data: parametros,
            beforeSend: function () {
                $("#resultado").html("Procesando, espere por favor...");
            },
            success: function (response) {
                if (response == "1") {
                    window.location.href = 'pagina_principal.php';
                } else {
                    $("#resultado").html(response);
                }
            }
        });
    }
}


document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(elems);
});

function registrarCliente() {
    var cedula = $("#cedula").val();
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var zona_pais = $("#zona_pais").val();

    if (cedula && nombre && apellido && zona_pais) {
        var parametros = {
            "cedula": cedula,
            "nombre": nombre,
            "apellido": apellido,
            "zona_pais": zona_pais
        };

        $.ajax({
            url: "registroC.php",
            type: "POST",
            data: parametros,
            beforeSend: function () {
                $("#resultado").html("Procesando, espere por favor...");
            },
            success: function (response) {
                if (response == "1") {
                    $("#resultado").html("Cliente registrado con éxito.");
                } else {
                    $("#resultado").html(response);
                }
            }
        });
    }
    // Evitar que el formulario se envíe de forma tradicional
    return false;
}


$(document).ready(function () {
    $('select').formSelect();

    $("#realizar_pedido_form").submit(function (e) {
        e.preventDefault();

        var cedula_cliente = $("#cedula_cliente").val();
        var cantidad_bote = $("#cantidad_bote").val();

        $.ajax({
            type: "POST",
            url: "registroP.php",
            data: {
                realizar_pedido: 1,
                cedula_cliente: cedula_cliente,
                cantidad_bote: cantidad_bote
            },
            dataType: "json",
            success: function (response) {
                if (response.response === "1") {
                    $("#success_message").html("Pedido realizado con éxito.");
                    $("#error_message").html(""); // Limpiar mensaje de error
                } else {
                    $("#error_message").html(response.response);
                    $("#success_message").html(""); // Limpiar mensaje de éxito
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(elems);
});





