<fieldset>
    <legend><?php echo t('Email Options')?></legend>

    <div class="form-group">

        <?php echo $form->label( 'akEmailPlaceholder', t('Placeholder Text') )?>

        <?php echo $form->text( 'akEmailPlaceholder' , $akEmailPlaceholder )?>
    </div>

</fieldset>