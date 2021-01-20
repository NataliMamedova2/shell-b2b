'use strict';

import axios from 'axios';
import toastr from 'toastr';
import modal from '../../libs/modal';

const formTemplate = '<form action="">\n' +
    '                    <div class="form-group">\n' +
    '                        <label for="recipient-name" class="control-label">\n' +
    '                            Email*\n' +
    '                        </label>\n' +
    '                        <input type="email" required="" class="form-control" id="email" name="email">\n' +
    '                    </div>\n' +
    '                </form>';

const list = document.querySelector("#clients-list");
if (list) {

    const buttons = list.querySelectorAll('#send_register_link');

    buttons.forEach(function (element) {
        element.addEventListener("click", (e) => {

            const button = e.target;
            const url = button.getAttribute("data-path");

            const data = {
                email: button.getAttribute('data-email') || '',
            };

            modal({
                title: button.getAttribute('data-client'),
                body: formTemplate,
                onShow: (e) => {
                    const inputs = $(e.target)[0].querySelectorAll('input');
                    inputs.forEach(function (element) {
                        if (data.hasOwnProperty(element.name)) {
                            element.value = data[element.name];
                        }
                    });

                    $(e.target).find('.datepicker-autoclose').datepicker({
                        format: 'yyyy-mm-dd',
                        uiLibrary: 'bootstrap4',
                        autoclose: true,
                        todayHighlight: true
                    });
                },
                onHide: (e) => {
                    $(this).data('bs.modal', null);
                    $(this).remove()
                },
                actions: [
                    {
                        label: 'Закрити',
                        cssClass: 'btn-default',
                        onClick: (e) => {
                            $(e.target).closest('.modal').modal('hide');
                        }
                    },
                    {
                        label: 'Відправити',
                        cssClass: 'btn-success',
                        onClick: (e) => {
                            const form = $(e.target).closest('.modal').find('form')[0];
                            sendLink(button, url, form, $(e.target).closest('.modal'));
                        }
                    },
                ]
            });
        });
    });

    function sendLink(button, url, form, modal) {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(function (element) {
            const row = element.closest('.form-group');
            row.classList.remove('has-error');

            const errorBlock = row.querySelector('.help-block');
            if (errorBlock) {
                errorBlock.remove();
            }
        });

        axios({
            method: "post",
            url: url,
            data: {
                email: form.querySelector('#email').value,
            },
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => {
                toastr.success("Message sent successfully!");

                const data = response.data;

                if (data.id) {
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                    button.setAttribute('data-email', data.email);
                    button.setAttribute('data-path', '/admin/api/v1/company/resend-register-link/' + data.id);
                    button.innerHTML = '<i class="fa fa-external-link-square" aria-hidden="true"></i>&nbsp;Відправити посилання повторно';
                }

                modal.modal('hide');
            })
            .catch(error => {
                if (!error || !error.response) {
                    return;
                }
                const errors = error.response.data.errors;
                for (let key in errors) {
                    inputs.forEach(function (element) {
                        if (element.getAttribute('name') === key) {
                            const row = element.closest('.form-group');
                            row.classList.add('has-error');

                            const el = document.createElement("span");
                            el.classList.add('help-block');
                            el.innerHTML = errors[key];
                            element.parentNode.insertBefore(el, element.nextSibling);
                        }
                    });
                }
            })
    }
}
