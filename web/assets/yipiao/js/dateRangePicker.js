$(function() {

    $('input[name="tea[newProperty]"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('input[name="tea[newProperty]"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('input[name="tea[newProperty]"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

});