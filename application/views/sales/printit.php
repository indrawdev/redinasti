<div class="row">
    <div class="col-xs-12">
        <div class="text-center">
            <i class="fa fa-search-plus pull-left icon"></i>
            <h2>Invoice #<?=$record['sales_invoice']?></h2>
        </div>
        <hr>
        <div class="row">
            <div class="col-xs-12 col-md-3 col-lg-3 pull-left">
                <div class="panel panel-default height">
                    <div class="panel-heading"><?=$record['store']?></div>
                    <div class="panel-body">
                        <?=$record['store_address']?>
                    </div>
                </div>
            </div>
            <?php if ($record['sales_note'] != '') : ?>
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <div class="panel panel-default height">
                        <div class="panel-heading">Catatan</div>
                        <div class="panel-body">
                            <?=$record['sales_note']?>
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
            <!--
            <div class="panel-heading">
                <h3 class="text-center"><strong>List Pembelian</strong></h3>
            </div>
            -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <td class="text-center" style="width:1px;"><strong>No</strong></td>
                                <td><strong>Kode Prod.</strong></td>
                                <td class="text-right"><strong>QTY</strong></td>
                                <td><strong>Nama Barang</strong></td>
                                <td class="text-right"><strong>Harga</strong></td>
                                <td class="text-right"><strong>Diskon(%)</strong></td>
                                <td class="text-right"><strong>Total</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($record['productions']) && count($record['productions'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['productions'] as $prod) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="text-left"><?=$prod['item_name']?></td>
                                    <td class="text-right">1</td>
                                    <td class="text-left"><?=$prod['production_code']?></td>
                                    <td class="text-right">Rp. <?=myprice($prod['sales_price'])?></td>
                                    <td class="text-right"><?=$prod['discount_percentage']?></td>
                                    <td class="text-right">
                                        Rp. <?=(isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) 
                                            ? myprice($prod['sales_price']-(($prod['discount_percentage']/100)*$prod['sales_price']))
                                            : myprice($prod['sales_price'])
                                        ?>
                                    </td>
                                </tr>
                            <?php $i++; endforeach; ?>
                            <tr>
                                <td class="highrow" colspan="5"></td>
                                <td class="highrow text-right"><strong>Subtotal</strong></td>
                                <td class="highrow text-right">Rp. <strong><?=myprice($record['total_price'])?></td>
                            </tr>
                            <tr>
                                <td class="emptyrow" colspan="5"></td>
                                <td class="emptyrow text-right"><strong>Ongkos Kirim</strong></td>
                                <td class="emptyrow text-right"><strong>0</strong></td>
                            </tr>
                            <tr>
                                <td class="emptyrow" colspan="5"></td>
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
<div class="row">
    <div class="col-xs-12 col-md-3 col-lg-3 pull-left">
        <div class="height">
            Tanda Terima, <?=date('d M Y')?><br/><br/><br/><br/><br/>
        </div>
    </div>
    <div class="col-xs-12 col-md-3 col-lg-3 pull-right">
        <div class="height">
            Hormat kami,<br/><br/><br/><br/><br/>
        </div>
    </div>
</div>
