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
                        <label for="division">Division</label>: <strong><?=$record['division']?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="production_category">Kategori</label>: <?=$record['production_category']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="production_name">Nama Produksi/Barang</label>: <?=$record['production_name']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="production_code">Kode Produksi</label>: <strong><?=$record['production_code']?></strong>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="production_type">Tipe</label>: <?=($record['production_type'] == 3) ? 'Mentah' : ($record['production_type'] == 2) ? '1/2 Jadi' : 'Jadi'?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="size">Ukuran</label>: <?=$record['size']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="production_note">Catatan</label>: <?=$record['production_note']?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="production_hpp_price">HPP</label>: <strong>Rp. <?=myprice($record['production_hpp_price'])?></strong>
                    </div>
                </div>
            </fieldset>
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
                        <?php if (isset($record['products']) && count($record['products'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['products'] as $prod) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="productOption"><?=$prod['product_name']?> (<?=$prod['product_code']?>)</td>
                                    <td class="qty text-right"><?=$prod['production_product_qty']?></td>
                                    <td class="price text-right">Rp. <?=myprice($prod['production_product_price'])?></td>
                                    <td class="text-right">Rp. <?=myprice($prod['production_product_price']*$prod['production_product_qty'])?></td>
                                </tr>
                            <?php $i++; $total_price += ($prod['production_product_price']*$prod['production_product_qty']); endforeach; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="4">&nbsp;</td>
                                <td class="text-right">Rp. <strong><?=myprice($total_price)?></strong></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
            <fieldset class="listProduction">
                <legend>Kode Produksi</legend>
                <div class="genProduction">
                    <table class="table table-striped table-bordered table-hover" id="tableProduction">
                        <thead>
                            <tr>
                                <th class="center" style='width:1px;'>No</th>
                                <th class="center" id="production_name">Kode Produksi <span></span></th>
                                <th class="center" id="production_price">HPP <span></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($record['productions']) && count($record['productions'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['productions'] as $production) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="productionOption"><?=$production['product_code'].' - '.$production['product_name']?></td>
                                    <td class="price text-right">Rp. <?=myprice($production['price'])?></td>
                                </tr>
                            <?php $i++; $total_price += ($production['price']); endforeach; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="4">&nbsp;</td>
                                <td class="text-right">Rp. <strong><?=myprice($total_price)?></strong></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
            <fieldset class="listCost">
                <legend>Biaya Lain-lain</legend>
                <div class="genCost">
                    <table class="table table-striped table-bordered table-hover" id="tableCost">
                        <thead>
                            <tr>
                                <th class="center" style='width:1px;'>No</th>
                                <th class="center" id="production_cost_note">Catatan <span></span></th>
                                <th class="center" id="production_cost">Biaya <span></span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($record['costs']) && count($record['costs'])>0) : $i=0; $total_price=0; ?>
                            <?php foreach ($record['costs'] as $cost) : ?>
                                <tr id="row<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="productOption"><?=$prod['production_cost_note']?></td>
                                    <td class="qty text-right">Rp. <?=myprice($prod['production_cost'])?></td>
                                </tr>
                            <?php $i++; $total_price += $prod['production_cost']; endforeach; ?>
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
            <a class="btn btn-primary" href="<?=$print_url?>"><i class="icon-print"></i> Cetak </a>
            <a class="btn btn-warning" href="<?=$back_url?>"><i class="icon-ban-circle"></i> Kembali</a>
        </div>
    </div>
</section>
