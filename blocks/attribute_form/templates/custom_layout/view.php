<?php
use Concrete\Package\AttributeForms\Attribute\Key\AttributeFormKey;

if (empty($aftID)) {
    echo t('No form type selected');
    return;
}
//var_dump($layoutAttributes);
if (!empty($layoutAttributes)) {
    $page = Page::getCurrentPage();
    $page->getCollectionID();
    ?>
    <br>
    <div class="row">
        <div class="col-xs-12">
            <?php if(isset($errors) && $errors->has()): ?>
                <div class="alert alert-danger">
                    <?php $errors->output(); ?>
                </div>
            <?php endif; ?>
            <?php
            if(!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <?php if (is_array($success_msg)): ?>
                        <ul>
                            <?php foreach ($success_msg as $msg): ?>
                                <li><?= $msg; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= $success_msg; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <form method="post" action="<?= $this->action('layoutSubmit') ?>" class="attribute-canvas">
        <input type="hidden" name="aftID" value="<?= $aftID ?>">
        <input type="hidden" name="_token" id="_token" value="<?= $token; ?>"/>

        <?= $attributesHtml; ?>


        <?php  if ($captcha): ?>
            <div class="row">
                <div class="col-sm-offset-4 col-sm-8">
                    <div class="form-group captcha">
                        <?php
                        $captchaLabel = $captcha->label();
                        if (!empty($captchaLabel)):?>
                            <label class="control-label"><?= $captchaLabel; ?></label>
                            <?php
                        endif; ?>
                        <div><?php  $controller->diaplayCaptcha($captcha, $aftID); ?></div>
                        <div><?php  $captcha->showInput(); ?></div>
                    </div>
                </div>
            </div>
        <?php  endif; ?>
        <div class="row">
            <div class="col-sm-8">
                <span class="small text-muted">* <?=t('Required fields.');?></span>
                <input type="submit" name="Submit" class="btn btn-primary pull-right" value="<?= h(t($submitText)); ?>" />
            </div>
        </div>
        <div class="se-pre-con">
            <img src="<?php echo $this->getBlockURL() ?>/img/preloader_3.gif">
            <h3><?=  t('Form loading...');?></h3>
        </div>
    </form>
    <style>
        .attribute-row{
            border:none ;
        }
        .form-control{
            width: 96%;
            float: left;
        }
        .text-danger{
            float: left;
        }

        .no-js #loader {
            display: none;
        }
        .js #loader {
            display: block;
            position: absolute;
            left: 100px;
            top: 0;
        }
        .se-pre-con {
            position: fixed;
            right: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            text-align: center;
            background:  center no-repeat #fff;
        }

        .se-pre-con img{
            margin-top: 25%;
            clear: both;
        }
    </style>

    <script>
        function generateHTML(dataRowId, dataColumnId, totalCount, attribute){

            var dataString = {'attributeKeyId' : attribute.akID}
            var countcol = $('form.attribute-canvas .row-fluid').find('.column').length;

            $.ajax({
                type: "POST",
                url: "<?=$view->action('renderAttributes')?>",
                data: dataString,
                success: function(response)
                {

                    var columnAttributeHTML = '';
                    columnAttributeHTML = columnAttributeHTML + '<div class="form-group attribute-row" id="attribute-key-id-'+attribute.akID+'">';

                    columnAttributeHTML = columnAttributeHTML + '</label>'+
                        '<div class="">';

                    if(attribute.required == true){
                        columnAttributeHTML = columnAttributeHTML + '<span class="text-danger">* &nbsp;</span>';
                    }
                    columnAttributeHTML = columnAttributeHTML + response;
                    columnAttributeHTML = columnAttributeHTML + '</div></div>';
                    if($('form.attribute-canvas').find('div[data-row-id='+dataRowId+']').find('div[data-column-id='+dataColumnId+']').find('p').text() == '') {
                        $('form.attribute-canvas').find('div[data-row-id='+dataRowId+']').find('div[data-column-id='+dataColumnId+']').html(columnAttributeHTML);
                    }else{
                        $('form.attribute-canvas').find('div[data-row-id='+dataRowId+']').find('div[data-column-id='+dataColumnId+']').find('p').after(columnAttributeHTML);
                    }

                    if(countcol == totalCount){
                        $(".se-pre-con").fadeOut("slow");
                    }
                }
            });
        }

        $(document).ready(function(){
            $('.se-pre-con').css('width', $('.main-content').width()+20);
            /**Load data if exist**/
            var dataRowId, dataColumnId,
                formPages = <?= isset($layoutAttributes->formPages)?  json_encode($layoutAttributes->formPages) :  '' ; ?>;
            var totalCount = 1;
            $('form.attribute-canvas .row-fluid').each(function(){

                if($.type($(this).data('row-id')) != "undefined"){
                    dataRowId = $(this).data('row-id');


                    $(this).find('.column').each(function(){
                        if($.type($(this).data('column-id')) != "undefined"){
                            dataColumnId = $(this).data('column-id');

                            if(formPages != ''){

                                $.each(formPages[dataRowId][dataColumnId], function( i, page ){
                                    $.each( page, function( j, attribute ) {
                                        totalCount = totalCount + 1;

                                        generateHTML(dataRowId, dataColumnId, totalCount, attribute);
                                    });
                                });
                            }
                        }
                    });
                }

            });
        });
    </script>
    <?php
}