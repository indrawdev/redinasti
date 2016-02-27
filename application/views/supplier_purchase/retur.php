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
        <div class="row-fluid suppptrans-detail">
            <div class="span8">
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="purchase_invoice">No Faktur</label>: <strong><?=$record['purchase_invoice']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="id_supplier">Supplier</label>: <strong><?=$record['supplier']?></strong>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="driver">Supir</label>: <?=$record['driver']?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="licence_plate">No. Plat</label>: <?=$record['licence_plate']?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="shipping_date">Tanggal Kirim</label>: <?=iso_date($record['shipping_date'],'-')?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="licence_plate">Total Harga</label>: Rp. <?=myprice($record['total_price'])?>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="purchase_note">Catatan</label>: <?=$record['purchase_note']?>
                        </div>
                    </div>
                </fieldset>
                <?php if ($retur) : ?>
                <fieldset class="listProduct">
                    <legend>Produk yang telah di retur</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableRetur">
                            <thead>
                                <tr>
                                    <td>NO</td>
                                    <th class="center" id="product_name">Produk <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $total_qty=0; $i=0; foreach ($retur as $rtr) : ?>
                                <tr id="rowRetur<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="product_name"><?=$rtr['product_name']?></td>
                                    <td class="product_qty text-right"><?=$rtr['total_qty']?></td>
                                </tr>
                            <?php $total_qty += $rtr['total_qty']; $i++; endforeach; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="2" class="text-right">TOTAL</td>
                                <td class="text-right"><strong><?=$total_qty?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <?php endif; ?>
                <fieldset class="listProduct">
                    <legend>Retur Produk</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableProduct">
                            <thead>
                                <tr>
                                    <th class="center" id="product_name">Produk <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_product']) && count($post['post_product'])>0) : $i=0; ?>
                                <?php foreach ($post['post_product'] as $prod) : ?>
                                    <tr id="row<?=$i?>">
                                        <td class="productOption">
                                            <select name="post_product[<?=$i?>][id_product]" id="post_product_id_<?=$i?>" class="prodSelect" data-id="<?=$i?>">
                                            <?php foreach ($products as $product) : ?>
                                                <?php if (isset($prod['id_product']) && $prod['id_product'] == $product['id_product']) : ?>
                                                    <option value="<?=$product['id_product']?>" selected="selected"><?=$product['product_name']?></option>
                                                <?php else : ?>
                                                    <option value="<?=$product['id_product']?>"><?=$product['product_name']?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="qty"><input type="number" min="1" max="999" name="post_product[<?=$i?>][qty]" id="post_product_qty_<?=$i?>" value="<?=(isset($prod['qty'])) ? $prod['qty'] : '' ?>" class="input-text number_only"/></td>
                                        <td><button type="button" class="btn btn-danger delProduct" id="delProduct-<?=$i?>" onclick="delProduct('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="3"><button type="button" class="btn btn-success pull-right" id="addProduct">(+)</button></td>
                            </tr>
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
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Submit</button>
                <a class="btn btn-warning" href="<?=$back_url?>"><i class="icon-ban-circle"></i> Kembali</a>
            </div>
        </div>
    </form>
</section>
<script type="text/javascript">
    var row = <?=$product_count?>;
    $(function() {
        $("#addProduct").click(function() {
            addProduct();
        });
        $(".prodSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Produk"
            });
        });   
    });
    
    function addProduct() {
        html = '<tr id="row'+ row +'">';
        html += '<td class="productOption">';
        html += '<select name="post_product['+ row +'][id_product]" id="post_product_id_'+ row +'" data-id="'+ row +'">';
        html += '<option value=""></option>';
        <?php foreach ($products as $product) : ?>
            html += '<option value="<?=$product['id_product']?>"><?=$product['product_name']?></option>';
        <?php endforeach; ?>
        html += '</select>';
        html += '</td>';
        html += '<td class="qty"><input type="number" min="1" max="999" name="post_product['+ row +'][qty]" id="post_product_qty_'+ row +'" value="" class="input-text number_only"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduct" id="delProduct-'+ row +'" onclick="delProduct(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduct #tableFooter').before(html);
        $("#post_product_id_"+ row).select2({
            placeholder: "Pilih Produk"
        });
        $("#post_product_id_"+ row).select2("enable");
        number_only('.number_only');
        row++;
    }
    
    function delProduct(id) {
        $("#row"+id).remove();
        //row--;
    }
    
</script>

