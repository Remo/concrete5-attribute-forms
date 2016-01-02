<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form role="role" class="form-horizontal form-groups-bordered" method="post"
      action="<?= $view->action('save', isset($attributeForm) ? $attributeForm->getID() : false) ?>">

    <input type="hidden" name="attributes" id="attributes">

    <div class="panel panel-default panel-shadow">
        <div class="panel-heading">
            <div class="panel-title">
                <?= isset($attributeForm) ? t('Edit Form') : t('Add Form') ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?= t('Form Name') ?>
                        </label>

                        <div class="col-sm-10">
                            <?= $form->text('formName',
                                isset($attributeForm) ? $attributeForm->getFormName() : '',
                                array('class' => 'form-control')) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?= t('Delete SPAM') ?>
                        </label>

                        <div class="col-sm-10">
                            <?= $form->checkbox('deleteSpam',
                                1,
                                isset($attributeForm) ? $attributeForm->getDeleteSpam() : false) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?= t('Attributes') ?>
                        </label>

                        <div class="col-sm-10" id="form-attributes">
                            <div id="attributes-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-blue"><?= t('Save') ?></button>
        </div>
    </div>
</form>

<script type="text/template" class="attributes-template">
    <table class="table table-striped table-bordered" border="0" cellspacing="1" cellpadding="0">
        <thead>
        <tr>
            <td class="header"><?= t('Page') ?></td>
        </tr>
        </thead>
        <tbody class="form-pages">
        <% _.each( rc.formPages, function( page, i ){ %>
        <tr>
            <td>
                <strong><%- page.name %></strong>
                <button class="btn btn-default remove-page pull-right" data-index="<%- i %>"><?= t('Remove Form Page') ?></button>
                <div class="clearfix spacer-row-3"></div>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td class="header"><?= t('Attribute') ?></td>
                    </tr>
                    </thead>
                    <tbody class="form-page-attributes">
                    <% _.each( page.attributes, function( attribute, j ){ %>
                        <tr>
                            <td>
                                <%- attribute.akName %>
                                <button class="btn btn-default remove-attribute pull-right" data-page-index="<%- i %>" data-index="<%- j %>"><?= t('Remove Attribute') ?></button>
                            </td>
                        </tr>
                    <% }); %>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>
                            <select name="new-attribute" class="form-control">
                                <% _.each( rc.attributeKeys, function( attributeKey, l ){
                                            console.log(attributeKey);
                                            %>
                                <option value="<%- attributeKey.akID %>"><%- attributeKey.akName %></option>
                                <% }); %>
                            </select>
                            <div class="spacer-row-1"></div>
                            <button class="btn btn-primary new-attribute-add" data-page-index="<%- i %>"><?= t('Add Page Attribute') ?></button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <% }); %>
        </tbody>
        <tfoot>
        <tr>
            <td>
                <input type="text" name="new-page" class="form-control">
                <button class="btn btn-primary new-page-add"><?= t('Add Form Page') ?></button>
            </td>
        </tr>
        </tfoot>
    </table>
</script>

<script type="text/javascript">
    $(document).ready(function () {

        $('.ccm-advanced-editor').redactor({
            'plugins': ['concrete5']
        });

        // setup underscore template
        _.templateSettings.variable = "rc";
        var templateAttributes = _.template(
            $("script.attributes-template").html()
        );
        var attributeKeys = <?=json_encode($attributeKeys)?>;
        var attributesData = <?=json_encode($selectedAttributes)?>;
        if(!attributesData){
            attributesData = {};
            attributesData.formPages = [];
        }
        attributesData.attributeKeys = attributeKeys;

        function renderAttributes() {
            $("#attributes-container").html(templateAttributes(attributesData));

            // make attribute list sortable
            // @TODO we have to update our JSON variable!
            $(".form-pages, .form-page-attributes").sortable();

            // save JSON in form
            $("#attributes").val(JSON.stringify(attributesData));
        }
        renderAttributes();


        // attribute actions
        $("#attributes-container").on("click", ".new-page-add", function (event) {
            event.preventDefault();
            var newPageName = $(this).closest("tr").find("input[name=new-page]").val();
            attributesData.formPages.push({name: newPageName, attributes: []});
            renderAttributes();
        });
        $("#attributes-container").on("click", ".remove-page", function (event) {
            var index = $(this).data("index");
            attributesData.formPages.splice(index, 1);
            renderAttributes();
        });
        $("#attributes-container").on("click", ".new-attribute-add", function (event) {
            event.preventDefault();
            var $newAttribute = $(this).closest("tr").find("select option:selected"),
                newAttributeName = $newAttribute.text(),
                newAttributeValue = $newAttribute.val(),
                pageIndex = $(this).data("page-index");

            attributesData.formPages[pageIndex].attributes.push({
                akName: newAttributeName,
                akID: newAttributeValue
            });
            renderAttributes();
        });
        $("#attributes-container").on("click", ".remove-attribute", function (event) {
            var pageIndex = $(this).data("page-index"),
                index = $(this).data("index");

            attributesData.formPages[pageIndex].attributes.splice(index, 1);
            renderAttributes();
        });

    });
</script>