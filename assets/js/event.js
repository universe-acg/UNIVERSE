/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

// css & scss

// js

$(document).ready(function () {
    // // Cart quantity check before saving the current cart element
    $('#add-to-cart-button').click(function (e) {
        var $ticketsQte = 0;
        $('.eventdate-ticket-qte').each(function () {
            if ($(this).val()) {
                $ticketsQte += parseInt($(this).val());
            }
        });
        if ($ticketsQte == 0) {
            showStackBarTop('error', '', Translator.trans('Please select the quantity of tickets to order.', {}, 'javascript'));
        } else {
            $('#add-to-cart-form').submit();
        }
    });

});