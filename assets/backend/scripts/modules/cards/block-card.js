'use strict';

import 'bootstrap/js/modal';

const blockCardModal = document.getElementById('block_card__confirm_modal');

if (blockCardModal) {
    $(blockCardModal).on('shown.bs.modal', function (e) {
        const redirectUrl = $(e.relatedTarget).data('redirect');
        let formAction = $(e.relatedTarget).data('href');

        if (formAction.indexOf('://') === -1) {
            formAction = window.location.protocol + '//' + window.location.host + formAction;
        }
        const parsedUrl = new URL(formAction);
        parsedUrl.searchParams.set('redirect', redirectUrl);

        const newUrl = decodeURIComponent(parsedUrl.toString());

        const current = e.currentTarget;
        $(current).find('form').attr('action', newUrl);
    });
    $(blockCardModal).on('hidden.bs.modal', function (e) {
        const current = e.currentTarget;
        $(current).find('form').attr('action', '');
    });
}