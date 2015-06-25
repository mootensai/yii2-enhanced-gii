<?php echo "<?php

use yii\helpers\Json;
use yii\helpers\Url;

?>\n"
?>
<script>
    $(function () {
        var data = <?= "<?php echo Json::encode([ \$pk => Yii::\$app->request->get(\$pk)]) ?>;\n" ?>
        $.ajax({
            type: 'GET',
            url: <?= "<?php echo Url::to(['add-'.\$relID]); ?>"; ?>,
            data: data,
            success: function (data) {
                $('#add-<?= "<?= \$relID?>"; ?>').html(data);
            }
        });
    });
    function addRow() {
        var data = $('#<?= "<?= \$class?>"; ?>').serializeArray();
        $.ajax({
            type: 'POST',
            url: <?= "<?php echo Url::to(['add-'.\$relID]); ?>" ?>,
            data: data,
            success: function (data) {
                $('#add-<?= "<?= \$relID?>"; ?>').html(data);
            }
        });
    }
    function delRow(id) {
        $('tr[data-key=' + id + ']').remove();
    }
</script>