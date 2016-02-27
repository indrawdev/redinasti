<?php if (is_ajax_requested()) : ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    </div>
    <div class="modal-body">
        <div class="error-form"></div>
        <form method="post" class="well" id="form-itemCategory" onsubmit="return false;">
            <div class="row-fluid">
                <div class="span12">
                    <label for="item_category">Nama Barang</label>
                    <input type="text" id="item_category" class="input-block-level" name="item_category" value="<?=(isset($post['item_category'])) ? $post['item_category'] : ''?>"/>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <button class="btn btn-primary" type="button" id="saveCategory">Simpan</button>
    </div>
    <script type="text/javascript">
        $(function() {
            $("#saveCategory").click(function() {
                $.ajax({
                    url:'<?=$form_action?>',
                    type:'post',
                    dataType:'json',
                    data:$('#form-itemCategory').serialize(),
                    beforeSend: function() {
                        $(".error-form").html('');
                        $(this).attr('disabled','disabled');
                    },
                    success: function(data) {
                        if (data['error']) {
                            $(".error-form").html(data['error']);
                        }
                        if (data['success']) {
                            $('.display_message').html(data['success']);
                            $('.module-modal').modal('hide');
                        }
                        if (data['return']) {
                            console.log(data['return']);
                            var ret = data['return'],
                                html;
                            for ( var i = 0; i < ret.length; i++) {
                                var obj = ret[i];
                                html += '<option value="'+obj.id_item_category+'">'+obj.item_category+'</option>';
                            }
                            $('#id_item_category').html(html).select2({
                                placeholder: "Pilih Kategori"
                            });
                        }
                        $(this).removeAttr('disabled');
                    }
                });
            });
        });
    </script>
<?php else : ?>
    <section class="well animated fadeInUp">
        <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
        <hr/>
        <?php 
            if (isset($message)) { 
                echo $message;
            }
        ?>
        <form action="<?= $form_action ?>" method="post" class="well">
            <div class="row-fluid">
                <div class="span8">
                    <fieldset>
                        <legend>Content</legend>
                        <div class="row-fluid">
                            <div class="span12">
                                <label for="item_category">Nama Barang</label>
                                <input type="text" id="item_category" class="input-block-level" name="item_category" value="<?=(isset($post['item_category'])) ? $post['item_category'] : ''?>"/>
                            </div>
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
                    <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Simpan</button>
                    <a class="btn btn-warning" href="<?=$cancel_url?>"><i class="icon-ban-circle"></i> Batal</a>
                </div>
            </div>
        </form>
    </section>
<?php endif; ?>