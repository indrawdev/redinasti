<?php foreach ($data as $dt) { ?>
    <tr id="row<?= $dt['idx'] ?>">
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['production_code'] ?></td>
        <td><?= $dt['production_name'] ?></td>
        <?php if (is_superadmin()) : ?>
            <td><?= $dt['division'] ?></td>
        <?php endif; ?>
            <td><?=myprice($dt['production_hpp_price'])?></td>
        <td><?=iso_date($dt['create_date'])?></td>
        <td><?= ($dt['production_type'] == 2) ? '1/2 Jadi' : (($dt['production_type'] == 3) ? 'Mentah' : 'Jadi')?></td>
        <td class="center">
            <a href="<?= current_controller() ?>detail/<?= $dt['idx'] ?>" title="Detail Record" class="btn btn-info"><i class="icon-edit"></i></a>
            <a title="Delete Record" class="btn btn-danger tangan hapus" data-url-rm="delete" data-id="<?=$dt['idx']?>"><i class="icon-remove"></i> </a>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="<?=(is_superadmin()) ? '8' : '7'?>"><?= $paging ?></td>
</tr>