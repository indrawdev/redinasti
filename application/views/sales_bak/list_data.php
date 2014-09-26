<?php foreach ($data as $dt) { ?>
    <tr id="row<?= $dt['idx'] ?>" <?=($dt['payment_status'] == 2) ? 'class="success"' : (($dt['payment_status'] == 1) ? 'class="info"' : '')?>>
        <td class="center"><?= ( ++$_GET['page']) ?></td>
        <td><?= $dt['sales_invoice'] ?></td>
        <td><?= $dt['store'] ?></td>
        <td><?= $dt['store_address'] ?></td>
        <td><?= myprice($dt['total_price']) ?></td>
        <td><?= ($dt['shipping_date'] != '') ? iso_date($dt['shipping_date']) : ''?></td>
        <td><?= ($dt['payment_status'] == 2) ? 'Lunas' : (($dt['payment_status'] == 1) ? 'Sudah bayar, belum lunas' : 'Proses')?></td>
        <td class="center">
            <a href="<?= current_controller() ?>detail/<?= $dt['idx'] ?>" title="Detail Record" class="btn btn-info"><i class="icon-edit"></i></a>
            <a title="Delete Record" class="btn btn-danger tangan hapus" data-url-rm="delete" data-id="<?=$dt['idx']?>"><i class="icon-remove"></i> </a>
            <a href="<?= current_controller() ?>payment/<?= $dt['idx'] ?>" title="Pembayaran" class="btn btn-primary"><i class="icon-briefcase"></i></a>
        </td>
    </tr>
<?php } ?>
<tr class="footer">
    <td colspan="8"><?= $paging ?></td>
</tr>