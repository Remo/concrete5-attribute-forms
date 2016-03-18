!function(global, $) {
    "use strict";
    
    global.ATTR_FORM_BLOCK = {
        params: {},
        formActionsContainer: false,
        _templateEntry: false,
        init: function(params){
            this.params = params;

            this.formActionsContainer = $('.ccm-atform-action-entries');
            this._templateEntry = _.template($('#formActionTemplate').html());

            // Add Actions
            for(var i = 0; i < this.params.actions.length; i++){
                var action = this.params.actions[i];
                this.addEntry(action);
            }
            
            this.doSortCount();
            this.setupAddEntryAction();
            this.setupEditEntryAction();
            this.setupDeleteEntryAction();
            this.setupSort();

            this.attachRedactor(this.formActionsContainer.find('.redactor-content'));
            $('#ccm-tab-content-atform-extra').on('change', 'select.actionTypeSelect', function(e){
                var actionID = $(this).closest('.ccm-atform-action-entry').find('.ccm-atform-action-id').val();
                var entry = ATTR_FORM_BLOCK.getActionByID(actionID);

                $.post(CCM_DISPATCHER_FILENAME + '/ccm/attribute_forms/tools/form/action_type/render/'+$(this).val()+'/form',
                    {"value": JSON.stringify(entry)}, function(data){
                    $("#actionTypeForm"+actionID).html(data);
                });
            });
            
            this.formActionsContainer.find('.ccm-atform-action-entry').each(function() {
                var $thiz = $(this);
                setTimeout(function(){$thiz.find('.form-group').not('.first').hide();},1000);
            });
        },
        attachFileManagerLaunch: function($obj) {
            $obj.click(function() {
                var oldLauncher = $(this);
                ConcreteFileManager.launchDialog(function(data) {
                    ConcreteFileManager.getFileDetails(data.fID, function(r) {
                        jQuery.fn.dialog.hideLoader();
                        var file = r.files[0];
                        oldLauncher.html(file.resultsThumbnailImg);
                        oldLauncher.next('.image-fID').val(file.fID);
                    });
                });
            });
        },
        attachRedactor: function($obj){
            if($obj.length > 0){
                $obj.redactor({
                    minHeight: 200,
                    'concrete5': {
                        filemanager: this.params.canAccessFileManager,
                        sitemap: this.params.canAccessSitemap,
                        lightbox: true
                    }
                });
            }
        },
        doSortCount: function() {
            this.formActionsContainer.find('.ccm-atform-action-entry').each(function(index) {
                $(this).find('.ccm-atform-action-entry-sort').val(index);
            });
        },
        setupSort: function(){
            this.formActionsContainer.sortable({
                placeholder: "ui-state-highlight",
                axis: "y",
                handle: "i.fa-arrows",
                cursor: "move",
                update: function() {
                    ATTR_FORM_BLOCK.doSortCount();
                }
            });
        },
        setupEditEntryAction: function(){
            this.formActionsContainer.on('click','.ccm-edit-atform-action', function() {
                if ($(this).data('entryEditText') === $(this).text()) {
                    $(this).closest('.ccm-atform-action-entry').find('.form-group').not('.first').slideDown();
                    $(this).text($(this).data('entryCloseText'));
                } else if ($(this).data('entryCloseText') === $(this).text()) {
                    $(this).closest('.ccm-atform-action-entry').find('.form-group').not('.first').slideUp();
                    $(this).text($(this).data('entryEditText'));
                }
            });  
        },
        setupDeleteEntryAction: function() {
            this.formActionsContainer.on('click','.ccm-delete-atform-action-entry', function() {
                var deleteIt = confirm(ATTR_FORM_BLOCK.params.confirmMessage);
                if (deleteIt === true) {
                    $(this).closest('.ccm-atform-action-entry').remove();
                    ATTR_FORM_BLOCK.doSortCount();
                }
            });
        },
        setupAddEntryAction: function(){
          $('.ccm-add-atform-action-entry').click(function() {
                var thisModal = $(this).closest('.ui-dialog-content');
                ATTR_FORM_BLOCK.addEntry({
                    ID: ATTR_FORM_BLOCK.uniqid('atf_'),
                    actionName: '',
                    actionType: '',
                    executionOrder: '',
                });

                $('.ccm-atform-action-entry').not('.entry-closed').each(function() {
                    $(this).addClass('entry-closed');
                    var thisEditButton = $(this).closest('.ccm-atform-action-entry').find('.btn.ccm-edit-atform-action');
                    thisEditButton.text(thisEditButton.data('entryEditText'));
                });

                var newEntry = ATTR_FORM_BLOCK.formActionsContainer.find('.ccm-atform-action-entry').last();
                var closeText = newEntry.find('.btn.ccm-edit-atform-action').data('entryCloseText');
                newEntry.removeClass('entry-closed').find('.btn.ccm-edit-atform-action').text(closeText);
                thisModal.scrollTop(newEntry.offset().top);

                ATTR_FORM_BLOCK.attachRedactor(newEntry.find('.redactor-content')); 
                ATTR_FORM_BLOCK.doSortCount();
            });  
        },
        addEntry: function(entry){
            var actionID = entry.ID;
            this.formActionsContainer.append(this._templateEntry({action: entry, actionTypes: this.params.actionTypes}));

            var actionType = entry.actionType;
            if(actionType == ''){
                actionType = $('#actionType'+actionID).val();
            }

            $.post(CCM_DISPATCHER_FILENAME + '/ccm/attribute_forms/tools/form/action_type/render/'+actionType+'/form',
                {value: JSON.stringify(entry)}, function(data){
                $("#actionTypeForm"+actionID).html(data);
            });
        },
        uniqid: function (prefix, more_entropy) {
            if (typeof prefix === 'undefined') {
                prefix = '';
            }

            var retId;
            var formatSeed = function (seed, reqWidth) {
                seed = parseInt(seed, 10).toString(16); // to hex str
                if (reqWidth < seed.length) { // so long we split
                    return seed.slice(seed.length - reqWidth);
                }
                if (reqWidth > seed.length) { // so short we pad
                    return Array(1 + (reqWidth - seed.length)).join('0') + seed;
                }
                return seed;
            };

            // BEGIN REDUNDANT
            if (!this.php_js) {
                this.php_js = {};
            }
            // END REDUNDANT
            if (!this.php_js.uniqidSeed) { // init seed with big random int
                this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
            }
            this.php_js.uniqidSeed++;

            retId = prefix; // start with prefix, add current milliseconds hex string
            retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
            retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
            if (more_entropy) {
                // for more entropy we add a float lower to 10
                retId += (Math.random() * 10).toFixed(8).toString();
            }

            return retId;
        },
        getActionByID: function(actionID) {
            for (var i = 0, len = this.params.actions.length; i < len; i++) {
                if (this.params.actions[i].ID === actionID)
                    return this.params.actions[i];
            }
            return {ID: actionID};
        }
    };
}(window, $);