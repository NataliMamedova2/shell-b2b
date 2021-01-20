'use strict';

import 'bootstrap';
import 'bootstrap-datepicker';
import 'bootstrap-tagsinput';
import 'bootstrap-select';
import 'bootstrap-select/dist/js/i18n/defaults-ua_UA.min';
import 'metismenu';
import 'jquery-slimscroll';

import '../themes/js/waves';
import '../themes/js/custom';

import './modules/upload-crop-image'
import './modules/upload-image'
import './modules/clients'
import axios from "axios";
import toastr from "toastr";

$('.js-datepicker').datepicker({
    'format': 'dd-mm-yyyy',
    uiLibrary: 'bootstrap4'
});

const formCollection = document.querySelectorAll("form .dynamic__collection");

if (formCollection) {
    $('select.selectpicker.disabled').on('loaded.bs.select', function (e) {
        const target = e.target;
        if (target.value && $(target).hasClass('disabled') === true) {
            const parent = $(target).closest('.bootstrap-select');
            parent.find('button.dropdown-toggle').addClass('disabled');
        }
    });

    $('select.selectpicker.disabled').on('refreshed.bs.select', function (e) {
        const target = e.target;
        if (target.value && $(target).hasClass('disabled') === true) {
            const parent = $(target).closest('.bootstrap-select');
            parent.find('button.dropdown-toggle').addClass('disabled');
        }
    });

    formCollection.forEach(function (element) {
        let counter = element.querySelectorAll('div.collection-row').length;

        const newProto = getRow(element, counter);
        const allOptionsArray = getSelectOptionsArray(newProto);

        if (allOptionsArray.length === counter) {
            const sddButtonBlock = element.querySelector('#button__block');
            sddButtonBlock.classList.add("hidden");
        }

        const addButton = element.querySelector('.collection-add');
        addButton.addEventListener("click", (e) => {
            const target = e.target;
            const container = target.closest('[data-prototype]');
            const parent = target.closest('#button__block');

            const newProto = getRow(container, counter);
            const allOptionsArray = getSelectOptionsArray(newProto);

            const newSelect = newProto.querySelector('select');
            newSelect.addEventListener("change", e => {
                const selectElements = element.querySelectorAll('select');
                selectElements.forEach(selectElement => {
                    if (selectElement.options[selectElement.selectedIndex].value !== '') {
                        return;
                    }
                    const selectElements = element.querySelectorAll('select');
                    let selectedValues = getSelectedValues(selectElements);
                    updateSelectOptions(selectElement, selectedValues);
                });
            });

            const selectElements = element.querySelectorAll('select');
            if (selectElements) {
                let selectedValues = getSelectedValues(selectElements);
                updateSelectOptions(newSelect, selectedValues);
                if (allOptionsArray.length === counter + 1) {
                    const sddButtonBlock = element.querySelector('#button__block');
                    sddButtonBlock.classList.add("hidden");
                }
            }
            container.insertBefore(newProto, parent);
            $('.selectpicker.disabled').selectpicker('refresh');

            element.querySelectorAll('select.selectpicker.disabled').forEach(disabledSelect => {
                $(disabledSelect).on('changed.bs.select', function (e) {
                    const target = e.target;
                    const collection = target.closest('.dynamic__collection');

                    collection.querySelectorAll('select.selectpicker.disabled').forEach(e => {
                        if (e.selectedIndex > 0) {
                            const parent = $(e).closest('.bootstrap-select');
                            parent.find('button.dropdown-toggle').addClass('disabled');
                        }
                    });
                });
                $(disabledSelect).on('refreshed.bs.select', function (e) {
                    const target = e.target;
                    const collection = target.closest('.dynamic__collection');

                    collection.querySelectorAll('select.selectpicker.disabled').forEach(e => {
                        if (e.selectedIndex > 0) {
                            const parent = $(e).closest('.bootstrap-select');
                            parent.find('button.dropdown-toggle').addClass('disabled');
                        }
                    });
                });
            });
            counter++;
        });

        $(element).on('click', '.collection-delete', function (event) {
            $(this).closest('.collection-row').remove();
            counter--;

            const sddButtonBlock = element.querySelector('#button__block');
            sddButtonBlock.classList.remove("hidden");
        });
    });

    function getSelectOptionsArray(rowElement)
    {
        const currentSelect = rowElement.querySelector('select');

        let availableOptions = [];
        for (let i = 0; i < currentSelect.options.length; i++) {
            let option = currentSelect.options[i];
            if (option.value) {
                availableOptions.push(option);
            }
        }

        return availableOptions;
    }

    function updateSelectOptions(select, selectedValues)
    {
        for (let i = 0; i < selectedValues.length; i++) {
            for (let j = 0; j < select.options.length; j++) {
                if (select.options[j].value === selectedValues[i]) {
                    select.remove(j);
                }
            }
        }
        $('select.selectpicker.disabled').selectpicker('refresh');
    }

    function getSelectedValues(selectElements)
    {
        let alreadySelectedValues = [];
        selectElements.forEach(function (element) {
            if (element.options[element.selectedIndex].value !== '') {
                alreadySelectedValues.push(element.options[element.selectedIndex].value);
            }
        });

        return alreadySelectedValues;
    }

    function getRow(container, counter)
    {
        let proto = container.getAttribute('data-prototype');
        const protoName = container.getAttribute('data-prototype-name') || '__name__';
        const id = container.getAttribute('id');

        // Set field id
        const idRegexp = new RegExp(id + '_' + protoName, 'g');
        proto = proto.replace(idRegexp, id + '_' + counter);

        // Set field name
        const parts = id.split('_');
        const nameRegexp = new RegExp(parts[parts.length - 1] + '\\[' + protoName, 'g');
        proto = proto.replace(nameRegexp, parts[parts.length - 1] + '[' + counter);

        let newProto = document.createElement('div');
        newProto.innerHTML = proto;

        return newProto;
    }
}

const supplyTypesSelect = document.getElementById('supplyTypes');
if (supplyTypesSelect) {
    supplyTypesSelect.addEventListener("change", (e) => {

        const select = document.getElementById('supplies');
        select.setAttribute('disabled', 'disabled');
        $(select).selectpicker('refresh');

        const target = e.target;

        let types = [], option;
        const length = target.options.length;
        for (let i = 0; i < length; i++) {
            option = target.options[i];

            if (true === option.selected) {
                types.push(option.value);
            }
        }

        if (types.length === 0) {
            removeOptions(select);
            select.setAttribute('disabled', 'disabled');
            $(select).selectpicker('refresh');
            return;
        }

        axios.get('/admin/api/v1/transactions/supplies', {
            params: {
                type: types
            }
        })
            .then(function (response) {
                select.removeAttribute('disabled');
                removeOptions(select);

                response.data.map(item => {
                    let opt = document.createElement("option");
                    opt.value = item.code;
                    opt.text = item.name;

                    select.options.add(opt, null);
                });

                $(select).selectpicker('refresh');
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(function () {
                // always executed
            });
    });

    function removeOptions(select)
    {
        for (let i = select.options.length - 1; i >= 0; i--) {
            select.remove(i);
        }
    }
}
