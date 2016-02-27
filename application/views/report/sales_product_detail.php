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
        <legend><?=$data['product_name']?></legend>
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
                    <th id="product_name">Invoice<span></span></th>
                    <th id="buy">Harga Beli (Rp.)<span></span></th>
                    <th id="sell">Harga Jual (Rp.)<span></span></th>
                    <th id="total_qty">QTY<span></span></th>
                    <th id="total_retur">QTY Retur<span></span></th>
                    <th id="total_price">Total (harga jual x qty) (Rp.)<span></span></th>
                </tr>
            </thead>
            <tbody>
                <?php $i=0; $total_price=$total_qty=$total_retur=$total_buy=$total_sell=$total_laba=0; foreach ($data['sales'] as $row) : $i++; ?>
                    <tr>
                        <td class="center"><?=$i?></td>
                        <td class="invoice"><strong><?=$row['purchase_invoice']?></strong></td>
                        <td class="text-right buy"><?=myprice($row['purchase_buy'])?></td>
                        <td class="text-right sell"><?=myprice($row['purchase_price'])?></td>
                        <td class="text-right total_qty"><?=$row['total_sales_qty']?></td>
                        <td class="text-right total_retur"><?=$row['total_retur_qty']?></td>
                        <td class="text-right total_price"><?=myprice($row['total_sales_price'])?></td>
                    </tr>
                    <?php 
                        $total_price += $row['total_sales_price']; 
                        $total_qty += $row['total_sales_qty']; 
                        $total_retur += $row['total_retur_qty']; 
                        $total_buy += $row['purchase_buy']; 
                        $total_sell += $row['purchase_price']; 
                        $total_laba += ($row['total_sales_price'])-($row['purchase_buy']*$row['total_sales_qty']);
                        
                    ?>
                <?php endforeach; ?>
                <tr class="footer">
                    <td class="text-right" colspan="2"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong><?=myprice($total_buy)?></strong></td>
                    <td class="text-right"><strong><?=myprice($total_sell)?></strong></td>
                    <td class="text-right"><strong><?=$total_qty?></strong></td>
                    <td class="text-right"><strong><?=$total_retur?></strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_price)?></strong></td>
                </tr>
                <tr class="footer">
                    <td class="text-right" colspan="6"><strong>TOTAL LABA (total harga jual - total harga beli)</strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_laba)?></strong></td>
                </tr>
            </tbody>
        </table>
        <!-- end of listing data -->
        <hr/>
    </div>
</section>
<style>
    .ui-icon-carat-1-s,.ui-icon-carat-1-n{float: right;}
</style>