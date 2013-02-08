$(function(){
  $('#Alterar').html('<i class="icon-ok-sign icon-white"></i>&nbsp;Alterar');
  $('#Cadastrar').html('<i class="icon-ok-sign icon-white"></i>&nbsp;Cadastrar');

  $('#chv_usuario-label').append($('#btn').html());
  $('#chv_usuario-label').addClass('pull pull-right');
  $('#btn').remove();

  var usuario = {};
  $('body').on('click','#excluir_usuario', function() {
    usuario.excluir($('#chv_usuario').val());
  }).on('click','#acesso_sis',function() {
    if($(this).is(':checked')) {
      $('#ds_login').removeAttr('disabled');
      $('#ds_login').attr('required', 'required');
    } else{
      $('#ds_login').removeAttr('required');
      $('#ds_login').attr('disabled', 'disabled');
      $('#ds_login').val("");
    }
  });
});