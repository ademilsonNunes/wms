function getDiasUteisMes() {
    var diasUteisMes = 0;
    $.getJSON('./controller/get_dmes.php', function (dados) {
        if (dados.length > 0) {
            var option = '';
            $.each(dados, function (i, obj) {
                diasUteisMes = Number(obj.QTDDIASEUTEIS);
            });
        }
        else {
            Reset();
        }
    });
    return diasUteisMes;
}
