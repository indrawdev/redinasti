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
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#allTrans" data-toggle="tab">Semua Transaksi</a></li>
                <li><a href="#paidTrans" data-toggle="tab">Lunas</a></li>
                <li><a href="#unpaidTrans" data-toggle="tab">Belum Lunas</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="allTrans">
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
                            $paid_transactions = array();
                            $unpaid_transactions = array();
                            $total_price=$total_qty=$total_retur=$total_buy=$total_payment=$total_laba=0; 
                            $i=0;
                            foreach ($data['transactions'] as $transaction) :
                                if ($transaction['payment_status'] == 2) {
                                    $paid_transactions[$i] = $transaction;
                                } else {
                                    $unpaid_transactions[$i] = $transaction;
                                }
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
                                            echo '('.$no.'). '.$production['production_code'].' '.$production['item_category'].' ('.$production['division'].') ('.$production['item_name'].') Rp. '.myprice($production['sales_price']).'<br/>';
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
                </div>
                <div class="tab-pane" id="paidTrans">
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
                            $total_price=$total_qty=$total_retur=$total_buy=$total_payment=$total_laba=0;
                            $i=0;
                            $paid_transaction = array_values($paid_transactions);
                            foreach ($paid_transaction as $paid) :
                            ?>
                                <tr>
                                    <td class="center"><?=$i+1?></td>
                                    <td class="sales_invoice"><strong><?=$paid['sales_invoice']?></strong></td>
                                    <td class="shipping_date"><strong><?=iso_date($paid['shipping_date'])?></strong></td>
                                    <td class="payment_status"><strong><?= ($paid['payment_status'] == 2) ? 'Lunas' : (($paid['payment_status'] == 1) ? 'Sudah bayar, belum lunas' : 'Proses')?></strong></td>
                                    <td class="productions">
                                        <?php
                                        $no=0;
                                        foreach ($paid['productions'] as $paid_production) {
                                            $no++;
                                            echo '('.$no.'). '.$paid_production['production_code'].' '.$paid_production['item_category'].' ('.$paid_production['division'].') ('.$paid_production['item_name'].') Rp. '.myprice($paid_production['sales_price']).'<br/>';
                                            $total_buy += $paid_production['buy_price'];
                                        }
                                        ?>
                                    </td>
                                    <td class="retur">
                                        <?php
                                        $no=0;
                                        foreach ($paid['retur'] as $paid_retur) {
                                            $no++;
                                            echo '('.$no.'). '.$paid_retur['production_code'].' '.$paid_retur['item_category'].' ('.$paid_retur['division'].') ('.$paid_retur['item_name'].')<br/>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-right total_price"><?=myprice($paid['total_price'])?></td>
                                    <td class="text-right total_retur"><?=myprice($paid['total_price_retur'])?></td>
                                    <td class="text-right total_payment"><?=myprice($paid['total_payment'])?></td>
                                </tr>
                                <?php 
                                    $total_price += $paid['total_price']; 
                                    $total_retur += $paid['total_price_retur']; 
                                    $total_payment += $paid['total_payment'];  
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
                </div>
                <div class="tab-pane" id="unpaidTrans">
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
                            $total_price=$total_qty=$total_retur=$total_buy=$total_payment=$total_laba=0;
                            $i=0;
                            $unpaid_transaction = array_values($unpaid_transactions);
                            foreach ($unpaid_transaction as $unpaid) :
                            ?>
                                <tr>
                                    <td class="center"><?=$i+1?></td>
                                    <td class="sales_invoice"><strong><?=$unpaid['sales_invoice']?></strong></td>
                                    <td class="shipping_date"><strong><?=iso_date($unpaid['shipping_date'])?></strong></td>
                                    <td class="payment_status"><strong><?= ($unpaid['payment_status'] == 2) ? 'Lunas' : (($unpaid['payment_status'] == 1) ? 'Sudah bayar, belum lunas' : 'Proses')?></strong></td>
                                    <td class="productions">
                                        <?php
                                        $no=0;
                                        foreach ($unpaid['productions'] as $unpaid_production) {
                                            $no++;
                                            echo '('.$no.'). '.$unpaid_production['production_code'].' '.$unpaid_production['item_category'].' ('.$unpaid_production['division'].') ('.$unpaid_production['item_name'].') Rp. '.myprice($unpaid_production['sales_price']).'<br/>';
                                            $total_buy += $unpaid_production['buy_price'];
                                        }
                                        ?>
                                    </td>
                                    <td class="retur">
                                        <?php
                                        $no=0;
                                        foreach ($unpaid['retur'] as $unpaid_retur) {
                                            $no++;
                                            echo '('.$no.'). '.$unpaid_retur['production_code'].' '.$unpaid_retur['item_category'].' ('.$unpaid_retur['division'].') ('.$unpaid_retur['item_name'].')<br/>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-right total_price"><?=myprice($unpaid['total_price'])?></td>
                                    <td class="text-right total_retur"><?=myprice($unpaid['total_price_retur'])?></td>
                                    <td class="text-right total_payment"><?=myprice($unpaid['total_payment'])?></td>
                                </tr>
                                <?php 
                                    $total_price += $unpaid['total_price']; 
                                    $total_retur += $unpaid['total_price_retur']; 
                                    $total_payment += $unpaid['total_payment'];  
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
                </div>
            </div>
        <hr/>
    </div>
</section>
<style>
    .ui-icon-carat-1-s,.ui-icon-carat-1-n{float: right;}
</style>