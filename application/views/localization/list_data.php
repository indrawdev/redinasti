<?php foreach ($data as $dt) { ?>
    <tr id='row<?= $dt['idx'] ?>'>
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= ucwords($dt['locale']) ?></td>
        <td><?= $dt['iso_1'] ?></td>
        <td><?= $dt['iso_2'] ?></td>
        <td><?= $dt['locale_path'] ?></td>
        <td><?= ($dt['locale_status'] == 1) ? 'Default' : '' ?></td>
        <td class="center">
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="7"><?= $paging ?></td>
</tr>