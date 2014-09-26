<?php foreach ($data as $dt) { ?>
    <tr id='row<?= $dt['idx'] ?>'>
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['site_name'] ?></td>
        <td><?= $dt['site_url'] ?></td>
        <td><?= ($dt['is_default']==1) ? 'default' : '' ?></td>
        <td class="center">
            <a href="<?= current_controller() ?>edit/<?= $dt['idx'] ?>" title="Edit Record" class="icon-pencil"></a>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="5"><?= $paging ?></td>
</tr>