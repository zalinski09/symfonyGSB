
$(document).ready(function() {

    $.material.init();

    $(".tableHorsForfait").dataTable();
    $(".tableForfait").dataTable();

    $(".datePicker").datepicker({
        minDate: 0,
        startDate: '1+d',
        format: "dd-mm-yy"
    });

    $(".select").dropdown({"optionClass": "withripple"});

    $().dropdown({autoinit: "select"});


});
