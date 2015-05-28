<?php
$akID = $this->getAttributeKey()->getAttributeKeyID();
?>
<div id="attribute-switcher-<?= $akID ?>">
    <label>
        <?php
        $cb = Loader::helper('form')->checkbox($this->field('value'), 1, $checked);
        print $cb . ' <span>' . t('Yes') . '</span>';
        ?>
    </label>

    <script type="text/javascript">
        $(document).ready(function () {
            updateAttributeVisibility<?=$akID?>();
            $("#attribute-switcher-<?= $akID ?>").on("change", "input", function () {
                updateAttributeVisibility<?=$akID?>();
            });

        });
        function updateAttributeVisibility<?=$akID?>() {
            var checkedActions = <?=json_encode($akCheckedActions)?>;
            var uncheckedActions = <?=json_encode($akUncheckedActions)?>;

            if ($("#attribute-switcher-<?= $akID ?> input").is(":checked")) {
                for (var i in checkedActions) {
                    if (checkedActions[i] == "hide") {
                        $("#attribute-key-id-" + i).hide();
                    }
                    if (checkedActions[i] == "show") {
                        $("#attribute-key-id-" + i).show();
                        $("#attribute-key-id-" + i).css("margin-left", "50px");
                    }
                }
            }
            else {
                for (var i in uncheckedActions) {
                    if (uncheckedActions[i] == "hide") {
                        $("#attribute-key-id-" + i).hide();
                    }
                    if (uncheckedActions[i] == "show") {
                        $("#attribute-key-id-" + i).show();
                        $("#attribute-key-id-" + i).css("margin-left", "50px");
                    }
                }
            }
        }
    </script>
</div>