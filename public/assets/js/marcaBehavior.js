$('#new').click(function () {
    $.ajax({
        type: "GET",
        //FOS Routing
        url: Routing.generate('marca_autor_new'),
        success: function(response) {
            $('#form-new').html(response.html);
            $('#form-new').show().animate({
                right:'0',
            }, 500);
        },
        error: function(data) {
            console.log("Error in async callback");
            alert("Tiempo de espera excedido");
        }
    });
})

$('.edit').on('click', function (e) {
    $.ajax({
        type: "GET",
        //FOS Routing
        url: Routing.generate('marca_autor_edit',  {'id': $(this).attr('data-id')}),
        success: function(response) {
            $('#form-edit').html(response.html);
            $('#form-edit').show().animate({
                right:'0',
            }, 500);
        },
        error: function(data) {
            console.log("Error in async callback");
            alert("Tiempo de espera excedido");
        }
    });
})

$(document).on('click', function (event) {
    content1 = $('#form-new');
    content2 = $('#form-edit');

    if(!content1.is(event.target) && !$.contains(content1[0],event.target)){
        content1.animate({
            right:'-300px',
        }, 500).hide(500);
    }
    if(!content2.is(event.target) && !$.contains(content2[0],event.target)){
        content2.animate({
            right:'-300px',
        }, 500).hide(500);
    }
})