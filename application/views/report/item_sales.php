<?php if (!is_ajax_requested()) : ?>
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
    <div id='list_data'>
        <form action="<?=$form_action?>" onsubmit="return false;" id="form-range">
            <div class="input-daterange report-daterange" id="datepicker">
                <input type="text" class="input-small" name="from_date" id="startRange" value="<?=(isset($post['to_date'])) ? $post['to_date'] : ''?>" />
                <span class="add-on">to</span>
                <input type="text" class="input-small" name="to_date" id="endRange" value="<?=(isset($post['to_date'])) ? $post['to_date'] : ''?>" />
                <button type="button" class="btn btn-success reload" title="Reload Data" id="submit-range"><i class="icon-refresh"></i></button>
            </div>
        </form>
        <div class="display_message" tabindex="1">
            <?php
                if (isset($error_msg)) {
                    echo $error_msg;
                }
            ?>
        </div>
        <!-- start listing data -->
        <table class="table table-striped table-bordered table-hover" id="reportTable">
            <thead>
                <tr>
                    <th class="center" style='width:1px;'>No</th>
                    <th id="item_category">Nama Barang<span></span></th>
                    <th id="item_code">Kode Barang<span></span></th>
                    <th id="division">Divisi<span></span></th>
                    <th id="item_stock">Stok<span></span></th>
                    <th id="total_sales_qty">Total Terjual<span></span></th>
                    <th id="total_retur_qty">Total Retur<span></span></th>
                    <th id="total_sales_price">Total Penjualan (Rp.)<span></span></th>
                    <th id="total_sales_retur">Total Retur (Rp.)<span></span></th>
                    <!--<th class="center">Action</th>-->
                </tr>
            </thead>
            <tbody>
                <?php $i=0; $total_stock=$total_price=$total_retur=$total_qty=$total_retur_qty=0; foreach ($data as $row) : $i++; ?>
                    <tr>
                        <td class="center"><?=$i?></td>
                        <td class="item_category"><?=$row['item_category']?></td>
                        <td class="item_code"><?=$row['item_code']?></td>
                        <td class="division"><?=$row['division']?></td>
                        <td class="text-right item_stock"><?=$row['item_stock']?></td>
                        <td class="text-right total_sales_qty"><?=$row['total_sales_qty']?></td>
                        <td class="text-right total_retur_qty"><?=$row['total_retur_qty']?></td>
                        <td class="text-right total_sales_price"><?=myprice($row['total_sales_price'])?></td>
                        <td class="text-right total_sales_retur"><?=myprice($row['total_retur_price'])?></td>
                        <!--
                        <td class="center">
                            <a href="<?=site_url($controller.'/item_sales_detail/'.$row['id_item'])?>" title="Detail Record" class="btn btn-info"><i class="icon-edit"></i></a>
                        </td>
                        -->
                    </tr>
                    <?php $total_stock += $row['item_stock']; $total_price += $row['total_sales_price']; $total_retur += $row['total_retur_price']; $total_qty += $row['total_sales_qty']; $total_retur_qty += $row['total_retur_qty']; ?>
                <?php endforeach; ?>
                <tr class="footer">
                    <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong><?=$total_qty?></strong></td>
                    <td class="text-right"><strong><?=$total_retur_qty?></strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_price)?></strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_retur)?></strong></td>
                    <!--<td class="text-right">&nbsp;</td>-->
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
<script type="text/javascript">
    $(function() {
        $("#submit-range").click(function() {
            var button_html = $(this).html(),
                start = $("#startRange").val(),
                end = $("#endRange").val();
            if (start != '' && end != '') {
                $.ajax({
                    url:'<?=$form_action?>',
                    type:'post',
                    dataType:'json',
                    data:$("#form-range").serialize(),
                    beforeSend: function() {
                        $("#submit-range").attr('disabled',true);
                        $("#submit-range").html('Loading...');
                        $("#reportTable tbody").slideUp();
                        $("#reportTable tbody").css('display','none');
                        $("#reportTable tbody").empty();
                        $('.display_message').empty();
                    },
                    success: function(data) {
                        $("#submit-range").removeAttr('disabled');
                        if (data['error']) {
                            $('.display_message').html(data['error']).focus();
                        }
                        if (data['return']) {
                            $("#reportTable tbody").html(data['return']);
                            $("#reportTable tbody").slideDown();
                        }
                        $("#submit-range").html(button_html);
                    }
                });
                $("#form-range").submit();
            }
        });
    });
</script>
<?php else : ?>
    <?php $i=0; $total_stock=$total_price=$total_retur=$total_qty=$total_retur_qty=0; foreach ($data as $row) : $i++; ?>
        <tr>
            <td class="center"><?=$i?></td>
            <td class="item_category"><?=$row['item_category']?></td>
            <td class="item_code"><?=$row['item_code']?></td>
            <td class="division"><?=$row['division']?></td>
            <td class="item_stock"><?=$row['item_stock']?></td>
            <td class="total_sales_qty"><?=$row['total_sales_qty']?></td>
            <td class="total_retur_qty"><?=$row['total_retur_qty']?></td>
            <td class="text-right total_sales_price"><?=myprice($row['total_sales_price'])?></td>
            <td class="text-right total_retur_price"><?=myprice($row['total_retur_price'])?></td>
            <td class="center">
                <a href="<?=site_url($controller.'/item_sales_detail/'.$row['id_item'])?>" title="Detail Record" class="btn btn-info"><i class="icon-edit"></i></a>
            </td>
        </tr>
        <?php $total_stock += $row['item_stock']; $total_price += $row['total_sales_price']; $total_retur += $row['total_retur_price']; $total_qty += $row['total_sales_qty']; $total_retur_qty += $row['total_retur_qty']; ?>
    <?php endforeach; ?>
    <tr class="footer">
        <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
        <td class="text-right"><strong><?=$total_qty?></strong></td>
        <td class="text-right"><strong><?=$total_retur_qty?></strong></td>
        <td class="text-right"><strong>Rp. <?=myprice($total_price)?></strong></td>
        <td class="text-right"><strong>Rp. <?=myprice($total_retur)?></strong></td>
        <td class="text-right">&nbsp;</td>
    </tr>
<?php endif; ?>