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
                            <label for="id_division">Divisi</label>: <strong><?=$record['division']?></strong>
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
                    <legend>Barang Produksi yang telah di retur</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableRetur">
                            <thead>
                                <tr>
                                    <td>NO</td>
                                    <th class="center" id="product_name">Barang Produksi <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $total_qty=0; $i=0; foreach ($retur as $rtr) : ?>
                                <tr id="rowRetur<?=$i?>">
                                    <td><?=($i+1)?></td>
                                    <td class="production_name"><?=$rtr['item_category']?> (<?=$rtr['production_code']?>) (<?=$rtr['item_name']?>)</td>
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
                <fieldset class="listProduction">
                    <legend>Retur Barang Produksi</legend>
                    <div class="genProduction">
                        <table class="table table-striped table-bordered table-hover" id="tableProduction">
                            <thead>
                                <tr>
                                    <th class="center" id="product_name">Barang Produksi <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_production']) && count($post['post_production'])>0) : $i=0; ?>
                                <?php foreach ($post['post_production'] as $prod) : ?>
                                    <tr id="row<?=$i?>">
                                        <td class="productOption">
                                            <select name="post_production[<?=$i?>][id]" id="post_production_id_<?=$i?>" class="prodSelect" data-id="<?=$i?>">
                                            <?php foreach ($productions as $production) : ?>
                                                <option value="<?=$production['id_purchase_production']?>" <?=(isset($prod['id']) && $prod['id'] == $production['id_purchase_production']) ? 'selected="selected"' : '' ?>><?=$production['item_category']?> (<?=$production['production_code']?>) (<?=$production['item_name']?>)</option>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="qty"><input type="number" readonly="readonly" min="1" max="999" name="post_production[<?=$i?>][qty]" id="post_production_qty_<?=$i?>" value="<?=(isset($prod['qty'])) ? $prod['qty'] : '' ?>" class="input-text number_only"/></td>
                                        <td><button type="button" class="btn btn-danger delProduction" id="delProduction-<?=$i?>" onclick="delProduction('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="3"><button type="button" class="btn btn-success pull-right" id="addProduction">(+)</button></td>
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
    var row = <?=$production_count?>;
    $(function() {
        $("#addProduction").click(function() {
            addProduction();
        });
        $(".prodSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Barang Produksi"
            });
        });   
    });
    
    function addProduction() {
        html = '<tr id="row'+ row +'">';
        html += '<td class="productionOption">';
        html += '<select name="post_production['+ row +'][id]" id="post_production_id_'+ row +'" data-id="'+ row +'">';
        html += '<option value=""></option>';
        <?php foreach ($productions as $production) : ?>
            html += '<option value="<?=$production['id_purchase_production']?>"><?=$production['item_category']?> (<?=$production['production_code']?>) (<?=$production['item_code']?>)</option>';
        <?php endforeach; ?>
        html += '</select>';
        html += '</td>';
        html += '<td class="qty"><input readonly="readonly" type="number" min="1" max="999" name="post_production['+ row +'][qty]" id="post_production_qty_'+ row +'" value="1" class="input-text number_only"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduction" id="delProduction-'+ row +'" onclick="delProduction(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduction #tableFooter').before(html);
        $("#post_production_id_"+ row).select2({
            placeholder: "Pilih Barang Produksi"
        });
        $("#post_production_id_"+ row).select2("enable");
        number_only('.number_only');
        row++;
    }
    
    function delProduction(id) {
        $("#row"+id).remove();
        //row--;
    }
    
</script>

