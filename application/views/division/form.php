<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <?php 
        if (isset($message)) { 
            echo $message;
        }
    ?>
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Content</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="division">Nama Divisi</label>
                            <input type="text" id="division" class="input-block-level" name="division" value="<?=(isset($post['division'])) ? $post['division'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="division_code">Kode Divisi</label>
                            <input type="text" id="division_code" class="input-block-level" name="division_code" value="<?=(isset($post['division_code'])) ? $post['division_code'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="division_pref">Prefix Divisi (* untuk penamaan kode produksi)</label>
                            <input type="text" id="division_pref" class="input-block-level" name="division_pref" value="<?=(isset($post['division_pref'])) ? $post['division_pref'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="division_pic">PIC Divisi</label>
                            <input type="text" id="division_pic" class="input-block-level" name="division_pic" value="<?=(isset($post['division_pic'])) ? $post['division_pic'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="division_address">Alamat Divisi</label>
                            <textarea id="division_address" rows="4" class="input-block-level" name="division_address" style="resize: none"><?=(isset($post['division_address'])) ? $post['division_address'] : ''?></textarea>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="division_note">Catatan</label>
                            <textarea id="division_note" rows="4" class="input-block-level" name="division_note" style="resize: none"><?=(isset($post['division_note'])) ? $post['division_note'] : ''?></textarea>
                        </div>
                    </div>
                </fieldset>
                <?php if (isset($post['id_division'])) : ?>
                <fieldset class="listProduct">
                    <legend>Stok Produk</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableProduct">
                            <thead>
                                <tr>
                                    <th class="center" style='width:1px;'>No</th>
                                    <th class="center" id="supplier">Produk <span></span></th>
                                    <th class="center" id="supplier_pic">Kode Produk <span></span></th>
                                    <th class="center" id="supplier_address">Jumlah <span></span></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($products)) : $i=0; ?>
                                <?php foreach ($products as $product) : ?>
                                    <tr id="row<?=$i?>">
                                        <td><?=($i+1)?></td>
                                        <td class="text-left"><?=$product['product_name']?></td>
                                        <td class="text-left"><?=$product['product_code']?></td>
                                        <td class="text-left"><?=$product['total_qty']?></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
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
