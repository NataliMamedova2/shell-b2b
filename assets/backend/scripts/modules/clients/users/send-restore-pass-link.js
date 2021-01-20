'use strict';

import axios from 'axios';
import toastr from 'toastr';

const list = document.querySelector("#clients-users-list");
if (list) {

    const buttons = list.querySelectorAll('.send-restore-pass-link');

    buttons.forEach(function (element) {
        element.addEventListener("click", (e) => {

            const button = e.target;
            const url = button.getAttribute("data-href");

            const data = {
                username: button.getAttribute('data-username') || '',
            };

            sendLink(button, url, data);
        });
    });

}
function sendLink(button, url, data) {

    axios({
        method: "post",
        url: url,
        data: data,
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => {
            toastr.success("Message sent successfully!");

            $('#success-modal').modal('show');
        })
        .catch(error => {
            $('#error-modal').modal('show');
        })
}
