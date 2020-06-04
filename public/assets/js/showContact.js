$('#showContact').click(function (e) {
    var id = $(this).attr('data-id');
    $.ajax({
        type: "GET",
        //FOS Routing
        url: Routing.generate('marca_autor_show', {'id' : id}),
        success: function(response) {
            $('#contact').html(response.html);
            $('#contact').show().animate({
                right:'50%',
            }, 500);
            $('#cortina').show();
        },
        error: function(data) {
            console.log("Error in async callback");
            alert("Tiempo de espera excedido");
        }
    });
})

$(document).on('click', function (event) {
    content = $('#contact');

    if( event.target.id == 'cerrar'){
        content.animate({
            right:'-300px',
        }, 500).hide(500);
        $('#cortina').hide();
    }
})