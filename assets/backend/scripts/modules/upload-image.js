'use strict';

import Dropify from '../../themes/plugins/dropify';
import axios from 'axios';

const {uploadUrl} = window.__UPLOAD_IMAGE_CONFIG__ || {uploadUrl: '/'};

const imageDropifyElements = document.querySelectorAll(".dropify-image");

if (imageDropifyElements) {

    Dropify.prototype.isImage = function () {
        return true;
    };

    Dropify.prototype.onFileReady = function (event, src) {
        this.input.off('dropify.fileReady', this.onFileReady);

        if (this.errorsEvent.errors.length === 0) {

            if (this.input.hasClass('dropify-image') === true) {
                UploadImage.setDropify(this.input, this);
                UploadImage.file = this.file.object;

                let reader = new FileReader();
                let file = this.file.object;

                reader.readAsDataURL(file);

                reader.onload = function (_file) {
                    UploadImage.src = _file.target.result;
                    UploadImage.fileIsChanged = true;
                    UploadImage.save(_file.target.result);
                }.bind(this);

            } else {
                this.setPreview(src, this.file.name);
            }

        } else {
            this.input.trigger(this.errorsEvent, [this]);
            for (var i = this.errorsEvent.errors.length - 1; i >= 0; i--) {
                var errorNamespace = this.errorsEvent.errors[i].namespace;
                var errorKey = errorNamespace.split('.').pop();
                this.showError(errorKey);
            }

            if (typeof this.errorsContainer !== "undefined") {
                this.errorsContainer.addClass('visible');

                var errorsContainer = this.errorsContainer;
                setTimeout(function () {
                    errorsContainer.removeClass('visible');
                }, 1000);
            }

            this.wrapper.addClass('has-error');
            this.resetPreview();
            this.clearElement();
        }
    };
    imageDropifyElements.forEach(function (element) {
        $(element).dropify();
    });
}

const UploadImage = function () {

    return {

        el: null,

        src: null,
        dropify: null,
        dropifyElement: null,

        image: null,
        file: null,
        fileIsChanged: false,

        init: function (options) {
            Object.assign(this, options);
        },

        getData() {
            return {
                'file': this.src
            };
        },

        setData(data) {
            const _this = this;
            $.each(data, function (k, v) {
                let input = _this.dropifyElement.closest('.form-group').find("input[data-name='" + k + "']");
                if (input && input.attr('type') !== 'file') {
                    input.attr('value', v);
                }
            });
        },

        save: function () {
            const errorsBlockEl = this.dropifyElement.closest('.form-group').find('.errors__block.has-error');
            errorsBlockEl.empty();

            axios({
                method: "post",
                url: uploadUrl,
                data: JSON.stringify(this.getData()),
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => {
                    let result = response.data;

                    this.setData(result);
                    this.updateDropifyPreview(result.file, result.fileName);

                    this.fileIsChanged = false;
                })
                .catch(error => {
                    if (error && error.response) {
                        let errors = error.response.data;

                        this.dropify.hideLoader();

                        if (error.response.status === 500) {
                            alert(error.response.statusText);
                            return;
                        }

                        $.each(errors, function (attribute, values) {
                            $.each(values, function (k, value) {
                                errorsBlockEl.append('<span class="help-block">' + value + '</span>');
                            });
                        });
                    }
                })
        },

        setDropify(el, dropify) {
            this.dropifyElement = el;
            this.dropify = dropify || null;
        },

        setEndpoint(endpoint) {
            this.endpoint = endpoint;
        },

        updateDropifyPreview(file, fileName) {
            $(this.dropifyElement).attr('data-default-file', file);

            $(this.dropifyElement).parent().find('img').attr('src', file);

            if (this.dropify && fileName) {
                this.dropify.file.name = fileName;
                this.dropify.setPreview(file, fileName);
            }
        }
    };

}();