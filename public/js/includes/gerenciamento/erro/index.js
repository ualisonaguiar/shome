var erro = {}
erro.Detalhes = function(chv_erro, status) {
  $.ajax({
    type: 'post',
    data: {
      chv_erro: chv_erro,
      status  : status
    },
    url: baseUrl + '/gerenciamento/detalheserro',
    dataType: 'json',
    beforeSend: function(){
      $('#loading').modal('show');
    },
    success: function(response){
      $('#loading').modal('hide');
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      $('#modal_detalhes').modal('show');
      $('#Emensagem').html(response.erro.ds_message);
      $('#Earquivo').html(response.erro.ds_file);
      $('#Elinha').html(response.erro.ds_line);
      $('#Edtlog').html(response.erro.dat_log);

      if(response.erro.dat_correcao != '' && response.erro.ds_solucao != '') {
        $('.clearfix:last li.hide').removeClass('hide');
        $('#Esolucao').html(response.erro.ds_solucao);
        $('#Edtcorrecao').html(response.erro.dat_correcao);
      }

      var tr;
      $.each(response.tracert, function(i, item) {
        tr += '<tr>';
        tr += '<th>'+ i + '</th>';
        tr += '<th>'+ item.ds_class + '</th>';
        tr += '<th>'+ item.ds_function + '</th>';
        tr += '<th>'+ item.ds_line + '</th>';
        tr += '<th>'+ item.ds_file + '</th>';
        tr += '</tr>';
      });

      $('.tracert').html(tr);
    }
  });
}

$(document).ready(function() {
  $('body').on('click','#resolver', function() {
    $('#chv_erro_modal').val('');
    if($('input:checked').length == 0) {
      Mensagem.information('Selecione pelo meno um erro para resolver.');
      return false;
    }
    $('#chv_erro_modal').val($('input:checked').val());
    if($('input:checked').length != 1) {
      var chvErro;
      $('input:checked').each(function() {
        if($(this).val() != '') {
          if(chvErro == null) {
            chvErro = $(this).val();
          } else {
            chvErro += '&' + $(this).val();
          }
        }

      });
      $('#chv_erro_modal').val(chvErro);
    }
    $('#chv_erro_modal').val();
    $('#modal_resolver').modal('show');
  }).on('click','#fechado', function() {
    erro.Detalhes($(this).attr('value'), true);
    return false;
  }).on('click', '#aberto', function(){
    erro.Detalhes($(this).attr('value'), false);
    return false;
  });
});