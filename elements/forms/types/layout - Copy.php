<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form role="role" class="form-horizontal form-groups-bordered" method="post"
      action="<?= $view->action('save', isset($attributeForm) ? $attributeForm->getID() : false) ?>">

    <input type="hidden" name="attributes" id="attributes">






    <div>
        <div class="holder">
            <div class="item" data="navbar">
                navbar
            </div>
            <div class="item" data="row">
                row
            </div>
            <div class="item" data="span2">
                span2
            </div>
            <div class="item" data="span3">
                span3
            </div>
            <div class="item" data="span4">
                span4
            </div>
            <div class="item" data="btn">
                <button class="btn">Button</button>
            </div>
            <div class="item" data="btn-group">
                <div class="btn-group">
                    <button class="btn">Left</button>
                    <button class="btn">Middle</button>
                    <button class="btn">Right</button>
                </div>
            </div>
            <div class="item"><img src="//placehold.it/100x100"></div>
        </div>
        <div class="special">
            <div class="container"> </div>
        </div>
    </div>







</form>

<script type="text/template" class="attributes-template">
    <table class="table table-striped table-bordered" border="0" cellspacing="1" cellpadding="0">
        <tbody class="form-pages">
        <% _.each( rc.attributesData.formPages, function( page, i ){ %>
        <tr class="form-page" data-index="<%- i %>">
            <td>



                    <% _.each( page.attributes, function( attribute, j ){ %>
                        <div class="form-page-attribute gm-content-draggable" data-index="<%- j %>">

                                <%- attribute.akName %><br>
                                <% _.each( rc.attributeOptions[attribute.atHandle], function( opt, optKey ){
                                    var optText = opt["text"];
                                    if(!attribute.options){
                                        attribute.options = {};
                                        attribute.options[optKey] = false;
                                    }
                                %>

                                <% }); %>


                        </div>
                    <% }); %>

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
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" type="text/css">
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>-->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script type='text/javascript' src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        //$("#mycanvas").gridmanager();

        attributeFormsApp.initFormTypesView({
            attributeKeys: <?= json_encode($attributeKeys) ?>,
            selectedAttributes: <?= json_encode($selectedAttributes) ?>,
            attributeOptions: <?= json_encode($attributeOptions) ?>
        });
    });



    var objs = {
        "row":{"ht":"<div class=\"row-fluid\"></div>","accept":"[data*=span]"},
        "span2":{"ht":"<div class=\"span2\"></div>","accept":"[data*=btn]"},
        "span3":{"ht":"<div class=\"span3\"></div>","accept":"[data*=span],[data*=btn]"},
        "span4":{"ht":"<div class=\"span4\"></div>","accept":"[data*=span],[data*=btn]"},
        "navbar":{"ht":"<div class=\"navbar\"><div class=\"navbar-inner\"><a class=\"brand\" href=\"#\">Brand</a><ul class=\"nav\"><li class=\"active\"><a href=\"#\">Home</a></li><li><a href=\"#\">Link</a></li><li><a href=\"#\">Link</a></li><li class=\"divider-vertical\"></li><li><a href=\"#\">More</a></li><li><a href=\"#\">Options</a></li></ul></div></div>","dropClass":".navbar-inner","accept":"[data*=btn]"},
        "btn":{"ht":"<button class=\"btn\">Button</button>","accept":".none"},
        "btn-group":{"ht":"<div class=\"btn-group\"><button class=\"btn\">Left</button><button class=\"btn\">Middle</button><button class=\"btn\">Right</button></div>","accept":".none"}
    };

    $('.item').draggable({
        helper: "clone",
        snap: true
    });

    var dropHandler = function(accept) {
        return {
            accept:accept,
            snap: true,
            greedy: true,
            hoverClass: "target",
            drop: function (event, ui) {
                var item = $(ui.helper);
                var d = item.attr("data");
                var ht = $(objs[d].ht);
                if (typeof d!="undefined" && objs[d].ht){
                    if (typeof objs[d].dropClass!="undefined"){
                        ht.appendTo(this).find(objs[d].dropClass).droppable(dropHandler(objs[d].accept)).draggable({snap:true,revert:"invalid"});
                    }
                    else {
                        ht.appendTo(this).droppable(dropHandler(objs[d].accept)).draggable({snap:true,revert:"invalid"});
                    }
                    ht.addClass("dropped");
                    ht.click(function(){

                        if ($(this).hasClass("droppedSelected")) {
                            //off
                            $(this).removeClass("droppedSelected");
                            $(this).find(".hanger").remove();
                            $('.icon-trash').unbind('click');
                        }
                        else {
                            // on
                            $('<div class="hanger"><i class="icon-trash" data=""></i></div>').appendTo(this);
                            $(this).addClass("droppedSelected");
                            $('.icon-trash').click(function(){
                                $(this).parents(".dropped").remove();
                            });

                        }
                    });
                }
            }
        }};

    $('.container').droppable({
        snap: true,
        hoverClass: "target",
        accept: "[data=row],[data*=span],[data=navbar]",
        drop: function (event, ui) {
            var item = $(ui.helper);
            //item.clone().appendTo(this).draggable();
            var d = item.attr("data");
            var ht = $(objs[d].ht);

            if (typeof objs[d].dropClass!="undefined"){
                ht.appendTo(this).find(objs[d].dropClass).droppable(dropHandler(objs[d].accept)).draggable({snap:true,revert:"invalid"});
            }
            else {
                ht.appendTo(this).droppable(dropHandler(objs[d].accept)).draggable({snap:true,revert:"invalid"});
            }
            ht.addClass("dropped");
            ht.click(function(){

                if ($(this).hasClass("droppedSelected")) {
                    //off
                    $(this).removeClass("droppedSelected");
                    $(this).find(".hanger").remove();
                    $('.icon-trash').unbind('click');
                }
                else {
                    // on
                    $('<div class="hanger"><i class="icon-trash" data=""></i></div>').appendTo(this);
                    $(this).addClass("droppedSelected");
                    $('.icon-trash').click(function(){
                        $(this).parents(".dropped").remove();
                    });

                }
            });
        }
    });



</script>
<style>
    .holder {
        float:left;
        width:300px;
        zoom:0.8;
    }
    .item {
        padding:3px;
        border:1px dashed #999;
        overflow:auto;

    }

    .droppedSelected {
        border:1px solid #22CC22;
    }

    .hanger {
        background-color:#22CC22;
        border:1px solid #22CC22;
        position:relative;
        padding:2px;
        z-index:7000;
        margin-left:2%;
        float:left;
    }

    .special {
        background-color:#efefef;
        height:400px;
        float:left;
        width:400px;
        margin-left:10px;
    }


    .special div {
        *background-color:#f9f9f9;
    }

    .special [class*="span"] {
        background-color:#999;
        opacity:.6;
        min-height:30px;
    }

    .special .row, .special .row-fluid {
        border:1px dashed #444;
        min-height:30px;
    }

    .special .container {
        height:100%;
        border:1px dotted #ccc;
    }

    .target {
        background-color:#33ff44 !important;
        opacity:.4;
    }
</style>