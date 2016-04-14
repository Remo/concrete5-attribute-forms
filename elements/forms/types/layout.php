<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form role="role" class="form-horizontal form-groups-bordered" method="post"
      action="<?= $view->action('savelayout', isset($attributeForm) ? $attributeForm->getID() : false) ?>">

    <input type="hidden" name="layout_attributes" class="layout_attributes">
    <input type="hidden" name="attributes_html" class="attributes_html">

    <div class="row panel panel-default panel-shadow">
        <div class="panel-heading">
            <div class="panel-title">
                <?= t('Edit Layout') ?>
            </div>
        </div>
        <div  class="col-md-9 col-sm-9 col-xs-9" id="mycanvas"><?= isset($attributesHtml)? $attributesHtml : '' ?></div>

        <div class="col-md-3 col-sm-3 col-xs-3 ">
            <h2>Available Attributes</h2>
            <p>Drag the attribute into your form :</p>
            <div class="list-group" id="draggableAttr">
                <?php
                    foreach($attributeKeys as $atKey =>$atOption){
                        echo '<div data-value="'.$atOption->akID.'" data-athandle="'.$atOption->atHandle.'" class="list-group-item">'.$atOption->akName.'</div>';
                    }
                ?>
            </div>
        </div>

        <div id="mycanvas-hidden" style="display: none;"></div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->action('');?>" class="pull-left btn btn-default"> <?=t('Back');?> </a>
            <button class="pull-right btn btn-success" type="submit" > <?= t('Save') ?> </button>
        </div>
    </div>
</form>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script type="text/template" class="attributes-template">

        <% if(rc.attributesData.formPages[rc.dataRowId]){ %>
            <% _.each( rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId], function( page, i ){ %>
                <% _.each( page, function( attribute, j ){ %>
                    <div data-value="<%- attribute.akID %>" data-athandle="<%- attribute.atHandle %>" class="list-group-item ui-draggable ui-draggable-handle" data-page-index="<%- i %>" data-sort-order="" >
                        <%- attribute.akName %><br>
                        <span>
                            <% _.each( rc.attributeOptions[attribute.atHandle], function( opt, optKey ){
                                    var optText = opt["text"];

                                    if($.type(rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId][i][j].options) == 'undefined'){
                                        rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId][i][j].options = {};
                                        rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId][i][j].options[optKey] = false;
                                    }

                                %>
                                    <label class="control-label" style="font-weight:normal;">
                                        <input type="checkbox" data-name="<%- optKey %>" class="attribute-option" value="1" <%- rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId][i][j].options[optKey]?'checked="checked"':'' %> />
                                        <span><%- optText %></span>
                                    </label> <br>
                                <% }); %>

                            <input type="checkbox" class="attribute-required" value="1" <%- attribute.required?'checked="checked"':'' %>/> <?= t('Mandatory') ?>&nbsp;&nbsp;&nbsp;
                            <a title="Remove Attribute" class="pull-right gm-removeAttr"><span class="fa fa-trash-o"></span></a>
                        </span>
                    </div>
                <% }); %>
            <% }); %>
        <% } %>

