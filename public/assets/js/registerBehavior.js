var tipo = $('#registration_form_rol');
var token = $('#registration_form__token');
var agent = $('.agentForm');

tipo.change(function () {
    var $form = $(this).closest('form');
    var data = {};

    data[token.attr('name')] = token.val();
    data[tipo.attr(('name'))] = tipo.val();

    $.ajax($form.attr('action'), data).then(function (response) {
        ocultar();
        mostrar();
    })

});

function ocultar() {
    if(tipo.val() == 1){
        $(".container").css("top","25%");
        agent.hide();
        agent.prop('required', false);
    }
}
function mostrar() {
    if(tipo.val() == 0){
        $(".container").css("top","18%");
        agent.show();
        agent.prop('required', true);
    }
}

window.onload=()=>{
    ocultar();
}