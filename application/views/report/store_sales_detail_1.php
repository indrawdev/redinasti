<section class="well animated fadeInUp">
    <div class="row-fluid">
        <div class="span12">
            <div style='float: left;color: #00a429'>
                <ul class="breadcrumb">
                </ul>
            </div>
            <div style="clear:both;"></div>
            <?php if (isset($message)) { ?>
                <div style='float: left;color: #00a429'><?= $message ?></div>
            <?php } ?>
            <?php if (isset($tmp_msg)) { ?>
                <div style='float: left;color: #00a429'><?= $tmp_msg ?></div>
            <?php } ?>
            <?php if (isset($success_msg)) { ?>
                <div style='float: left;color: #00a429'><?= $success_msg ?></div>
            <?php } ?>
        </div>
    </div>  
    <hr>
    <fieldset>
        <legend><?=$data['store']?></legend>
    </fieldset>
    <div id='list_data'>
        <?php
            if (isset($error_msg)) {
                echo $error_msg;
            }
        ?>
        <!-- start listing data -->
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="center" style='width:1px;'>No</th>
                    <th id="sales_invoice">Invoice<span></span></th>
                    <th id="shipping_date">Tanggal Kirim<span></span></th>
                    <th id="payment_status">Status<span></span></th>
                    <th id="productions">Barang<span></span></th>
                    <th id="retur">Barang (Retur)<span></span></th>
                    <th id="total_price">Total Harga (Rp.)<span></span></th>
                    <th id="total_retur">Total Retur (Rp.)<span></span></th>
                    <th id="total_payment">Total Pembayaran (Rp.)<span></span></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i=0;
                $total_price=$total_qty=$total_retur=$total_buy=$total_payment=$total_laba=0; 
                foreach ($data['transactions'] as $transaction) :
                ?>
                    <tr>
                        <td class="center"><?=$i+1?></td>
                        <td class="sales_invoice"><strong><?=$transaction['sales_invoice']?></strong></td>
                        <td class="shipping_date"><strong><?=iso_date($transaction['shipping_date'])?></strong></td>
                        <td class="payment_status"><strong><?= ($transaction['payment_status'] == 2) ? 'Lunas' : (($transaction['payment_status'] == 1) ? 'Sudah bayar, belum lunas' : 'Proses')?></strong></td>
                        <td class="productions">
                            <?php
                            $no=0;
                            foreach ($transaction['productions'] as $production) {
                                $no++;
                                echo '('.$no.'). '.$production['production_code'].' '.$production['item_category'].' ('.$production['division'].') ('.$production['item_name'].')<br/>';
                                $total_buy += $production['buy_price'];
                            }
                            ?>
                        </td>
                        <td class="retur">
                            <?php
                            $no=0;
                            foreach ($transaction['retur'] as $retur) {
                                $no++;
                                echo '('.$no.'). '.$retur['production_code'].' '.$retur['item_category'].' ('.$retur['division'].') ('.$retur['item_name'].')<br/>';
                            }
                            ?>
                        </td>
                        <td class="text-right total_price"><?=myprice($transaction['total_price'])?></td>
                        <td class="text-right total_retur"><?=myprice($transaction['total_price_retur'])?></td>
                        <td class="text-right total_payment"><?=myprice($transaction['total_payment'])?></td>
                    </tr>
                    <?php 
                        $total_price += $transaction['total_price']; 
                        $total_retur += $transaction['total_price_retur']; 
                        $total_payment += $transaction['total_payment'];  
                        $i++; 
                    ?>
                <?php endforeach; ?>
                <tr class="footer">
                    <td class="text-right" colspan="6"><strong>TOTAL</strong></td>
                    <!--
                    <td class="text-right"><strong><?=myprice($total_buy)?></strong></td>
                    <td class="text-right"><strong>&nbsp;</strong></td>
                    -->
                    <td class="text-right"><strong><?=myprice($total_price)?></strong></td>
                    <td class="text-right"><strong><?=myprice($total_retur)?></strong></td>
                    <td class="text-right"><strong><?=myprice($total_payment)?></strong></td>
                </tr>
                <!--
                <tr class="footer">
                    <td class="text-right" colspan="8"><strong>TOTAL LABA = (total penjualan - total retur) - total harga beli)</strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_laba)?></strong></td>
                </tr>
                -->
            </tbody>
        </table>
        <!-- end of listing data -->
        <hr/>
    </div>
</section>
<style>
    .ui-icon-carat-1-s,.ui-icon-carat-1-n{float: right;}
</style>