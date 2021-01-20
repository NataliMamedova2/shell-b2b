'use strict';

import axios from 'axios';
import toastr from 'toastr';

const list = document.querySelector("#site-config__list-container");
const buttons = list.querySelectorAll('.btn-save');

buttons.forEach(function (element) {
    element.addEventListener("click", (e) => {
        const clickedElem = e.target.closest('button');

        const url = clickedElem.getAttribute("data-url");
        const row = clickedElem.closest('.form-group');

        axios({
            method: "post",
            url: url,
            data: {
                value: row.querySelector('#value').value
            },
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                toastr.success("Successfully updated!")
            })
            .catch(error => {

                if(error.response) {
                    toastr.error(error.response.data.message)
                }
            })
    });
});
