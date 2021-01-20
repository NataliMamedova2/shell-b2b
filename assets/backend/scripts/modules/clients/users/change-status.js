'use strict';

import axios from 'axios';
import toastr from 'toastr';
import 'bootstrap/js/modal';
import modal from '../../../libs/modal';

const selectors = document.querySelectorAll('.change-status');

selectors.forEach(function (element) {
    element.addEventListener("click", (e) => {
        const button = e.target;

        const status = button.getAttribute('data-value');
        const confirmText = button.getAttribute('data-confirm');
        let title = '';

        if (status === 'active') {
            title = button.getAttribute('data-blockedtext');
        } else {
            title = button.getAttribute('data-activetext');
        }

        modal({
            title: title,
            body: confirmText,
            size: 'small',
            actions: [
                {
                    label: 'Закрити',
                    cssClass: 'btn-default',
                    onClick: (e) => {
                        $(e.target).closest('.modal').modal('hide');
                    }
                },
                {
                    label: 'Так',
                    cssClass: 'btn-success',
                    onClick: (e) => {
                        const url = button.getAttribute("data-url");
                        const data = {
                            status: button.getAttribute('data-value'),
                        };
                        send(button, url, data);

                        $(e.target).closest('.modal').modal('hide');
                    }
                },
            ]
        });
    });
});

function send(button, url, data) {
    axios({
        method: "post",
        url: url,
        data: data,
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => {
            let message = button.getAttribute('data-activemessage');
            if (data.status === 'active') {
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');

                button.setAttribute("data-value", "blocked");
                button.innerText = button.getAttribute('data-activetext');

            } else {
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');

                button.setAttribute("data-value", "active");
                button.innerText = button.getAttribute('data-blockedtext');

                message = button.getAttribute('data-blockedmessage');
            }

            toastr.success(message);
        })
        .catch(error => {
            const errors = error.response.data.errors;
            toastr.error(errors.status)
        })
}
