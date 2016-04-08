<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form role="role" class="form-horizontal form-groups-bordered" method="post"
      action="<?= $view->action('savelayout', isset($attributeForm) ? $attributeForm->getID() : false) ?>">

    <input type="hidden" name="layout_attributes" class="layout_attributes">
    <input type="hidden" name="attributes_html" class="attributes_html">

    <div class="panel panel-default panel-shadow">
        <div class="panel-heading">
            <div class="panel-title">
                <?= t('Edit Layout') ?>
            </div>
        </div>
        <div id="mycanvas"><?= isset($attributesHtml)? $attributesHtml : '' ?></div>
        <div id="mycanvas-hidden" style="display: none;"></div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->action('');?>" class="pull-left btn btn-default"> <?=t('Back');?> </a>
            <button class="pull-right btn btn-success" type="submit" > <?= t('Save') ?> </button>
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
        <% if(rc.attributesData.formPages[rc.dataRowId]){ %>
            <% _.each( rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId], function( page, i ){ %>

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
                                        <option value="<%- attributeKey.akID %>" data-athandle="<%- attributeKey.atHandle %>" ><%- attributeKey.akName %></option>
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
        <% } %>
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


       //setTimeout(function(){
           var gm = $("#mycanvas").gridmanager({
               //controlPrepend: "<div class='row-fluid'><div class='col-md-12'><div id='gm-addnew' class='btn-group '>",
               // Row Specific
               rowClass:    "row-fluid",
               rowSelector: "div.row-fluid",
               //rowPrepend:  "<div class='row-fluid gm-editing'>",
               debug:1,
               customControls: {
                   //global_row: [{ callback: 'test_callback', loc: 'top', btnLabel: 'row btn' }],
                   global_col: [{ callback: 'custom_attribute_callback', loc: 'top'}]
               }
           }).data('gridmanager');



           custom_attribute_callback = function(container, btn) {
               var cTagOpen = '<!--'+gm.options.gmEditRegion+'-->',
                   cTagClose = '<!--\/'+gm.options.gmEditRegion+'-->',
                   elem = null;

               $(('.'+gm.options.gmToolClass+':last'),container)
                   .before(elem = $('<div>').addClass(gm.options.gmEditRegion+' '+gm.options.contentDraggableClass)
                       .append(gm.options.controlContentElem+'<div class="'+gm.options.gmContentRegion+'"><div class="attributes-container"></div></div>')).before(cTagClose).prev().before(cTagOpen);
               gm.initNewContentElem(elem);
               gm.attributeFormsApp.initFormTypesView({
                   attributeKeys: <?= json_encode($attributeKeys) ?>,
                   selectedAttributes: '',
                   attributeOptions: <?= json_encode($attributeOptions) ?>,
                   buttn:$(btn).parent().parent().parent(),
                   dataRowId:$(btn).closest('.row-fluid').data("row-id"),
                   dataColumnId:$(btn).closest('.column').data("column-id")
               });
           };


           var dataRowId, dataColumnId;
           $('#mycanvas #gm-canvas .row-fluid').each(function(){

               if($.type($(this).data('row-id')) != "undefined"){
                   dataRowId = $(this).data('row-id');

                   $(this).find('.column').each(function(){
                       if($.type($(this).data('column-id')) != "undefined"){
                           dataColumnId = $(this).data('column-id');

                           gm.attributeFormsApp.initFormTypesView({
                               attributeKeys: <?= json_encode($attributeKeys) ?>,
                               selectedAttributes: <?= json_encode($selectedAttributes) ?>,
                               attributeOptions: <?= json_encode($attributeOptions) ?>,
                               buttn:$(this).find('.gm-custom_attribute_callback').parent().parent(),
                               dataRowId:dataRowId,
                               dataColumnId:dataColumnId
                           });
                       }
                   });
               }
           });


       //},100);



       // To remove unwanted html element and comments
       $('#mycanvas').on("click", function(){
           $('#mycanvas-hidden').html($(this).find('#gm-canvas').html());
           $('#mycanvas-hidden').find('#gm-controls').remove().html();
           $('#mycanvas-hidden').find('.gm-tools.clearfix').remove().html();
           $('#mycanvas-hidden').find('.gm-editable-region.gm-content-draggable').remove().html();
           $('#mycanvas-hidden').find('.gm-editable-region.gm-content-draggable').remove().html();
           $('#mycanvas-hidden').find('.column').html('');

           //add value to hidden field
           $('.attributes_html').val($('#mycanvas-hidden').html());
           //alert($('.attributes_html').val());
       });
    });




   /*
       attributeFormsApp.initFormTypesView({
           attributeKeys: <?= json_encode($attributeKeys) ?>,
           selectedAttributes: <?= json_encode($selectedAttributes) ?>,
           attributeOptions: <?= json_encode($attributeOptions) ?>
       });
   */

</script>