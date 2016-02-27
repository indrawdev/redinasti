
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <div class="display_message" tabindex="1">
        <?php 
            if (isset($message)) { 
                echo $message;
            }
        ?>
    </div>
    <?php if ( ($record['total_price']-$record['total_discount']) > $total_paid) : ?>
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
    <?php endif; ?>
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="purchase_invoice">No Faktur:</label> <strong><?=$record['purchase_invoice']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="id_division">Divisi:</label> <strong><?=$record['division']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="total_price">Total Harga:</label> <strong>Rp. <!--<?=myprice($record['total_price'])?> - <?=myprice($record['total_discount'])?> = --><?=myprice($record['total_price'])?></strong>
                        </div>
                    </div>
                    <?php if ( ($record['total_price']-$record['total_discount']) <= $total_paid) : ?>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="payment_status">Status Pembayaran:</label> <strong><?=($record['payment_status'] == 1) ? 'Sudah dibayar tapi belum lunas' : ($record['payment_status'] == 2) ? 'Lunas' : 'Transaksi baru'?></strong>
                        </div>
                    </div>
                        <?php if ($division_credit) : ?>
                        <div class="row-fluid row-label">
                            <div class="span12">
                                <label for="total_credit">Total Piutang:</label> <strong>Rp. <?=myprice($division_credit)?></strong>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php /*
                    <div class="row-fluid" style="margin-bottom: 30px;">
                        <div class="span12">
                            <label for="purchase_note">Catatan Pembelian:</label> <?=$record['purchase_note']?>
                        </div>
                    </div>
                     * 
                     */
                    ?>
                    <?php if ( ($record['total_price']-$record['total_discount']) > $total_paid) : /* ?>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="payment_type">Tipe Pembayaran</label>
                            <label class="radio">
                                <input type="radio" name="payment_type" id="cash" value="1" <?=(isset($post['payment_type']) && $post['payment_type'] == 1) ? 'checked="checked"' : ''?>/> Cash/Tunai
                            </label>
                            <label class="radio">
                                <input type="radio" name="payment_type" id="giro" value="2" <?=(isset($post['payment_type']) && $post['payment_type'] == 2) ? 'checked="checked"' : ''?>/> Giro
                            </label>
                        </div>
                    </div>
                    <div class="row-fluid giro-payment">
                        <div class="span12">
                            <label for="id_giro">Giro</label>
                            <select name="id_giro" id="id_giro">
                                <option value=""></option>
                                <?php
                                foreach ($giros as $giro) {
                                    if (isset($post['id_giro']) && $post['id_giro'] == $giro['id_giro']) {
                                        echo '<option value="'.$giro['id_giro'].'" selected="selected">'.$giro['giro_code'].' - '.$giro['giro_bank'].' - '.myprice($giro['giro_price']).'</option>';
                                    } else {
                                        echo '<option value="'.$giro['id_giro'].'">'.$giro['giro_code'].' - '.$giro['giro_bank'].' - '.myprice($giro['giro_price']).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid cash-payment">
                        <div class="span12">
                            <label for="payment_total">Jumlah Pembayaran</label>
                            Rp. <input type="text" id="payment_total" name="payment_total" value="<?=(isset($post['payment_total'])) ? $post['payment_total'] : ($record['total_price']-$total_paid)?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="payment_note">Catatan Pembayaran</label>
                            <textarea id="payment_note" rows="4" class="input-block-level" name="payment_note" style="resize: none"><?=(isset($post['payment_note'])) ? $post['payment_note'] : ''?></textarea>
                        </div>
                    </div>
                    <?php */ endif; ?>
                </fieldset>
                <?php if ( ($record['total_price']-$record['total_discount']) > $total_paid) : ?>
                <fieldset class="listPayment">
                    <legend>Pembayaran</legend>
                    <div class="genPayment">
                        <table class="table table-striped table-bordered table-hover" id="tablePayment">
                            <thead>
                                <tr>
                                    <th class="center">Tipe Pembayaran <span></span></th>
                                    <th class="center">Total</th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_payment']) && count($post['post_payment'])>0) : $a=0; $total_payment=0; ?>
                                <?php foreach ($post['post_payment'] as $pay) : ?>
                                    <tr id="rowPay<?=$a?>">
                                        <td class="productOption">
                                            <select name="post_payment[<?=$a?>][type]" id="post_payment_type_<?=$a?>" class="paySelect" data-id="<?=$a?>">
                                                <option value="0" <?php (isset($pay['type']) && $pay['type'] == 0) ? 'selected' : '' ?>>Cash</option>
                                                <?php foreach ($giros as $giro) : ?>
                                                    <option value="<?=$giro['id_giro']?>" <?php ((isset($pay['type'])) && ($pay['type'] == $giro['id_giro'])) ? 'selected="selected"' : '' ?>><?=$giro['giro_code'].' - '.$giro['giro_bank']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="price">Rp. <input type="text" name="post_payment[<?=$a?>][price]" id="post_payment_price_<?=$a?>" value="<?=(isset($pay['price'])) ? $pay['price'] : '' ?>" class="input-text number_only"/></td>
                                        <td><button type="button" class="btn btn-danger delPayment" id="delPayment-<?=$a?>" onclick="delPayment('<?=$a?>')">(-)</button></td>
                                    </tr>
                                <?php $a++; $total_payment += $pay['price']; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer" id="tableFooterPayment">
                                <td colspan="5"><button type="button" class="btn btn-success pull-right" id="addPayment">(+)</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <?php endif; ?>
                <fieldset class="listProduct">
                    <legend>Histori Pembayaran</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableProduct">
                            <thead>
                                <tr>
                                    <th class="center" style='width:1px;'>No</th>
                                    <th class="center">Tanggal Pembayaran <span></span></th>
                                    <th class="center">Tipe Pembayaran <span></span></th>
                                    <th class="center">Catatan</th>
                                    <th class="center">Bukti Pembayaran</th>
                                    <th class="center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($payments) && count($payments)>0) : $i=0; $total_price=0; ?>
                                <?php foreach ($payments as $payment) : ?>
                                    <tr id="row<?=$i?>">
                                        <td><?=($i+1)?></td>
                                        <td><?=($payment['payment_date'] != '') ? iso_date($payment['payment_date']) : '' ?></td>
                                        <td><?=($payment['payment_type'] == 2) ? 'Giro' : 'Cash/Tunai'?></td>
                                        <td><?=$payment['payment_note']?></td>
                                        <td class="center"><?= ($payment['payment_image'] !='' && file_exists(IMG_UPLOAD_DIR.'purchase/'.$payment['payment_image'])) ? '<img class="thumb-gal" src="'.IMG_UPLOAD_DIR_REL.'/purchase/sml_'.$payment['payment_image'].'"/>' : '---' ?></td>
                                        <td class="price text-right">Rp. <?=myprice($payment['payment_total'])?></td>
                                    </tr>
                                <?php $i++; $total_price += $payment['payment_total']; endforeach; ?>
                                <tr class="footer" id="tableFooter">
                                    <td colspan="5">&nbsp;</td>
                                    <td class="text-right">Rp. <strong><?=myprice($total_price)?></strong></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <?php if ( ($record['total_price']-$record['total_discount']) > $total_paid) : ?>
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="shipDate">Tanggal Pembayaran</label>
                            <div id="shipDate" class="input-prepend">
                                <span class="add-on curp">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                                <input data-format="yyyy-mm-dd" type="text" class="span11 pop-datepicker" name="payment_date" value="<?=(isset($post['payment_date'])) ? $post['payment_date'] : date('Y-m-d')?>" placeholder="yyyy-mm-dd"/>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( ($record['total_price']-$record['total_discount']) > $total_paid) : ?>
        <hr/>
        <div class="row-fluid">
            <div class="span6">
                &nbsp;
            </div>
            <div class="span6 text-right">
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Simpan</button>
                <a class="btn btn-warning" href="<?=$cancel_url?>"><i class="icon-ban-circle"></i> Batal</a>
            </div>
        </div>
        <?php endif; ?>
    <?php if ( ($record['total_price']-$record['total_discount']) > $total_paid) : ?>
    </form>
    <script type="text/javascript">
        /**
        $(function(){
            if ($('input:radio[name=payment_type]:checked').val() == '2') {
                $(".cash-payment").fadeOut('fast');
                $(".giro-payment").fadeIn('slow');
            } else {
                $(".cash-payment").fadeIn('slow');
                $(".giro-payment").fadeOut('fast');
            }

            $("input[name='payment_type']").bind("click",PaymentClicks);
        });

        function PaymentClicks()
        {
            if ($(this).val() == '2')
            {
                $(".giro-payment").fadeIn('slow');
                $(".cash-payment").fadeOut('fast');
            }
            else
            {
                $(".cash-payment").fadeIn('slow');
                $(".giro-payment").fadeOut('fast');
            }
        }
        */
       
        var row = <?=$payment_count?>;
        $(function() {
            $("#addPayment").click(function() {
                addPayment();
            });
            $(".paySelect").each(function() {
                var this_id = $(this).attr('data-id');
                $(this).select2().on('change', function(e) {
                    var giro_id = e.val;
                    if (giro_id != 0) {
                        $.ajax({
                            url:'<?=$giro_info_url?>',
                            type:'post',
                            dataType:'json',
                            data:'giro_id='+giro_id,
                            success: function(data) {
                                if (data['error']) {
                                    $('.display_message').html(data['error']).focus();
                                }
                                if (data['value']['giro_price']) {
                                    $("#post_payment_price_"+this_id).val(data['value']['giro_price']).attr('readonly',true);
                                }
                            }
                        });
                    } else {
                        $("#post_payment_price_"+this_id).val('0').removeAttr('readonly');
                    }
                });
            });   
        });

        function addPayment() {
            html = '<tr id="rowPay'+ row +'">';
            html += '<td class="productOption">';
            html += '<select name="post_payment['+ row +'][type]" id="post_payment_type_'+ row +'" data-id="'+ row +'">';
            html += '<option value="0">Cash</option>';
            <?php foreach ($giros as $giro) : ?>
                html += '<option value="<?=$giro['id_giro']?>"><?=$giro['giro_code'].' - '.$giro['giro_bank']?></option>';
            <?php endforeach; ?>
            html += '</select>';
            html += '</td>';
            html += '<td class="price">Rp. <input type="text" name="post_payment['+ row +'][price]" id="post_payment_price_'+ row +'" value="" class="input-text number_only"/></td>';
            html += '<td><button type="button" class="btn btn-danger delPayment" id="delPayment-'+ row +'" onclick="delPayment(\''+row+'\')">(-)</button></td>';
            html += '</tr>';
            $('#tablePayment #tableFooterPayment').before(html);
            $("#post_payment_type_"+ row).select2().on('change', function(e) {
                var this_id = $(this).attr('data-id');
                var giro_id = e.val,
                    seltor = $(e.target).attr('data-id');
                if (giro_id != 0) {
                    $.ajax({
                        url:'<?=$giro_info_url?>',
                        type:'post',
                        dataType:'json',
                        data:'giro_id='+giro_id,
                        success: function(data) {
                            if (data['error']) {
                                $('.display_message').html(data['error']).focus();
                            }
                            if (data['value']['giro_price']) {
                                $("#post_payment_price_"+this_id).val(data['value']['giro_price']).attr('readonly',true);
                            }
                        }
                    });
                } else {
                    $("#post_payment_price_"+seltor).val('0').removeAttr('readonly');
                }
            });
            $("#post_payment_id_"+ row).select2("enable");
            number_only('.number_only');
            row++;
        }

        function delPayment(id) {
            $("#rowPay"+id).remove();
            row--;
        }
    </script>
    <?php endif; ?>
</section>
