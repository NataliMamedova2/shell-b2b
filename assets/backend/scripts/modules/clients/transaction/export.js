'use strict';

import axios from 'axios';
import toastr from 'toastr';
import 'bootstrap/js/modal';
import modal from '../../../libs/modal';
import saveAs from "file-saver";


const selectors = document.querySelectorAll('.export-btn');

selectors.forEach(function (element) {
    element.addEventListener("click", (e) => {
            let form  = document.querySelector('form');
            let query = $(form).serialize();
            let href = $(element).data('href');
            let url = href + '?' + query;

            send(element, url);

    });
});

function send(button, url) {
    axios({
        method: "get",
        url: url,
        responseType: "blob",
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => {
            saveAs(response.data, 'export-transactions.xls');
        })
        .catch(error => {
            const errors = error.response.data.errors;
            toastr.error(errors.status)
        })
}
