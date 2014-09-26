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
                            <label for="purchase_invoice">No Faktur:</label> <strong><?=$record['purchase_invoice']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="id_supplier">Supplier:</label> <strong><?=$record['supplier']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid row-label">
                        <div class="span12">
                            <label for="total_price">Total Harga:</label> <strong>Rp. <?=$record['total_price']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-bottom: 30px;">
                        <div class="span12">
                            <label for="purchase_note">Catatan Pembelian:</label> <?=$record['purchase_note']?>
                        </div>
                    </div>
                    <?php if ($record['total_price'] > $total_paid) : ?>
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
                    <div class="row-fluid">
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
                    <?php endif; ?>
                </fieldset>
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
                                        <td class="center"><?= ($payment['payment_image'] !='' && file_exists(IMG_UPLOAD_DIR.'supplier_purchase/'.$payment['payment_image'])) ? '<img class="thumb-gal" src="'.IMG_UPLOAD_DIR_REL.'/supplier_purchase/sml_'.$payment['payment_image'].'"/>' : '---' ?></td>
                                        <td class="price text-right">Rp. <?=$payment['payment_total']?></td>
                                    </tr>
                                <?php $i++; $total_price += $payment['payment_total']; endforeach; ?>
                                <tr class="footer" id="tableFooter">
                                    <td colspan="5">&nbsp;</td>
                                    <td class="text-right">Rp. <strong><?=$total_price?></strong></td>
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
    <?php endif; ?>
</section>
