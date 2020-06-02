$('#new').click(function () {
    $.ajax({
        type: "GET",
        //FOS Routing
        url: Routing.generate('wond_art_new'),
        success: function(response) {
            if (newform){
                $('#form-new').html(response.html);
                newform=false;
            }
            $('#form-new').show().animate({
                right:'50%',
            }, 500);
            $('#cortina').show();
            $("#wond_art_media").on('change', function (evt) {
                reader(evt);
            })
            $('#wond_art_etiquetas').parent().on('reset', reset);
            $('#etiquetas').change(function () {
                var etiquetas = $('#tags-id').val();
                $('#wond_art_etiquetas').val(etiquetas);
            });
            $(function() {
                sanitizar();
            });
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
        url: Routing.generate('wond_art_edit',  {'id': $(this).attr('data-id')}),
        success: function(response) {
            $('#form-edit').html(response.html);
            $('#form-edit').show().animate({
                right:'50%',
            }, 500);
            $('#cortina').show();

            $("#wond_art_media").on('change', function (evt) {
                reader(evt);
            });
            var initialtags = $('#form-edit #wond_art_etiquetas').val();
            $('#form-edit #tags-id').val(initialtags);
            $('#etiquetas').change(function () {
                var etiquetas = $('#tags-id').val();
                $('#wond_art_etiquetas').val(etiquetas);
            });
            $(function() {
                sanitizar();
            });
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

    //!content1.is(event.target) && !$.contains(content1[0],event.target) && event.target.id != 'new' && event.target.tagName!="SPAN"
    //         ||
    // !content2.is(event.target) && !$.contains(content2[0],event.target) && event.target.classList[0] != 'edit'  && event.target.tagName!="SPAN"
    // ||
    if( event.target.id == 'cerrar'){
        content1.animate({
            right:'-300px',
        }, 500).hide(500);
        $('#cortina').hide();
    }
    if( event.target.id == 'cerrar'){
        content2.animate({
            right:'-300px',
        }, 500).hide(500);
        $('#cortina').hide();
    }
})

function sanitizar() {
    $('input, select').on('change', function(event) {
        var $element = $(event.target),
            $container = $element.closest('.example');

        if (!$element.data('tagsinput'))
            return;

        var val = $element.val();
        if (val === null)
            val = "null";
        $('code', $('pre.val', $container)).html( ($.isArray(val) ? JSON.stringify(val) : "\"" + val.replace('"', '\\"') + "\"") );
        $('code', $('pre.items', $container)).html(JSON.stringify($element.tagsinput('items')));
    }).trigger('change');
}

function reader(evt) {
    var tgt = evt.target || window.event.srcElement,
        files = tgt.files;

    // FileReader support
    if (FileReader && files && files.length) {
        var fr = new FileReader();
        fr.onload = function () {
            preview = document.getElementById('preview');
            preview.src = fr.result;
            tgt.previousSibling.innerText = 'Cambiar';
        }
        fr.readAsDataURL(files[0]);
    }
}

function reset() {
    $('#wond_art_etiquetas').val('');
    $('.bootstrap-tagsinput>span').remove();
}
