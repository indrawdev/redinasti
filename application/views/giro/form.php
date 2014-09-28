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
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Content</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="giro_code">Bilyet Giro</label>
                            <input type="text" id="giro_code" class="input-block-level" name="giro_code" value="<?=(isset($post['giro_code'])) ? $post['giro_code'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="giro_bank">Nama Bank</label>
                            <input type="text" id="giro_bank" class="input-block-level" name="giro_bank" value="<?=(isset($post['giro_bank'])) ? $post['giro_bank'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="shipDate">Tanggal Giro</label>
                            <div id="shipDate" class="input-prepend">
                                <span class="add-on curp">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                                <input data-format="yyyy-mm-dd" type="text" class="span11 pop-datepicker" name="giro_date" value="<?=(isset($post['giro_date'])) ? $post['giro_date'] : date('Y-m-d')?>" placeholder="yyyy-mm-dd"/>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="giro_price">Nominal Giro</label>
                            <input type="text" id="giro_price" class="input-block-level" name="giro_price" value="<?=(isset($post['giro_price'])) ? $post['giro_price'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="giro_invoice">Invoice (Asal Giro)</label>
                            <input type="text" id="giro_invoice" class="input-block-level" name="giro_invoice" value="<?=(isset($post['giro_invoice'])) ? $post['giro_invoice'] : ''?>"/>
                        </div>
                    </div>
                </fieldset>
                <?php if (isset($histories)) : ?>
                <fieldset class="listHistory">
                    <legend>Histori</legend>
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#from" data-toggle="tab">Asal Giro</a></li>
                            <li><a href="#to" data-toggle="tab">Penggunaan Pembayaran</a></li>
                            <li><a href="#cashed" data-toggle="tab">Di Uangkan</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="from">
                                <div class="genProduct">
                                    <table class="table table-striped table-bordered table-hover" id="tableHistory">
                                        <thead>
                                            <tr>
                                                <th class="center" style='width:1px;'>No</th>
                                                <th class="center">Invoice <span></span></th>
                                                <th class="center">Tanggal Pembayaran <span></span></th>
                                                <th class="center">Nilai</th>
                                                <th class="center">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($histories['from']) && count($histories['from'])>0) : $i=0; $total_price=0; ?>
                                            <?php foreach ($histories['from'] as $from) : ?>
                                                <tr id="row<?=$i?>">
                                                    <td><?=($i+1)?></td>
                                                    <td><?=$from['invoice']?></td>
                                                    <td><?=($from['dated'] != '') ? iso_date($from['dated']) : '' ?></td>
                                                    <td class="text-right"><?=myprice($from['total'])?></td>
                                                    <td><?=$from['note']?></td>
                                                </tr>
                                            <?php $i++; $total_price += $from['total']; endforeach; ?>
                                            <tr class="footer" id="tableFooter">
                                                <td colspan="3" class="text-right">TOTAL</td>
                                                <td class="text-right"><strong><?=myprice($total_price)?></strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="to">
                                <div class="genProduct">
                                    <table class="table table-striped table-bordered table-hover" id="tableHistory">
                                        <thead>
                                            <tr>
                                                <th class="center" style='width:1px;'>No</th>
                                                <th class="center">Invoice <span></span></th>
                                                <th class="center">Tanggal Pembayaran <span></span></th>
                                                <th class="center">Nilai</th>
                                                <th class="center">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($histories['payment']) && count($histories['payment'])>0) : $i=0; $total_price=0; ?>
                                            <?php foreach ($histories['payment'] as $payment) : ?>
                                                <tr id="row<?=$i?>">
                                                    <td><?=($i+1)?></td>
                                                    <td><?=$payment['invoice']?></td>
                                                    <td><?=($payment['dated'] != '') ? iso_date($payment['dated']) : '' ?></td>
                                                    <td class="text-right"><?=myprice($payment['total'])?></td>
                                                    <td><?=$payment['note']?></td>
                                                </tr>
                                            <?php $i++; $total_price += $payment['total']; endforeach; ?>
                                            <tr class="footer" id="tableFooter">
                                                <td colspan="3" class="text-right">TOTAL</td>
                                                <td class="text-right"><strong><?=myprice($total_price)?></strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="cashed">
                                <div class="genProduct">
                                    <table class="table table-striped table-bordered table-hover" id="tableHistory">
                                        <thead>
                                            <tr>
                                                <th class="center" style='width:1px;'>No</th>
                                                <th class="center">Tanggal Di Uangkan <span></span></th>
                                                <th class="center">Nilai</th>
                                                <th class="center">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($histories['cashed']) && count($histories['cashed'])>0) : $i=0; $total_price=0; ?>
                                            <?php foreach ($histories['cashed'] as $cashed) : ?>
                                                <tr id="row<?=$i?>">
                                                    <td><?=($i+1)?></td>
                                                    <td><?=($cashed['create_date'] != '') ? date_time($cashed['create_date'],'d N Y') : '' ?></td>
                                                    <td class="text-right"><?=myprice($cashed['giro_price'])?></td>
                                                    <td><?=$cashed['note']?></td>
                                                </tr>
                                            <?php $i++; $total_price += $cashed['giro_price']; endforeach; ?>
                                            <tr class="footer" id="tableFooter">
                                                <td colspan="2" class="text-right">TOTAL</td>
                                                <td class="text-right"><strong><?=myprice($total_price)?></strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        <?php else: ?>
                                            <tr class="footer" id="tableFooter">
                                                <td colspan="4" class="text-center"><button class="btn btn-primary" type="button" id="cashed-in">Uangkan</button></td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php endif; ?>
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
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Simpan</button>
                <a class="btn btn-warning" href="<?=$cancel_url?>"><i class="icon-ban-circle"></i> Batal</a>
            </div>
        </div>
    </form>
</section>
<?php if (isset($post['id_giro'])) : ?>
<script>
    $(function() {
        $("#cashed-in").click(function() {
            $(".display_message").html('');
            var conf = confirm('Apa Anda yakin untuk meng-uangkan giro ini?');
            if (conf) {
                var button = $(this),
                    button_html = $(this).html();
                $.ajax({
                    url:'<?=$cashed_url?>',
                    type:'post',
                    dataType:'json',
                    data:'giro_id=<?=$post['id_giro']?>',
                    beforeSend: function() {
                        button.attr('disabled');
                        button.html('Loading...');
                    },
                    complete: function() {
                        button.removeAttr('disabled');
                        button.html(button_html);
                    },
                    success: function(data) {
                        if (data['error']) {
                            $(".display_message").html(data['error']).focus();
                        }
                        if (data['success']) {
                            $(".display_message").html(data['success']).focus();
                        }
                    }
                });
            }
        });
    });
</script>
<?php endif; ?>
