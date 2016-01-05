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
        <% _.each( rc.attributesData.formPages, function( page, i ){ %>
        <tr class="form-page" data-index="<%- i %>">
            <td>
                <strong><%- page.name %></strong>
                <button class="btn btn-default remove-page pull-right"><?= t('Remove Form Page') ?></button>
                <div class="clearfix spacer-row-3"></div>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td class="header"><?= t('Attribute') ?></td>
                        <td class="header" width="80"><?= t('Mandatory') ?></td>
                        <td class="header" width="80"></td>
                    </tr>
                    </thead>
                    <tbody class="form-page-attributes">
                    <% _.each( page.attributes, function( attribute, j ){ %>
                        <tr class="form-page-attribute" data-index="<%- j %>">
                            <td>
                                <%- attribute.akName %><br>
                                <% _.each( rc.attributeOptions[attribute.atHandle], function( opt, optKey ){
                                    var optText = opt["text"];
                                    if(!attribute.options){
                                        attribute.options = {};
                                        attribute.options[optKey] = false;
                                    }
                                %>
                                    <label class="control-label" style="font-weight:normal;">
                                        <input type="checkbox" data-name="<%- optKey %>" class="attribute-option" value="1" <%- attribute.options[optKey]?'checked="checked"':'' %>/>
                                        <span><%- optText %></span>
                                    </label>
                                <% }); %>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="attribute-required" value="1" <%- attribute.required?'checked="checked"':'' %>/>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-default remove-attribute pull-right" ><?= t('Remove Attribute') ?></button>
                            </td>
                        </tr>
                    <% }); %>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">
                            <select name="new-attribute" class="form-control">
                                <% _.each( rc.attributeKeys, function( attributeKey, l ){ %>
                                <option value="<%- attributeKey.akID %>"><%- attributeKey.akName %></option>
                                <% }); %>
                            </select>
                            <div class="spacer-row-1"></div>
                            <button class="btn btn-primary new-attribute-add"><?= t('Add Page Attribute') ?></button>
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
                <div class="spacer-row-1"></div>
                <button class="btn btn-primary new-page-add"><?= t('Add Form Page') ?></button>
            </td>
        </tr>
        </tfoot>
    </table>
</script>
<script type="text/javascript">
    $(document).ready(function () {
        attributeFormsApp.initFormTypesView({
            attributeKeys: <?= json_encode($attributeKeys) ?>,
            selectedAttributes: <?= json_encode($selectedAttributes) ?>,
            attributeOptions: <?= json_encode($attributeOptions) ?>
        });
    });
</script>