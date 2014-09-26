<?php foreach ($data as $dt) { ?>
    <tr id='row<?= $dt['idx'] ?>'>
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['giro_code'] ?></td>
        <td><?= $dt['giro_bank'] ?></td>
        <td><?= iso_date($dt['giro_date']) ?></td>
        <td>Rp. <?= myprice($dt['giro_price']) ?></td>
        <td><?= ($dt['giro_status'] == 1) ? 'Terpakai' : ($dt['giro_status'] == 2) ? 'Di-uangkan' : 'Belum Terpakai' ?></td>
        <!--
        <td>#<?=$dt['giro_invoice']?></td>
        -->
        <td class="center">
            <?php if ($dt['giro_status'] != 1) : ?>
                <a href="<?= current_controller() ?>edit/<?= $dt['idx'] ?>" title="Edit Record" class="icon-pencil"></a>
                <a title="Delete Record" class="icon-trash tangan hapus" data-url-rm="delete" data-id="<?=$dt['idx']?>"></a>
            <?php endif; ?>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="7"><?= $paging ?></td>
</tr>