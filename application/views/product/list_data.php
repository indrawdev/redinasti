<?php foreach ($data as $dt) { ?>
    <tr id='row<?= $dt['idx'] ?>'>
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['product_name'] ?></td>
        <td><?= $dt['product_code'] ?></td>
        <td><?= $dt['category'] ?></td>
        <td><?= $dt['product_stock'] ?></td>
        <td><?= myprice($dt['buy_price']) ?></td>
        <td><?= myprice($dt['sell_price']) ?></td>
        <td class="center">
            <a href="<?= current_controller() ?>edit/<?= $dt['idx'] ?>" title="Edit Record" class="icon-pencil"></a>
            <a title="Delete Record" class="icon-trash tangan hapus" data-url-rm="delete" data-id="<?=$dt['idx']?>"></a>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="8"><?= $paging ?></td>
</tr>