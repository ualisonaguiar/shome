$(function(){
  $('body').on('click','#excluir_conta', function(){
    if($('input:checked').length == 0) {
      Mensagem.information('Escolhe pelo menos uma conta para excluir.');
      return false;
    }
    var url = baseUrl+'/conta/excluir/chv_contas/'+$('input:checked').serialize();
    Mensagem.confirmacao("Você deseja excluir esta conta?", url);
  }).on('change', '#bandeiras', function() {
    $('img').parent().parent().removeClass('hide');
    switch($(this).val()){
      case '1':
        $('img#flag-amarela').parent().parent().attr('class','hide');
        $('img#flag-verde').parent().parent().attr('class','hide');
        break;
      case '2':
        $('img#flag-vermelho').parent().parent().attr('class','hide');
        $('img#flag-verde').parent().parent().attr('class','hide');
        break;
      case '3':
        $('img#flag-vermelho').parent().parent().attr('class','hide');
        $('img#flag-amarela').parent().parent().attr('class','hide');
        break;
    }
  })
});