<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<script type="text/template" id="formActionTemplate">
    <div class="ccm-atform-action-entry entry-well entry-closed">
        <button type="button" class="btn btn-sm btn-default ccm-edit-atform-action" data-entry-close-text="<?= t('Collapse'); ?>" data-entry-edit-text="<?= t('Edit'); ?>"><?= t('Edit'); ?></button>
        <button type="button" class="btn btn-sm btn-danger ccm-delete-atform-action-entry"><?= t('Remove'); ?></button>
        <i class="fa fa-arrows"></i>
        <div class="spacer-row-3"></div>

        <input class="ccm-atform-action-id" type="hidden" name="<?= $view->field('actionID'); ?>[]" value="<%=action.ID%>"/>
        <div class="col-xs-12">
            <div class="form-group first">
                <label><?= t('Action Name'); ?></label>
                <input type="text" name="<?= $view->field('actionName'); ?>[]" value="<%=action.actionName%>" class="form-control"/>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group" >
                <label><?= t('Action Type'); ?></label>
                <select id="actionType<%=action.ID%>" name="<?=$view->field('actionType');?>[]" class="actionTypeSelect form-control" style="width: 200px;">
                    <% _.each(actionTypes, function( type, key ){ %>
                    <option value="<%=key%>" <% if (type == action.actionType) { %>selected<% } %>><%= type%></option>
                    <% }); %>
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <div class="form-group" >
                <hr style="border-top-color:#8F8D8D;">
            </div>
        </div>
        <div class="clearfix"></div>
        <div id="actionTypeForm<%=action.ID%>" class="col-xs-12 actionTypeForm" >
            
        </div>
        <div class="clearfix"></div>
        <input class="ccm-atform-action-entry-sort" type="hidden" name="<?= $view->field('customActions'); ?>[]" value=""/>
    </div>
</script>