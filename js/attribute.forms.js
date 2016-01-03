var attributeFormsApp = {
    initFormTypesView: function (params) {
        this.initRedactor();
        
        // setup underscore template
        _.templateSettings.variable = "rc";
        this.templateAttributes = _.template(
            $("script.attributes-template").html()
        );
        
        this.attributesData = params.selectedAttributes;
        if(!this.attributesData){
            this.attributesData = {
                formPages: []
            };
        }
        this.attributesData.attributeKeys = params.attributeKeys;
        this.renderAttributes();

        // attribute actions
        $("#attributes-container").on("click", ".new-page-add", function (event) {
            event.preventDefault();
            var newPageName = $(this).closest("tr").find("input[name=new-page]").val();
            attributeFormsApp.attributesData.formPages.push({name: newPageName, attributes: []});
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("click", ".remove-page", function (event) {
            var index = $(this).closest('tr.form-page').data("index");
            attributeFormsApp.attributesData.formPages.splice(index, 1);
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("click", ".new-attribute-add", function (event) {
            event.preventDefault();
            var $newAttribute = $(this).closest("tr").find("select option:selected"),
                newAttributeName = $newAttribute.text(),
                newAttributeValue = $newAttribute.val(),
                pageIndex = $(this).closest('tr.form-page').data("index");

            attributeFormsApp.attributesData.formPages[pageIndex].attributes.push({
                akName: newAttributeName,
                akID: newAttributeValue,
                required: false,
            });
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("click", ".remove-attribute", function (event) {
            var pageIndex = $(this).closest('tr.form-page').data("index"),
                index = $(this).closest('tr.form-page-attribute').data("index");

            attributeFormsApp.attributesData.formPages[pageIndex].attributes.splice(index, 1);
            attributeFormsApp.renderAttributes();
        });
        
        $("#attributes-container").on("change", ".attribute-required", function (event) {
            var pageIndex = $(this).closest('tr.form-page').data("index"),
                index = $(this).closest('tr.form-page-attribute').data("index");
                
            attributeFormsApp.attributesData
                    .formPages[pageIndex]
                    .attributes[index].required = $(this).is(':checked');
            attributeFormsApp.updateFormData();
        });
    },
    initRedactor: function () {
        $('.ccm-advanced-editor').redactor({
            'plugins': ['concrete5']
        });
    },
    renderAttributes: function() {
        $("#attributes-container").html(this.templateAttributes(this.attributesData));

        // make attribute list sortable
        $(".form-pages").sortable({
            start: function(event, ui) {
                ui.item.startPos = ui.item.index();
            },
            stop: function(event, ui) {
                attributeFormsApp.attributesData.formPages.move(ui.item.startPos, ui.item.index());
                attributeFormsApp.renderAttributes();
            }
        });
        
        $(".form-page-attributes").sortable({
            start: function(event, ui) {
                ui.item.startPos = ui.item.index();
            },
            stop: function(event, ui) {
                var pageIndex = ui.item.closest('tr.form-page').data("index");
                attributeFormsApp.attributesData.formPages[pageIndex].attributes.move(ui.item.startPos, ui.item.index());
                attributeFormsApp.renderAttributes();
            }
        });
        this.updateFormData();
    },
    updateFormData: function(){
        // save JSON in form
        $("#attributes").val(JSON.stringify(this.attributesData));
    }
};
Array.prototype.move = function (from, to) {
    this.splice(to, 0, this.splice(from, 1)[0]);
};