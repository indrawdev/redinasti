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
                    <legend></legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="sales_invoice">No Faktur</label>
                            <input type="text" id="purchase_invoice" name="sales_invoice" value="<?=(isset($post['sales_invoice'])) ? $post['sales_invoice'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_store">Toko</label>
                            <select name="id_store" id="id_store">
                                <option value=""></option>
                                <?php foreach ($stores as $store) : ?>
                                    <?php if (isset($post['id_store']) && $post['id_store'] == $store['id_store']) : ?>
                                    <option value="<?=$store['id_store']?>" selected="selected"><?=$store['store']?></option>
                                    <?php else : ?>
                                    <option value="<?=$store['id_store']?>"><?=$store['store']?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php if (is_superadmin()) : ?>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_division">Divisi</label>
                            <!--<input type="hidden" id="id_division" name="id_division" class="bigdrop" value=""/>-->
                            <select name="id_division" id="id_division">
                                <option value=""></option>
                                <?php foreach ($divisions as $division) : ?>
                                    <?php if (isset($post['id_division']) && $post['id_division'] == $division['id_division']) : ?>
                                    <option value="<?=$division['id_division']?>" selected="selected"><?=$division['division']?></option>
                                    <?php else : ?>
                                    <option value="<?=$division['id_division']?>"><?=$division['division']?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="driver">Supir</label>
                            <input type="text" id="driver" name="driver" value="<?=(isset($post['driver'])) ? $post['driver'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="licence_plate">No. Plat</label>
                            <input type="text" id="licence_plate" name="licence_plate" value="<?=(isset($post['licence_plate'])) ? $post['licence_plate'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="sales_note">Catatan</label>
                            <textarea id="sales_note" rows="4" class="input-block-level" name="sales_note" style="resize: none"><?=(isset($post['sales_note'])) ? $post['sales_note'] : ''?></textarea>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="listProduction">
                    <legend>Barang</legend>
                    <div class="genProduction">
                        <table class="table table-striped table-bordered table-hover" id="tableProduction">
                            <thead>
                                <tr>
                                    <th class="center" id="production_name">Nama Barang <span></span></th>
                                    <th class="center" id="price_hpp">HPP <span></span></th>
                                    <th class="center" id="price">Harga Jual <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_production']) && count($post['post_production'])>0) : $i=0; ?>
                                <?php foreach ($post['post_production'] as $prod) : ?>
                                    <tr id="row-production<?=$i?>">
                                        <td class="productionOption">
                                            <select name="post_production[<?=$i?>][id_production]" id="post_production_id_<?=$i?>" class="productionSelect" data-id="<?=$i?>">
                                                <option value=""></option>
                                            <?php foreach ($productions as $production) : ?>
                                                <option value="<?=$production['id_production']?>" <?php echo (isset($prod['id_production']) && ($prod['id_production'] == $production['id_production'])) ? 'selected="selected"' : '';  ?>><?=$production['production_name'].' ('.$production['production_code'].')'?></option>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="price">Rp. <input type="text" name="post_production[<?=$i?>][production_hpp]" id="post_production_hpp_<?=$i?>" value="<?=(isset($prod['production_hpp'])) ? $prod['production_hpp'] : '' ?>" class="input-text" readonly="readonly"/></td>
                                        <td class="price">Rp. <input type="text" name="post_production[<?=$i?>][production_price]" id="post_production_price_<?=$i?>" value="<?=(isset($prod['production_price'])) ? $prod['production_price'] : '' ?>" class="input-text"/></td>
                                        <td><button type="button" class="btn btn-danger delProduction" id="delProduction-<?=$i?>" onclick="delProduction('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer tableFooter">
                                <td colspan="5"><button type="button" class="btn btn-success pull-right" id="addProduction">(+)</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="shipDate">Tanggal Kirim</label>
                            <div id="shipDate" class="input-prepend">
                                <span class="add-on curp">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                                <input data-format="yyyy-mm-dd" type="text" class="span11 pop-datepicker" name="shipping_date" value="<?=(isset($post['shipping_date'])) ? $post['shipping_date'] : date('Y-m-d')?>" placeholder="yyyy-mm-dd"/>
                            </div>
                        </div>
                    </div>
                </fieldset>
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
<script type="text/javascript">
    var row_production = '0';
    var division_id = $("#id_division").val();
    $(function() {
        $("#id_store").select2({
            placeholder: "Pilih Toko"
        });
        $("#id_division").select2({
            placeholder: "Pilih Divisi"
        }).on('change', function(e) {
            var division_id = e.val;
            $("table#tableProduction tbody tr").not('.tableFooter').html('');
        });
        
        $("#addProduction").click(function() {
            <?php if (is_superadmin()) : ?>
                var division_id = $("#id_division").val();
            <?php else : ?>
                var division_id = '<?=getSessionAdmin('admin_id_division')?>';
            <?php endif; ?>
            if (division_id) {
                $.ajax({
                    url:'<?=$getproduction_url?>',
                    type:'post',
                    dataType:'json',
                    data:{division_id:division_id},
                    success: function(data) {
                        if (data['error']) {
                            $(".display_message").html(data['error']);
                        }
                        if (data['return']) {
                            addProduction(row_production);
                            $("#post_production_id_"+row_production).append(data['return']);
                            row_production++;
                        }
                    }
                });
            } else {
                alert('mohon pilih divisi terlebih dahulu.');
            }
        });
        $(".productionSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Kode Produksi"
            }).on('change', function(e) {
                var production_id = e.val;
                if (production_id) {
                    $.ajax({
                        url:'<?=$productioninfo_url?>',
                        type:'post',
                        dataType:'json',
                        data:'production_id='+production_id+'&division_id='+division_id,
                        success: function(data) {
                            if (data['error']) {
                                $('.display_message').html(data['error']);
                            }
                            if (data['value']['price']) {
                                $("#post_production_hpp_"+this_id).val(data['value']['price']);
                            }
                        }
                    });
                }
            });
        }); 
    });
    function addProduction(row) {
        <?php if (is_superadmin()) : ?>
            var division_id = $("#id_division").val();
        <?php else : ?>
            var division_id = '<?=getSessionAdmin('admin_id_division')?>';
        <?php endif; ?>
        html = '<tr id="row-production'+ row +'">';
        html += '<td class="productionOption">';
        html += '<select name="post_production['+ row +'][id_production]" id="post_production_id_'+ row +'">';
        html += '<option value=""></option>';
        html += '</select>';
        html += '</td>';
        html += '<td class="price">Rp. <input type="text" name="post_production['+ row +'][production_hpp]" id="post_production_hpp_'+ row +'" value="" class="input-text" readonly="readonly"/></td>';
        html += '<td class="price">Rp. <input type="text" name="post_production['+ row +'][production_price]" id="post_production_price_'+ row +'" value="" class="input-text"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduction" id="delProduction-'+ row +'" onclick="delProduction(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduction .tableFooter').before(html);
        $("#post_production_id_"+ row).select2({
            placeholder: "Pilih Barang"
        }).on('change', function(e) {
            var production_id = e.val;
            if (production_id) {
                $.ajax({
                    url:'<?=$productioninfo_url?>',
                    type:'post',
                    dataType:'json',
                    data:'production_id='+production_id+'&division_id='+division_id,
                    success: function(data) {
                        if (data['error']) {
                            $('.display_message').html(data['error']);
                        }
                        if (data['value']['price_hpp']) {
                            $('#post_production_hpp_'+(row)).val(data['value']['price_hpp']);
                        }
                    }
                });
            }
        });
        $("#post_production_id_"+ row).select2("enable");
        //row++;
    }
    
    function delProduction(id) {
        $("#row-production"+id).remove();
    }
    
</script>
