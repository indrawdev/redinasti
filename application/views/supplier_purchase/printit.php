<div class="row">
    <div class="col-xs-12">
        <div class="text-center">
            <i class="fa fa-search-plus pull-left icon"></i>
            <h2>Invoice #<?=$record['purchase_invoice']?></h2>
        </div>
        <hr>
        <div class="row">
            <div class="col-xs-12 col-md-3 col-lg-3 pull-left">
                <div class="panel panel-default height">
                    <div class="panel-heading"><?=$record['supplier']?></div>
                    <div class="panel-body">
                        <?=$record['supplier_address']?><br/>
                    </div>
                </div>
            </div>
            <?php if ($record['purchase_note'] != '') : ?>
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <div class="panel panel-default height">
                        <div class="panel-heading">Catatan</div>
                        <div class="panel-body">
                            <?=$record['purchase_note']?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-xs-12 col-md-3 col-lg-3 pull-right">
                <div class="panel panel-default height">
                    <div class="panel-heading">Detail Pengiriman</div>
                    <div class="panel-body">
                        Tanggal Kirim: <strong><?=iso_date($record['shipping_date'],'-')?></strong><br/>
                        <?=$record['driver']?><br/>
                        <?=$record['licence_plate']?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="text-center"><strong>List Pembelian</strong></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <td class="text-center" style="width:1px;"><strong>No</strong></td>
                                <td><strong>Produk</strong></td>
                                <td class="text-right"><strong>QTY</strong></td>
                                <td class="text-right"><strong>Harga Satuan</strong></td>
                                <td class="text-right"><strong>Total</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($record['product']) && count($record['product'])>0) : $i=0; $total_price=0; ?>
                                <?php foreach ($record['product'] as $prod) : ?>
                                    <tr id="row<?=$i?>">
                                        <td><?=($i+1)?></td>
                                        <td class="text-left"><?=$prod['product_name']?> (<?=$prod['product_code']?>)</td>
                                        <td class="text-right"><?=$prod['purchase_qty']?></td>
                                        <td class="text-right">Rp. <?=myprice($prod['purchase_price'])?></td>
                                        <td class="text-right">Rp. <?=myprice(($prod['purchase_price']*$prod['purchase_qty']))?></td>
                                    </tr>
                                <?php $i++; $total_price += ($prod['purchase_price']*$prod['purchase_qty']); endforeach; ?>
                                    
                                <tr>
                                    <td class="highrow" colspan="3"></td>
                                    <td class="highrow text-right"><strong>Subtotal</strong></td>
                                    <td class="highrow text-right">Rp. <strong><?=myprice($record['total_price'])?></td>
                                </tr>
                                <tr>
                                    <td class="emptyrow" colspan="3"></td>
                                    <td class="emptyrow text-right"><strong>Ongkos Kirim</strong></td>
                                    <td class="emptyrow text-right"><strong>0</strong></td>
                                </tr>
                                <tr>
                                    <td class="emptyrow" colspan="3"></td>
                                    <td class="emptyrow text-right"><strong>Grand Total</strong></td>
                                    <td class="emptyrow text-right">Rp. <strong><?=myprice($record['total_price'])?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
