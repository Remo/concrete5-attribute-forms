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

<!-- Optional theme -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">-->
<script type="text/template" class="attributes-template">

        <% if(rc.attributesData.formPages[rc.dataRowId]){ %>
            <% _.each( rc.attributesData.formPages[rc.dataRowId][rc.dataColumnId], function( page, i ){ %>
                <% _.each( page, function( attribute, j ){ %>
                    <div data-value="<%- attribute.akID %>" data-athandle="<%- attribute.atHandle %>" class="list-group-item ui-draggable ui-draggable-handle" data-page-index="<%- i %>" data-sort-order="" >
                        <%- attribute.akName %>
                        <a title="Remove Attribute" class="pull-right gm-removeAttr"><span class="fa fa-trash-o"></span></a>
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
       $('#mycanvas').on("click", function(){
           $('#mycanvas-hidden').html($(this).find('#gm-canvas').html());
           $('#mycanvas-hidden').find('#gm-controls').remove().html();
           $('#mycanvas-hidden').find('.gm-tools.clearfix').remove().html();
           $('#mycanvas-hidden').find('.gm-editable-region.gm-content-draggable').remove().html();
           $('#mycanvas-hidden').find('.row-fluid').removeAttr('style');
           $('#mycanvas-hidden').find('.column').html('');

           //add value to hidden field
           $('.attributes_html').val($('#mycanvas-hidden').html());

       });


       /* Draggable Attributes */
       $("#draggableAttr div").draggable({
           connectToSortable: ".column",
           helper: "clone",
           revert: "invalid",
           stop:function(){
           }
       });

       $('#mycanvas').trigger('click');
    });

</script>