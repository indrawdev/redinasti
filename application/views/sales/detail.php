<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <?php 
        if (isset($message)) { 
            echo $message;
        }
    ?>
    <div class="row-fluid suppptrans-detail">
        <div class="span8">
            <fieldset>
                <legend></legend>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="sales_invoice">No Faktur</label>: <strong><?=$record['sales_invoice']?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="store">Toko</label>: <strong><?=$record['store']?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="driver">Supir</label>: <?=$record['driver']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="licence_plate">No. Plat</label>: <?=$record['licence_plate']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="shipping_date">Tanggal Kirim</label>: <?=iso_date($record['shipping_date'],'-')?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="licence_plate">Total Harga</label>: <strong>Rp. <?=myprice($record['total_price'])?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="purchase_note">Catatan</label>: <?=$record['sales_note']?>
                    </div>
                </div>
            </fieldset>
            <fieldset class="listProduction">
                <legend>Barang Produksi</legend>
                <div class="genProduction">
                    <table class="table table-striped table-bordered table-hover" id="tableProduction">
                        <thead>
                            <tr>
                                <th class="center" style='width:1px;'>No</th>
                                <th class="center" id="production_code">Kode Produksi <span></span></th>
                                <th class="center" id="item_name">Nama Barang <span></span></th>
                                <th class="center" id="division">Divisi <span></span></th>
                                <th class="center" id="qty">QTY <span></span></th>
                                <th class="center" id="price">Harga <span></span></th>
                                <th class="center" id="discount">Diskon (%)<span></span></th>
                                <th class="center" id="total">Total <span></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($record['productions']) && count($record['productions'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['productions'] as $prod) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="production_code"><?=$prod['production_code']?></td>
                                    <td class="item_name"><?=$prod['item_name']?></td>
                                    <td class="division"><?=$prod['division']?></td>
                                    <td class="qty text-right">1</td>
                                    <td class="price text-right">Rp. <?=myprice($prod['sales_price'])?></td>
                                    <td class="discount"><?=$prod['discount_percentage']?> %</td>
                                    <td class="total text-right">
                                        Rp. <?=(isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) 
                                            ? myprice($prod['sales_price']-(($prod['discount_percentage']/100)*$prod['sales_price']))
                                            : myprice($prod['sales_price'])
                                        ?>
                                    </td>
                                </tr>
                            <?php $i++; $total_price += $prod['sales_price']; endforeach; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="7" class="text-right">GRAND TOTAL</td>
                                <td class="text-right">Rp. <strong><?=myprice($record['total_price'])?></strong></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
        <div class="span4">
        </div>
    </div>
    <hr/>
    <div class="row-fluid">
        <div class="span6">
            &nbsp;
        </div>
        <div class="span6 text-right">
            <a class="btn btn-primary" href="<?=$print_url?>" target="_blank"><i class="icon-print"></i> Cetak Faktur</a>
            <a class="btn btn-warning" href="<?=$back_url?>"><i class="icon-ban-circle"></i> Kembali</a>
        </div>
    </div>
</section>
