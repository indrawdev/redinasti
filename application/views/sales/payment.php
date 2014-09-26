<link href="<?= CSS_URL ?>bootstrap-fileupload.min.css" rel="stylesheet"/>
<script language="JavaScript" src="<?= JS_URL ?>bootstrap-fileupload.min.js"></script>
<script language="JavaScript" src="<?= JS_URL ?>jquery.alphanumeric.pack.js"></script>
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <?php 
        if (isset($message)) { 
            echo $message;
        }
    ?>
    <?php if ($record['total_price'] > $total_paid) : ?>
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
    <?php endif; ?>
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="sales_invoice">No Faktur:</label> <strong><?=$record['sales_invoice']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="store">Toko:</label> <strong><?=$record['store']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="total_price">Total Harga:</label> <strong>Rp. <?=myprice($record['total_price'])?></strong>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-bottom: 30px;">
                        <div class="span12">
                            <label for="sales_note">Catatan Pembelian:</label> <?=$record['sales_note']?>
                        </div>
                    </div>
                    <?php if ($record['total_price'] > $total_paid) : ?>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="payment_note">Catatan Pembayaran</label>
                            <textarea id="payment_note" rows="4" class="input-block-level" name="payment_note" style="resize: none"><?=(isset($post['payment_note'])) ? $post['payment_note'] : ''?></textarea>
                        </div>
                    </div>
                    <?php endif; ?>
                </fieldset>
                <?php if ($record['total_price'] > $total_paid) : ?>
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
                                            <select name="post_payment[<?=$a?>][type]" id="post_payment_<?=$a?>_type" class="paySelect" data-id="<?=$a?>">
                                                <option value="1" <?=(isset($pay['type']) && $pay['type'] == 1) ? 'selected' : '' ?>>Cash</option>
                                                <option value="2" <?=(isset($pay['type']) && $pay['type'] == 2) ? 'selected' : '' ?>>Giro</option>
                                                <option value="3" <?=(isset($pay['type']) && $pay['type'] == 3) ? 'selected' : '' ?>>Potongan Lain-lain</option>
                                            </select>
                                            <div id="info-<?=$a?>" style="margin-top:10px;">
                                                <div id="info-<?=$a?>-cash" <?=(isset($pay['type']) && $pay['type'] == 1) ? '' : 'class="hide-it"'?>>
                                                </div>
                                                <div id="info-<?=$a?>-giro" <?=(isset($pay['type']) && $pay['type'] == 2) ? '' : 'class="hide-it"'?>>
                                                    <input type="text" class="input-text" name="post_payment[<?=$a?>][giro_code]" id="post_payment_<?=$a?>_giro_code" placeholder="Bilyet Giro" value="<?=(isset($pay['giro_code'])) ? $pay['giro_code'] : ''?>"/><br/>
                                                    <input type="text" class="input-text" name="post_payment[<?=$a?>][giro_bank]" id="post_payment_<?=$a?>_giro_bank" placeholder="Nama Bank" value="<?=(isset($pay['giro_bank'])) ? $pay['giro_bank'] : ''?>"/><br/>
                                                    <input data-format="yyyy-mm-dd" type="text" class="span11 pop-datepicker" name="post_payment[<?=$a?>][giro_date]" value="<?=(isset($post['giro_date'])) ? $post['giro_date'] : date('Y-m-d')?>" placeholder="Tanggal Giro" id="post_payment_<?=$a?>_giro_date"/>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="price"><input type="text" name="post_payment[<?=$a?>][price]" id="post_payment_<?=$a?>_price" value="<?=(isset($pay['price'])) ? $pay['price'] : '' ?>" class="input-text number_only" placeholder="Rp. "/></td>
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
                                        <td>
                                            <?php
                                                if ($payment['payment_type'] == 2) {
                                                    echo 'Giro';
                                                    if (isset($payment['giro_info'])) {
                                                        echo ' ('.$payment['giro_info']['giro_code'].' - '.$payment['giro_info']['giro_bank'].')';
                                                    }
                                                } else {
                                                    echo 'Cash/Tunai';
                                                }
                                            ?>
                                        </td>
                                        <td><?=$payment['payment_note']?></td>
                                        <td class="center"><?= ($payment['payment_image'] !='' && file_exists(IMG_UPLOAD_DIR.'sales/'.$payment['payment_image'])) ? '<img class="thumb-gal" src="'.IMG_UPLOAD_DIR_REL.'/sales/sml_'.$payment['payment_image'].'"/>' : '---' ?></td>
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
                <?php if ($record['total_price'] > $total_paid) : ?>
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
                <fieldset>
                    <legend>Bukti</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="payment_image">Bukti Pembayaran</label>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <img src="" />
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                    
                                </div>
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Pilih Gambar</span>
                                        <span class="fileupload-exists">Ganti</span>
                                        <input name="payment_image" id="payment_image" type="file" />
                                    </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($record['total_price'] > $total_paid) : ?>
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
    <?php if ($record['total_price'] > $total_paid) : ?>
    </form>
    <script type="text/javascript">
        var row = '<?=$payment_count?>';
        $(function() {
            $("#addPayment").click(function() {
                addPayment();
            });
            $(".paySelect").each(function() {
                var this_id = $(this).attr('data-id');
                //PaymentClicks(this_id,1);
                $(this).select2().on('change', function(e) {
                    var payment_type = e.val;
                    if (payment_type == 2) {
                        $("#info-"+this_id+"-cash").fadeOut('fast');
                        $("#info-"+this_id+"-giro").fadeIn('slow');
                    } else {
                        $("#info-"+this_id+"-giro").fadeOut('fast');
                        $("#info-"+this_id+"-cash").fadeIn('slow');
                    }
                });
            });  
        });

        function addPayment() {
            html = '<tr id="rowPay'+ row +'">';
            html += '<td class="productOption">';
            html += '<select name="post_payment['+ row +'][type]" id="post_payment_type_'+ row +'" data-id="'+ row +'">';
            html += '<option value="1">Cash</option>';
            html += '<option value="2">Giro</option>';
            html += '<option value="3">Potongan Lain-lain</option>';
            html += '</select>';
            html += '<div id="info-'+row+'" style="margin-top:10px;">';
            html += '<div class="hide-it" id="info-'+row+'-cash">';
            html += '</div>';
            html += '<div class="hide-it" id="info-'+row+'-giro">';
            html += '<input type="text" class="input-text" name="post_payment['+row+'][giro_code]" id="post_payment_'+row+'_giro_code" placeholder="Bilyet Giro" value=""/><br/>';
            html += '<input type="text" class="input-text" name="post_payment['+row+'][giro_bank]" id="post_payment_'+row+'_giro_bank" placeholder="Nama Bank" value=""/><br/>';
            html += '<input data-format="yyyy-mm-dd" type="text" class="input-text" name="post_payment['+row+'][giro_date]" value="<?=date('Y-m-d')?>" placeholder="Tanggal Giro" id="post_payment_'+row+'_giro_date"/>';
            html += '</div>';
            html += '</div>';
            html += '</td>';
            html += '<td class="price"><input type="text" name="post_payment['+ row +'][price]" id="post_payment_'+ row +'_price" value="" class="input-text number_only" placeholder="Rp. "/></td>';
            html += '<td><button type="button" class="btn btn-danger delPayment" id="delPayment-'+ row +'" onclick="delPayment(\''+row+'\')">(-)</button></td>';
            html += '</tr>';
            $('#tablePayment #tableFooterPayment').before(html);
            $("#post_payment_type_"+ row).select2().on('change', function(e) {
                var payment_type = e.val,
                    seltor = $(e.target).attr('data-id');
                if (payment_type == 2) {
                    $("#info-"+seltor+"-cash").fadeOut('fast');
                    $("#info-"+seltor+"-giro").fadeIn('slow');
                } else {
                    $("#info-"+seltor+"-giro").fadeOut('fast');
                    $("#info-"+seltor+"-cash").fadeIn('slow');
                }
            });
            $("#post_payment_"+ row +"_type").select2("enable");
            number_only('.number_only');
            row++;
        }

        function delPayment(id) {
            $("#rowPay"+id).remove();
            row--;
        }
        
        function PaymentClicks(rows,val)
        {
            if (val == '2') {
                $("#info-"+rows+"-cash").fadeOut('fast');
                $("#info-"+rows+"-giro").fadeIn('slow');
            } else {
                $("#info-"+rows+"-giro").fadeOut('fast');
                $("#info-"+rows+"-cash").fadeIn('slow');
            }
        }
        /*
        $(function(){
            if ($('input:radio[name=payment_type]:checked').val() == '2') {
                $(".giro-payment").fadeIn('slow');
            } else {
                $(".giro-payment").fadeOut('fast');
            }

            $("input[name='payment_type']").bind("click",PaymentClicks);
        });

        function PaymentClicks()
        {
            if ($(this).val() == '2')
            {
                $(".giro-payment").fadeIn('slow');
            }
            else
            {
                $(".giro-payment").fadeOut('fast');
            }
        }
        */
    </script>
    <?php endif; ?>
</section>
