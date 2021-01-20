'use strict';

import axios from 'axios';
import toastr from 'toastr';

const {url} = window.__TRANSLATIONS_CONFIG__ || {url: "/"};

const list = document.querySelector("#list-container");
const buttons = list.querySelectorAll('.btn-save');

buttons.forEach(function (element) {
    element.addEventListener("click", (e) => {
        const clickedElem = e.target;

        const row = clickedElem.closest('tr');
        const domain = clickedElem.getAttribute("data-domain");
        const locale = clickedElem.getAttribute("data-locale");

        const url = '/admin/api/translations/update/'+domain+'/'+locale+'/'+domain;

        axios({
            method: "post",
            url: url,
            data: {
                message: row.querySelector('#message').value,
                key: clickedElem.getAttribute("data-key")
            },
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                toastr.success("Successfully updated!")
            })
            .catch(error => {
                toastr.error(error.response.data.message)
            })
    });
});
