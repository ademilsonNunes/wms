let TCaixasFat = 0;
let TValorFat = 0;
let diaUtilMes = 0;

$(document).ready(function () {

    $.getJSON('./controller/get_dmes.php', function (dados) {
        if (dados.length > 0){    
            $.each(dados, function(i, obj){
               
                diaUtilMes = Number(obj.QTDDIASEUTEIS);
            })               
         }else{
             Reset();            
         }
    })   
       
    $.getJSON('./controller/getcarteira.php', function (dados){ 
        if (dados.length > 0){    
            $.each(dados, function(i, obj){
               $(".profit h4 span:first-child").text(formatar(obj.QTDE));  
               $(".income h4 span:first-child").text(mascaraValor(obj.LIQ));      
               $(".task h4 span:first-child").text( mascaraValor( (Number(obj.LIQ) / Number(obj.QTDE)).toFixed(2) ));

            })               
         }else{
             Reset();            
         }
    })
    

    $.getJSON('./controller/get_fat_ac.php', function (dados){ 
        if (dados.length > 0){    
            //var option = '';                                   
            $.each(dados, function(i, obj){
                TCaixasFat =   Number(obj.QTDE); 
                TValorFat  =   Number(obj.LIQ);
               $(".profit1 h4 span:first-child").text(formatar(obj.QTDE));  
               $(".income1 h4 span:first-child").text(mascaraValor(obj.LIQ));       
               $(".task4 h4 span:first-child").text(mascaraValor((Number(obj.LIQ) / Number(obj.QTDE)).toFixed(2)));                                  
                //Dia Util
                getDiaUtil(TCaixasFat, TValorFat);

            })               
         }else{
             Reset();            
         }
    })
    


    $.getJSON('./controller/get_fat.php', function (dados){ 
        if (dados.length > 0){    
            $.each(dados, function(i, obj){
               $(".visit h4 span:first-child").text(formatar(obj.QTDE));   
               $(".task2 h4 span:first-child").text(mascaraValor((obj.LIQ)));   
               $(".task3 h4 span:first-child").text(mascaraValor( (Number(obj.LIQ) / Number(obj.QTDE)).toFixed(2) ));   

            })               
         }else{
             Reset();            
         }
    })   
   
});


//Ajustar saída do valor
function getDiaUtil( TCaixasFat, TValorFat ) {
    //var diaUtilMes = getDiasUteisMes();      

    $.getJSON('./controller/get_diaUtil.php', function (dados) {
        if (dados.length > 0) {
            $.each(dados, function (i, obj) {
                var diaUtil = Number(obj.DIAUTIL);
                var dd = obj.DIAUTIL.toString();
                var ddmes = diaUtilMes.toString();       

                var Mediacaixas = (TCaixasFat / diaUtil ).toFixed();
                var mediaFat =  (TValorFat / diaUtil ).toFixed(2);
                var precoMedio = (mediaFat / Mediacaixas).toFixed(2);

                var projCaixasMes = (Mediacaixas * diaUtilMes);
                var projFatMes = (mediaFat * diaUtilMes).toFixed(2);
                var projPrecoMedio = (projFatMes / projCaixasMes).toFixed(2);

                $('.media_cx').html("Média em " + dd + " Dias");
                $(".profit2 h4 span:first-child").text(  formatar(Mediacaixas) );
                $(".income2 h4 span:first-child").text( mascaraValor( mediaFat ));    
                $(".task5 h4 span:first-child").text( mascaraValor( precoMedio ));

                $('.media_cx_mes').html("Projeção para " + ddmes  + " Dias");
                $(".profit3 h4 span:first-child").text(  formatar(projCaixasMes) );
                $(".income3 h4 span:first-child").text( mascaraValor(projFatMes)  );    
                $(".task6 h4 span:first-child").text( mascaraValor(projPrecoMedio)  );
            });
        }
        else {
            Reset();
        }
    });
}



function mascaraValor(valor) {
    valor = valor.toString().replace(/\D/g,"");
    valor = valor.toString().replace(/(\d)(\d{8})$/,"$1.$2");
    valor = valor.toString().replace(/(\d)(\d{5})$/,"$1.$2");
    valor = valor.toString().replace(/(\d)(\d{2})$/,"$1,$2");
    valor = "R$ " + valor;
    return valor                    
}


function formatar(nr) {
    return String(nr)
      .split('').reverse().join('').split(/(\d{3})/).filter(Boolean)
      .join('.').split('').reverse().join('');
  }


  function RetornaDataHoraAtual(){
    var dNow = new Date();
    var localdate = dNow.getDate() + '/' + (dNow.getMonth()+1) + '/' + dNow.getFullYear() + ' ' + dNow.getHours() + ':' + dNow.getMinutes();
    return localdate;
  }
