jQuery(document).ready(function () {
    $("#form_recover button").click(function (e) {
        e.preventDefault();
        var email = $("#input_recoverpass").val(); 
        if (email === "") {
            Swal.fire("Error", "Por favor ingresa un correo electrónico.", "error");
            return;
        }

        $.ajax({
            url: "/password/email", 
            type: "POST",
            data: {
                email: email,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (response) {
                Swal.fire("Éxito", "Se ha enviado un enlace de recuperación a tu correo.", "success");
            },
            error: function (xhr) {
                Swal.fire("Error", "Hubo un problema al procesar la solicitud.", "error");
            }
        });
    });
});