'use strict';

import Dropify from '../../themes/plugins/dropify';
import Cropper from 'cropperjs';
import axios from 'axios';

const {uploadUrl, cropImageUrl} = window.__CROPPER_CONFIG__ || {uploadUrl: '/', cropImageUrl: '/'};

const cropperDropifyElements = document.querySelectorAll(".dropify-cropper");

if (cropperDropifyElements) {
    Dropify.prototype.isImage = function () {
        return true;
    };

    Dropify.prototype.onFileReady = function (event, src) {
        this.input.off('dropify.fileReady', this.onFileReady);

        if (this.errorsEvent.errors.length === 0) {

            if (this.input.hasClass('dropify-cropper') === true) {
                Upload.renderModal(event, src, this.file);
                Upload.setDropify(this.input, this);
                Upload.fileIsChanged = true;
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

    Dropify.prototype.clearElement = function()
    {
        if (this.errorsEvent.errors.length === 0) {
            var eventBefore = $.Event("dropify.beforeClear");
            this.input.trigger(eventBefore, [this]);

            if (eventBefore.result !== false) {
                clearInput(this);

                this.input.trigger($.Event("dropify.afterClear"), [this]);
            }
        } else {
            clearInput(this);
        }

        function clearInput(dropify) {
            dropify.resetFile();
            dropify.input.val('');
            dropify.input.attr('data-default-file', '');
            dropify.input.attr('data-original-file', '');
            dropify.input.attr('data-cropper-data', '');
            dropify.resetPreview();

            Upload.clearData(dropify.element.closest('.form-group'));
        }
    };

    cropperDropifyElements.forEach(function (element) {
        $(element).dropify();

        element.addEventListener("click", (e) => {
            const clickedElem = e.target;

            const cropperData = JSON.parse(clickedElem.getAttribute('data-cropper-data'));
            const cropperOptions = JSON.parse(clickedElem.getAttribute('data-cropper-options'));
            const originalFile = clickedElem.getAttribute('data-original-file');

            Upload.setCropperData(cropperData);
            Upload.setCropperOptions(cropperOptions);

            Upload.setDropify(clickedElem);

            if (originalFile && originalFile.length > 0) {
                e.preventDefault();
                Upload.renderModal(e, originalFile);
            }
        });
    });
}

const Upload = function () {

    const renderModal = function (body) {
        return showBSModal({
            title: "Медіа",
            size: "large",
            body: body,
            onHide: function (e) {
                $(this).data('bs.modal', null);
                $(this).remove()
            },
            actions: [
                {
                    label: 'Зберегти',
                    cssClass: 'btn-success',
                    onClick: function (e) {
                        Upload.save();
                    }
                },
                {
                    label: 'Закрити',
                    cssClass: 'btn-default',
                    onClick: function (e) {
                        $(e.target).parents('.modal').modal('hide');
                    }
                },
            ]
        });
    };

    return {

        el: null,
        modal: null,

        dropify: null,
        dropifyElement: null,

        image: null,
        file: null,
        fileIsChanged: false,

        cropper: null,
        cropperData: {},

        cropperOptions: {
            aspectRatio: 16 / 9,
            preview: '.img-preview',
            autoCropArea: true,
            movable: false,
            strict: false,
            guides: false,
            highlight: false,
            center: true,
            scalable: false,
            zoomable: false,
            crop: function (e) {
                var data = e.detail;

                Upload.modal.find('.dataX').val(Math.round(data.x));
                Upload.modal.find('.dataY').val(Math.round(data.y));
                Upload.modal.find('.dataHeight').val(Math.round(data.height));
                Upload.modal.find('.dataWidth').val(Math.round(data.width));
            },
            ready: function () {
                Upload.cropper.setData(Upload.cropperData);
            }
        },

        init: function (options) {
            Object.assign(this, options);

            this.modal.find('#original__image').attr('src', this.src);
            this.cropperOptions.preview = this.modal.find('.img-preview');
        },

        initCropper: function () {

            if (this.cropper !== null) {
                this.destroyCropper();
            }
            this.cropper = new Cropper(this.image, this.cropperOptions);

            let _this = this;
            this.modal.find('.docs-buttons')[0].onclick = function (e) {
                let target = e.target || e.currentTarget;
                let result;
                let input;
                let data;

                if (!_this.cropper) {
                    return;
                }

                while (target !== this) {
                    if (target.getAttribute('data-method')) {
                        break;
                    }

                    target = target.parentNode;
                }

                if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
                    return;
                }

                data = {
                    method: target.getAttribute('data-method'),
                    target: target.getAttribute('data-target'),
                    option: target.getAttribute('data-option') || undefined,
                    secondOption: target.getAttribute('data-second-option') || undefined
                };
                if (data.method) {

                    result = _this.cropper[data.method](data.option, data.secondOption);

                    if (typeof result === 'object' && result !== _this.cropper && input) {
                        try {
                            input.value = JSON.stringify(result);
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }
            };
        },

        destroyCropper() {
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
                this.cropperData = {};
            }
        },

        getData() {

            return {
                'file': this.src,
                'fileName': this.modal.find('input[data-name=fileName]').val(),
                'path': this.modal.find('input[data-name=path]').val(),
                'cropData': {
                    'x': this.modal.find('.dataX').val(),
                    'y': this.modal.find('.dataY').val(),
                    'width': this.modal.find('.dataWidth').val(),
                    'height': this.modal.find('.dataHeight').val(),
                },
            };
        },

        setData(data) {
            const _this = this;
            $.each(data, function (k, v) {
                let input = _this.el.find("input[data-name='" + k + "']");
                if (input && input.attr('type') !== 'file') {
                    input.attr('value', v);
                }
            });

            if (data.cropData) {
                let cropper = data.cropData;

                _this.el.find("input.dataX").val(cropper.x);
                _this.el.find("input.dataY").val(cropper.y);
                _this.el.find("input.dataWidth").val(cropper.width);
                _this.el.find("input.dataHeight").val(cropper.height);
            }
        },

        clearData(el) {
            el = $(el);
            $.each(el.find("input[type=hidden]"), function (k, input) {
                input.value = null;
            });
        },

        save: function () {
            const errorsBlockEl = this.modal.find('.errors__block.has-error');
            errorsBlockEl.empty();

            if (this.fileIsChanged === false) {
                axios({
                    method: "post",
                    url: cropImageUrl,
                    data: JSON.stringify(this.getData()),
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => {
                        let result = response.data;

                        this.setData(result);
                        this.updateDropifyPreview(result.file, result.fileName);

                        this.modal.modal('hide');
                    });

                return;
            }

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

                    this.modal.modal('hide');

                    this.setData(result);
                    this.updateDropifyPreview(result.file, result.fileName);
                    this.updateDropifyData(result.originalFile, result.cropData);

                    let saveEvent = $.Event("saved");
                    this.modal.trigger(saveEvent, [result]);

                    this.fileIsChanged = false;
                })
                .catch(error => {
                    let errors = error.response.data;

                    if (error.response.status === 500) {
                        alert(error.response.statusText);
                        return;
                    }

                    $.each(errors, function (attribute, values) {
                        $.each(values, function (k, value) {
                            errorsBlockEl.append('<span class="help-block">' + value + '</span>');
                        });
                    });
                })
        },

        renderModal: function (event, src, file) {
            let parent = $(event.currentTarget).closest('.form-group');
            let body = parent.find('#modal-form');

            let modal = renderModal(body.html());

            this.init({
                el: parent,
                modal: modal,
                image: modal.find('img#original__image')[0],
                src: src,
                file: file || null
            });

            document.getElementById("inputImage").value = "";

            const _this = this;
            modal.on('shown.bs.modal', function () {
                _this.initCropper()
            });

            modal.on('hidden.bs.modal', function () {
                _this.destroyCropper()
            });

            return modal;
        },

        onChange: function (e) {
            let files = e.files;

            if (!Upload.image) {
                Upload.modal = $(e).closest('.modal');
                Upload.image = $(e).closest('.cropper__block').find('img')[0];
            }

            if (files && files[0]) {
                let reader = new FileReader();
                let image = new Image();
                let file = files[0];

                reader.readAsDataURL(file);

                reader.onload = function (_file) {

                    image.src = _file.target.result;
                    image.onload = function () {
                        Upload.destroyCropper();

                        Upload.image.src = image.src;
                        Upload.src = image.src;
                        Upload.file = file;

                        Upload.initCropper();
                        Upload.cropper.reset();
                        Upload.fileIsChanged = true;
                    };
                }.bind(this);
            }
        },

        setCropperData: function (cropperData) {
            this.cropperData = cropperData;
        },

        setCropperOptions: function (options) {
            Object.assign(this.cropperOptions, options);
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
        },

        updateDropifyData(originalFile, cropperData) {
            $(this.dropifyElement).attr('data-original-file', originalFile);
            $(this.dropifyElement).attr('data-cropper-data', JSON.stringify(cropperData));
        }
    };

}();
window.upload = Upload;

window.showBSModal = function self(options) {

    var options = $.extend({
        title: '',
        body: '',
        remote: false,
        backdrop: 'static',
        size: false,
        onShow: false,
        onHide: false,
        actions: false
    }, options);

    self.onShow = typeof options.onShow == 'function' ? options.onShow : function () {
    };
    self.onHide = typeof options.onHide == 'function' ? options.onHide : function () {
    };

    if (self.$modal === undefined) {
        self.$modal = $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content"></div></div></div>').appendTo('body');

        self.$modal.on('shown.bs.modal', function (e) {
            self.onShow.call(this, e);
        });
    }
    self.$modal.on('hidden.bs.modal', function (e) {
        self.onHide.call(this, e);
    });

    let modalClass = {
        small: "modal-sm",
        large: "modal-lg"
    };

    self.$modal.data('bs.modal', false);
    self.$modal.find('.modal-dialog').removeClass().addClass('modal-dialog ' + (modalClass[options.size] || ''));
    self.$modal.find('.modal-content').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title">${title}</h4></div><div class="modal-body">${body}</div><div class="modal-footer"></div>'.replace('${title}', options.title).replace('${body}', options.body));

    let footer = self.$modal.find('.modal-footer');
    if (Object.prototype.toString.call(options.actions) === "[object Array]") {
        for (var i = 0, l = options.actions.length; i < l; i++) {
            options.actions[i].onClick = typeof options.actions[i].onClick == 'function' ? options.actions[i].onClick : function () {
            };
            $('<button type="button" class="btn ' + (options.actions[i].cssClass || '') + '">' + (options.actions[i].label || '{Label Missing!}') + '</button>').appendTo(footer).on('click', options.actions[i].onClick);
        }
    } else {
        $('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>').appendTo(footer);
    }

    self.$modal.modal(options);

    return self.$modal;
};