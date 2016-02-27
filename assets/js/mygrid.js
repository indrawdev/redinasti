function the_grid(grid_id, url_grid, per_page, order_id, order_direction, page) {
    grid_id = (grid_id) ? '#' + grid_id : '#myGrid';
    per_page = (per_page) ? per_page : 10;
    page = (page) ? page : 0;
    order_id = (order_id) ? order_id : 'id';
    order_direction = (order_direction) ? order_direction : 'desc';
    default_perpage = per_page;

    function my_grid(id) { // id = id field yg di sort
        var s_val;
        var s_field;
        var s_url = '';
        var kelas = $('#' + id + ' span').attr('class');
        var sort_type = (kelas == 'ui-icon ui-icon-carat-1-s') ? 'asc' : 'desc';
        var new_class = (kelas == 'ui-icon ui-icon-carat-1-s') ? 'ui-icon ui-icon-carat-1-n' : 'ui-icon ui-icon-carat-1-s';
        $(grid_id + ' thead tr').find('span').removeClass('sort ui-icon-carat-1-s ui-icon ui-icon-carat-1-n');
        $('#' + id + ' span').addClass(new_class);
        $(grid_id).find('.cari').each(function() {
            s_val = $(this).val();
            s_field = $(this).attr('id');
            s_url += '&' + s_field + '=' + s_val;
        })
        s_url += '&perpage=' + per_page + '&sort_field=' + id + '&sort_type=' + sort_type;
        load_data(url_grid + '/' + page + '?page=' + page + s_url);
    }

    //header table di klik
    $(grid_id + ' thead tr th').click(function() {
        var id = $(this).attr('id');
        var is_sort = $(this).attr('title');
        if (is_sort != 'Sort')
            return false;
        my_grid(id);

    })

    //tambahin class sort utk kolom yg mau di sort
    $(grid_id + ' thead tr').find('th').each(function() {
        if ($(this).attr('title') == 'Sort') {
            $(this).addClass('sort');
        }
    })

    //reset value pencarian on refresh
    $(grid_id).find('.cari').val('');

    //pencarian
    $('.cari').keypress(function(e) {
        if (e.which == 13) {
            var val;
            var key;
            var kelas;
            var id;
            var url = '';
            $(grid_id).find('.cari').each(function() {
                val = $(this).val();
                key = $(this).attr('id');
                url += '&' + key + '=' + val;
            })

            sort_field = kolom_sort();
            sort_type = kolom_type();
            url += '&perpage=' + per_page + '&sort_field=' + sort_field + '&sort_type=' + sort_type;
            load_data(url_grid + '?&page=0' + url);
        }
    });

    function kolom_sort() {
        var ret = '';
        var kelas = '';
        var hasil = '';
        $(grid_id + ' thead tr th').each(function() {
            if ($(this).attr('title') == 'Sort') {
                ret = ($(this).attr('id'));
                $(this).find('span').each(function() {
                    kelas = $(this).attr('class');
                    if (kelas == 'ui-icon ui-icon-carat-1-s' || kelas == 'ui-icon ui-icon-carat-1-n') {
                        hasil = ret;
                    }
                })
            }
        })
        return (hasil) ? hasil : order_id;
    }
    function kolom_type() {
        var ret = '';
        var kelas = '';
        var hasil = '';
        $(grid_id + ' thead tr th').each(function() {
            if ($(this).attr('title') == 'Sort') {
                ret = ($(this).attr('id'));
                $(this).find('span').each(function() {
                    kelas = $(this).attr('class');
                    if (kelas == 'ui-icon ui-icon-carat-1-s') {
                        hasil = 'desc';
                    }
                    else if (kelas == 'ui-icon ui-icon-carat-1-n') {
                        hasil = 'asc';
                    }
                })
            }
        })
        return (hasil) ? hasil : order_direction;
    }

    function paging() {
        $('.pagination ul li a').click(function() {
            var url = $(this).attr('href');
            var s_url = '';
            if (url) {
                $(grid_id).find('.cari').each(function() {
                    s_val = $(this).val();
                    s_field = $(this).attr('id');
                    s_url += '&' + s_field + '=' + s_val;
                })

                var urls = $(this).attr('href').split('/');
                var page = urls.pop();
                page = (page) ? page : 0;
                sort_field = kolom_sort();
                sort_type = kolom_type();
                var next = url + '?page=' + page + '&perpage=' + per_page + '&sort_field=' + sort_field + '&sort_type=' + sort_type + s_url;
                load_data(next);
            }
            return false;
        })
    }
    function load_data(url) {
        $(grid_id + ' tbody').html('<tr><td colspan="100" class="center"><br>' + loadingBtn + '</br></br></td></tr>');
        $.ajax({
            url: url + '&' + Math.random(),
            success: function(msg) {
                $(grid_id + ' tbody').html(msg);
                paging(grid_id, per_page);

                $('.hapus').click(function() {
                    var idx = $(this).attr('data-id');
                    var link = $(this).attr('data-url-rm');
                    if (confirm('Are You sure want to delete this record?')) {
                        $.ajax({
                            url: current_ctrl + link,
                            data: 'iddel=' + idx,
                            type: 'POST',
                            success: function(msg) {
                                my_grid(order_id);
                                alert('Delete Success');
                            }
                        })
                    }
                })
            }
        });
    }
    $(grid_id + ' .reload').click(function() {
        per_page = default_perpage;
        $(grid_id + ' .perpage').val(default_perpage);
        $(grid_id).find('.cari').val('');
        my_grid(order_id);
    })
    $(grid_id + ' .perpage').change(function() {
        per_page = $(this).val();
        my_grid(order_id);
    })

    my_grid(order_id);
    $(grid_id + ' .perpage').val(default_perpage);


}
