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
                        <label for="purchase_invoice">No Faktur</label>: <strong><?=$record['purchase_invoice']?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="id_division">Divisi</label>: <strong><?=$record['division']?></strong>
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
                        <label for="total_price">Total Harga</label>: Rp. <?=myprice($record['total_price'])?>
                    </div>
                </div>
                <!--
                <div class="row-fluid">
                    <div class="span12">
                        <label for="total_price">Total HPP</label>: Rp. <?=myprice($record['total_hpp'])?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="total_price">Total Discount</label>: Rp. <?=myprice($record['total_discount'])?>
                    </div>
                </div>
                -->
                <div class="row-fluid">
                    <div class="span12">
                        <label for="purchase_note">Catatan</label>: <?=$record['purchase_note']?>
                    </div>
                </div>
            </fieldset>
            <fieldset class="listProduction">
                <legend>Barang Produksi</legend>
                <div class="genProduction">
                    <table class="table table-striped table-bordered table-hover" id="tableProduction">
                        <thead>
                            <tr>
                                <tr>
                                    <th class="center" style='width:1px;'>No</th>
                                    <th class="center" id="product_code_name">Nama Barang <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                    <th class="center" id="price">Harga Jual <span></span></th>
                                    <!--
                                    <th class="center" id="hpp">HPP <span></span></th>
                                    <th class="center" id="discount">Diskon <span></span></th>
                                    -->
                                </tr>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($record['production']) && count($record['production'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['production'] as $prod) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="product_code_name"><?=$prod['production_code']?> (<?=$prod['item_name']?>)</td>
                                    <td class="qty text-right"><?=$prod['purchase_qty']?></td>
                                    <td class="price text-right">Rp. <?=myprice($prod['purchase_sales_price'])?></td>
                                    <!--
                                    <td class="hpp text-right">Rp. <?=myprice($prod['purchase_hpp_price'])?></td>
                                    <td class="discount text-right">Rp. <?=myprice($prod['purchase_discount_price'])?></td>
                                    -->
                                </tr>
                            <?php $i++; endforeach; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="3" class="text-right">GRAND TOTAL</td>
                                <td class="text-right">Rp. <strong><?=myprice($record['total_price'])?></strong></td>
                                <!--
                                <td class="text-right">Rp. <strong><?=myprice($record['total_hpp'])?></strong></td>
                                <td class="text-right">Rp. <strong><?=myprice($record['total_discount'])?></strong></td>
                                -->
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
