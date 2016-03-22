!function(global, $) {
    'use strict';

    var MeschAlertDialog = {
        
        show: function(title, message, onCloseFn) {
            $('<div id="ccm-mesch-dialog" class="ccm-ui"><div class="container-fluid">'
                +'<div class="row"><div class="col-xs-offset-1 col-xs-10">'
                +'<div class="spacer-row-2"></div><h4>' + message + '</h4><div class="spacer-row-2"></div>'
                +'</div></div></div></div>').dialog({
                title: title,
                dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper',
                autoOpen: true,
                width: 'auto',
                resizable: false,
                modal: true,
                onDestroy: onCloseFn,
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        },
        confirm : function(title, message, confirm, cancel, okCallback, cancelCallback) {
            $('<div id="ccm-mesch-dialog" class="ccm-ui"><div class="container-fluid">'
                +'<div class="row"><div class="col-xs-offset-1 col-xs-10">'
                +'<div class="spacer-row-2"></div><h4>' + message + '</h4><div class="spacer-row-2"></div>'
                +'</div></div></div></div>').dialog({
                title: title,
                dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper ccm-ui',
                autoOpen: true,
                width: '400px',
                resizable: false,
                modal: true,
                buttons: {
                    Confirm:  {
                        click: function () {
                            okCallback();
                            $(this).dialog("close");
                        },
                        text: confirm,
                        class: 'btn btn-danger btn-sm'
                    },
                    cancel: {
                        click: function () {
                            if(cancelCallback){
                                cancelCallback();
                            }
                            $(this).dialog("close");
                        },
                        text: cancel,
                        class: 'btn btn-default btn-sm'
                    }
                },
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        },
    };

    global.MeschAlertDialog = MeschAlertDialog;

}(this, $);
            


