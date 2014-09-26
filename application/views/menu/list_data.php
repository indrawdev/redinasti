<?php foreach ($data as $dt) { ?>
    <tr id='row<?= $dt['idx'] ?>'>
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['menu'] ?></td>
        <td><?=($dt['parent_auth_menu'] == 0) ? 'ROOT' : $dt['parent_menu']?></td>
        <td><?= $dt['file'] ?></td>
        <td><?= $dt['position'] ?></td>
        <td class="center">
            <a href="<?= current_controller() ?>edit/<?= $dt['idx'] ?>" title="Edit Record" class="icon-pencil"></a>
            <a title="Delete Record" class="icon-trash tangan hapus" data-url-rm="delete" data-id="<?=$dt['idx']?>"></a>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="6"><?= $paging ?></td>
</tr>