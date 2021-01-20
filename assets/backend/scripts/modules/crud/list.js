'use strict';

import 'tablesaw';
import 'bootstrap/js/modal';

Tablesaw.init('.tablesaw');

const deleteModal = document.getElementById('delete-confirm-modal');
if (deleteModal) {
    $(deleteModal).on('shown.bs.modal', function (e) {
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
    $(deleteModal).on('hidden.bs.modal', function (e) {
        const current = e.currentTarget;
        $(current).find('form').attr('action', '');
    });
}
