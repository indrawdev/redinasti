<?php foreach ($data as $dt) { ?>
    <tr id="row<?= $dt['idx'] ?>">
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['item_category'] ?></td>
        <td><?= $dt['item_name'] ?></td>
        <?php if (is_superadmin()) : ?>
            <td><?= $dt['division'] ?></td>
        <?php endif; ?>
            <td><?=myprice($dt['item_hpp_price'])?></td>
        <td class="center">
            <a href="<?= current_controller() ?>edit/<?= $dt['idx'] ?>" title="Edit Record" class="btn btn-info"><i class="icon-edit"></i></a>
            <a title="Delete Record" class="btn btn-danger tangan hapus" data-url-rm="delete" data-id="<?=$dt['idx']?>"><i class="icon-remove"></i> </a>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="<?=(is_superadmin()) ? '6' : '5'?>"><?= $paging ?></td>
</tr>