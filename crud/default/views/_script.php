<?php echo "<?php
use yii\helpers\Url;

?>\n"
?>
<script>
    $(function () {
        var data = <?= "<?= \$value ?>" ?>;
        $.ajax({
            type: 'POST',
            url: '<?= "<?php echo Url::to(['add-'.\$relID]); ?>"; ?>',
            data: {'<?= "<?= \$class?>"?>' : data, action : 'load', isNewRecord : <?= "<?= \$isNewRecord ?>" ?>},
            success: function (data) {
                $('#add-<?= "<?= \$relID?>"; ?>').html(data);
            }
        });
    });
    function addRow<?= "<?= \$class ?>" ?>() {
        var data = $('#add-<?= "<?= \$relID?>"; ?> :input').serializeArray();
        data.push({name: 'action', value : 'add'});
        $.ajax({
            type: 'POST',
            url: '<?= "<?php echo Url::to(['add-'.\$relID]); ?>" ?>',
            data: data,
            success: function (data) {
                $('#add-<?= "<?= \$relID?>"; ?>').html(data);
            }
        });
    }
    function delRow<?= "<?= \$class ?>" ?>(id) {
        $('tr[data-key=' + id + ']').remove();
    }
</script>