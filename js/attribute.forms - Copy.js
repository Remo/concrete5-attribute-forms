var attributeFormsApp = {
    initFormTypesList: function (params) {
       $('#mesch-ftypes-list').on('click', '.delete-form-type',function(e){
            var $thiz = $(this);
            var ftypeName = $thiz.closest('tr').find('.form-type-name').text();
            MeschAlertDialog.confirm('', params.deleteFType.msg+'<br/>"'+ftypeName+'"',
                                         params.deleteFType.ok,
                                         params.deleteFType.cancel,
                                         function(){
                                             window.location.href = $thiz.attr('href');
                                         }
                                    );
            
            return false;
        });  
    },
    initFormTypesView: function (params) {
        this.initRedactor();
        
        // setup underscore template
        _.templateSettings.variable = "rc";
        this.templateAttributes = _.template(
            $("script.attributes-template").html()
        );
        this.data = {
            attributeKeys: params.attributeKeys,
            attributesData: params.selectedAttributes,
            attributeOptions: params.attributeOptions
        };
        
        if(!this.data.attributesData){
            this.data.attributesData = {
                formPages: []
            };
        }
        this.renderAttributes();

        // attribute actions
        $("#attributes-container").on("click", ".new-page-add", function (event) {
            event.preventDefault();
            var newPageName = $(this).closest("tr").find("input[name=new-page]").val();
            attributeFormsApp.data.attributesData.formPages.push({name: newPageName, attributes: []});
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("click", ".remove-page", function (event) {
            var index = $(this).closest('tr.form-page').data("index");
            attributeFormsApp.data.attributesData.formPages.splice(index, 1);
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("click", ".new-attribute-add", function (event) {
            event.preventDefault();
            var $newAttribute = $(this).closest("tr").find("select option:selected"),
                newAttributeName = $newAttribute.text(),
                newAttributeValue = $newAttribute.val(),
                atHandle = $newAttribute.data('athandle'),
                pageIndex = $(this).closest('tr.form-page').data("index");
                
            attributeFormsApp.data.attributesData.formPages[pageIndex].attributes.push({
                akName: newAttributeName,
                akID: newAttributeValue,
                atHandle: atHandle,
                required: false
            });
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("click", ".remove-attribute", function (event) {
            var pageIndex = $(this).closest('tr.form-page').data("index"),
                index = $(this).closest('tr.form-page-attribute').data("index");

            attributeFormsApp.data.attributesData.formPages[pageIndex].attributes.splice(index, 1);
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("change", ".attribute-required", function (event) {
            var pageIndex = $(this).closest('tr.form-page').data("index"),
                index = $(this).closest('tr.form-page-attribute').data("index");
                
            attributeFormsApp.data
                    .attributesData
                    .formPages[pageIndex]
                    .attributes[index].required = $(this).is(':checked');
            attributeFormsApp.updateFormData();
        });
        
        $("#attributes-container").on("change", ".attribute-option", function (event) {
            var pageIndex = $(this).closest('tr.form-page').data("index"),
                index = $(this).closest('tr.form-page-attribute').data("index");
                
            var attr = attributeFormsApp.data.attributesData.formPages[pageIndex].attributes[index];
            var options = attr.options ? attr.options : {};
            
            
            var optionKey = $(this).data('name');
            var isUnique  = attributeFormsApp.data.attributeOptions[attr.atHandle][optionKey].unique;
            if($(this).is(':checked') && isUnique){
                attributeFormsApp.data.attributesData.formPages.forEach(function(page, i) {
                    page.attributes.forEach(function(attribute, j){
                        if(attribute.options){
                            attributeFormsApp.data
                                    .attributesData
                                    .formPages[i]
                                    .attributes[j]
                                    .options[optionKey] = false;
                        }
                    });
                });
            }
            options[optionKey] = $(this).is(':checked');
            attributeFormsApp.data.attributesData
                    .formPages[pageIndex]
                    .attributes[index]
                    .options = options;
            if($(this).is(':checked') && isUnique){
                attributeFormsApp.renderAttributes();
            }else{
                attributeFormsApp.updateFormData();
            }
        });
    },
    initRedactor: function () {
        $('.ccm-advanced-editor').redactor({
            'plugins': ['concrete5']
        });
    },
    renderAttributes: function() {
        $("#attributes-container").html(this.templateAttributes(this.data));

        // make attribute list sortable
        $(".form-pages").sortable({
            start: function(event, ui) {
                ui.item.startPos = ui.item.index();
            },
            stop: function(event, ui) {
                attributeFormsApp.data.attributesData.formPages.move(ui.item.startPos, ui.item.index());
                attributeFormsApp.renderAttributes();
            }
        });
        
        $(".form-page-attributes").sortable({
            start: function(event, ui) {
                ui.item.startPos = ui.item.index();
            },
            stop: function(event, ui) {
                var pageIndex = ui.item.closest('tr.form-page').data("index");
                attributeFormsApp.data.attributesData.formPages[pageIndex].attributes.move(ui.item.startPos, ui.item.index());
                attributeFormsApp.renderAttributes();
            }
        });
        this.updateFormData();
    },
    updateFormData: function(){
        // save JSON in form
        $("#attributes").val(JSON.stringify(this.data.attributesData));
    }
};
Array.prototype.move = function (from, to) {
    this.splice(to, 0, this.splice(from, 1)[0]);
};