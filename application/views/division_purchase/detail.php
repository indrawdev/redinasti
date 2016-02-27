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
                        <label for="divisi">Divisi</label>: <strong><?=$record['division']?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="pic">PIC</label>: <?=$record['purchase_pic']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="shipping_date">Tanggal Kirim</label>: <?=iso_date($record['shipping_date'],'-')?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="licence_plate">Total Harga</label>: Rp. <?=myprice($record['total_price'])?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="purchase_note">Catatan</label>: <?=$record['purchase_note']?>
                    </div>
                </div>
            </fieldset>
            <?php if ($record['credit']) : ?>
            <fieldset class="listCredit">
                <legend>Hutang (Uang Cash)</legend>
                <div class="genCredit">
                    <table class="table table-striped table-bordered table-hover" id="tableCredit">
                        <thead>
                            <tr>
                                <th class="center" style='width:1px;'>No</th>
                                <th class="center" id="credit_price">Jumlah<span></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $a=0; $total_credit=0; ?>
                        <?php foreach ($record['credit'] as $crd) : ?>
                            <tr id="rowCredit<?=$a?>">
                                <td><?=($a+1)?></td>
                                <td class="price text-right">Rp. <?=myprice($crd['credit_price'])?></td>
                            </tr>
                            <?php $total_credit += $crd['credit_price']; ?>
                        <?php $a++; endforeach; ?>
                        <tr class="footer" id="tableFooter">
                            <td>&nbsp;</td>
                            <td class="text-right">Rp. <strong><?=myprice($total_credit)?></strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </fieldset>
            <?php endif; ?>
            <fieldset class="listProduct">
                <legend>Produk</legend>
                <div class="genProduct">
                    <table class="table table-striped table-bordered table-hover" id="tableProduct">
                        <thead>
                            <tr>
                                <th class="center" style='width:1px;'>No</th>
                                <th class="center" id="product_name">Produk <span></span></th>
                                <th class="center" id="qty">QTY <span></span></th>
                                <th class="center" id="product_price">Harga Satuan <span></span></th>
                                <th class="center" >Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($record['product']) && count($record['product'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['product'] as $prod) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="productOption"><?=$prod['product_name']?> (<?=$prod['product_code']?>)</td>
                                    <td class="qty text-right"><?=$prod['purchase_qty']?></td>
                                    <td class="price text-right">Rp. <?=myprice($prod['purchase_price'])?></td>
                                    <td class="text-right">Rp. <?=myprice(($prod['purchase_price']*$prod['purchase_qty']))?></td>
                                </tr>
                            <?php $i++; $total_price += ($prod['purchase_price']*$prod['purchase_qty']); endforeach; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="4">&nbsp;</td>
                                <td class="text-right">Rp. <strong><?=myprice($total_price)?></strong></td>
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