</script>
<script type="text/javascript">


   $(document).ready(function () {

        /***Init Grid manager***/
       var gm = $("#mycanvas").gridmanager({
        rowClass:    "row-fluid",
        rowSelector: "div.row-fluid",
        debug:1,
       }).data('gridmanager');

        /**Load data if exist**/
       var dataRowId, dataColumnId;
       $('#mycanvas #gm-canvas .row-fluid').each(function(){

           if($.type($(this).data('row-id')) != "undefined"){
               dataRowId = $(this).data('row-id');

               $(this).find('.column').each(function(){
                   if($.type($(this).data('column-id')) != "undefined"){
                       dataColumnId = $(this).data('column-id');

                       gm.attributeFormsApp.initDynamicDataView({
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


       // To remove unwanted html element and comments
       $('#mycanvas').on({
           click :function(){
               $('#mycanvas-hidden').html($(this).find('#gm-canvas').html());
               $('#mycanvas-hidden').find('#gm-controls').remove().html();
               $('#mycanvas-hidden').find('.gm-tools.clearfix').remove().html();

               $('#mycanvas-hidden').find('.row-fluid').each(function(){
                  $(this).removeAttr('style');
                  $(this).find('.column').each(function(){
                      var labelText = $(this).find('p').html();
                      $(this).find('.gm-editable-region.gm-content-draggable').remove().html();
                      $(this).html('');
                      if($.type(labelText) != 'undefined'){
                          $(this).html('<p>'+labelText+'</p>');
                      }
                  });
               });

               //add value to hidden field
               $('.attributes_html').val($('#mycanvas-hidden').html());

           },
           keyup :function(){
               $('#mycanvas-hidden').html($(this).find('#gm-canvas').html());
               $('#mycanvas-hidden').find('#gm-controls').remove().html();
               $('#mycanvas-hidden').find('.gm-tools.clearfix').remove().html();

               $('#mycanvas-hidden').find('.row-fluid').each(function(){
                   $(this).removeAttr('style');
                   $(this).find('.column').each(function(){
                       var labelText = $(this).find('p').html();
                       $(this).find('.gm-editable-region.gm-content-draggable').remove().html();
                       $(this).html('');
                       if($.type(labelText) != 'undefined'){
                           $(this).html('<p>'+labelText+'</p>');
                       }
                   });
               });
               //add value to hidden field
               $('.attributes_html').val($('#mycanvas-hidden').html());

           }
       });


       /* Draggable Attributes */
       $("#draggableAttr div").draggable({
           connectToSortable: ".column",
           helper: "clone",
           revert: "invalid",
           stop:function(){
           }
       });




       /*******Remove Row and bind click*******/
       $('#mycanvas').find('a.gm-removeRow').bind('click',function(event){
           event.preventDefault();
           var rowId = $(this).closest('.row-fluid').data('row-id');
           $(this).closest('.row-fluid').find('.column').each(function(){
               $(this).find('a.gm-removeCol').trigger('click');
           });
           delete gm.attributeFormsApp.data.attributesData.formPages[rowId];
       });



       /*******Remove column and bind click*******/
       $('#mycanvas').find('a.gm-removeCol').bind('click',function(event){
           event.preventDefault();
           var rowId = $(this).closest('.row-fluid').data('row-id'),
               columnId = $(this).closest('.column').data('column-id');
           $(this).closest('.column').find('.list-group-item').each(function(){
               $(this).find('.gm-removeAttr').trigger('click');
           });
           delete gm.attributeFormsApp.data.attributesData.formPages[rowId][columnId];
       });

       /******Add sort order****/
       $('#mycanvas').find('.row-fluid').find('.column').each(function(i, el){
           $(this).find('.list-group-item').each(function(k, el){
               $(this).attr('data-sort-order',k);
           });
       });

       /***********Attribute mandatory / required change************/
       $('#mycanvas').on("change", ".attribute-required", function (event) {

           var rowId = $(this).closest('.row-fluid').data('row-id'),
               columnId = $(this).closest('.column').data('column-id'),
               sortOrderId = $(this).closest('.list-group-item').data('sort-order'),
               pageIndex = rowId+''+columnId;

           gm.attributeFormsApp.data
               .attributesData
               .formPages[rowId][columnId][pageIndex][sortOrderId]
               .required = $(this).is(':checked');
           gm.attributeFormsApp.updateFormData();

       });


       /*********** Attribute mandatory / required change ************/
       $('#mycanvas').on("change", ".attribute-option", function (event) {
           var dataRowId = $(this).closest('.row-fluid').data('row-id'),
               dataColumnId = $(this).closest('.column').data('column-id'),
               sortOrderId = $(this).closest('.list-group-item').data('sort-order'),
               pageIndex = dataRowId+''+dataColumnId;

           var attr = gm.attributeFormsApp.data.attributesData.formPages[dataRowId][dataColumnId][pageIndex][sortOrderId];
           var options = attr.options ? attr.options : {};


           var optionKey = $(this).data('name');
           var isUnique  = gm.attributeFormsApp.data.attributeOptions[attr.atHandle][optionKey].unique;
           if($(this).is(':checked') && isUnique){
               gm.attributeFormsApp.data.attributesData.formPages[dataRowId][dataColumnId].forEach(function(page, i) {
                   page.attributes.forEach(function(attribute, j){
                       if(attribute.options){
                           gm.attributeFormsApp.data
                               .attributesData
                               .formPages[dataRowId][dataColumnId][i]
                               .attributes[j]
                               .options[optionKey] = false;
                       }
                   });
               });
           }
           options[optionKey] = $(this).is(':checked');
           gm.attributeFormsApp.data.attributesData
               .formPages[dataRowId][dataColumnId][pageIndex][sortOrderId]
               .options = options;
           if($(this).is(':checked') && isUnique){
               gm.attributeFormsApp.renderClosestAttributes(this);
           }else{
               gm.attributeFormsApp.updateFormData();
           }
       });






       /******Trigger click to fill hidden field****/
       $('#mycanvas').trigger('click');
    });

</script>