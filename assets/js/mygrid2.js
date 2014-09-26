function paging(grid_id){
    $('.pagination ul li a').click(function(){
        var param   = $.cookie('url');
        var url     = $(this).attr('href');
        if(url){
            var urls    = $(this).attr('href').split('/');
            var page    = urls.pop();
            page        = (page) ? page : 0;
            $.cookie("page",page);
            var next    = url+'?page='+page+param;
            load_data(grid_id,next);
        }
        return false;
    })
}
function the_grid(grid_id,url_grid,per_page,page){
    grid_id     = (grid_id)     ? '#'+grid_id   : '#myGrid';
    per_page    = (per_page)    ? per_page      : 10;
    page        = (page)        ? page          : 0;
    //$.cookie("per_page", per_page);
    //$.cookie("page", page);
    
	function my_grid(id){
		var s_val;
		var s_field;
		var s_url		= '';
		var kelas 		= $('#'+id +' span').attr('class');
		var sort_type 	= (kelas=='ui-icon ui-icon-carat-1-s') ? 'asc' : 'desc';
		var new_class 	= (kelas == 'ui-icon ui-icon-carat-1-s') ? 'ui-icon ui-icon-carat-1-n' : 'ui-icon ui-icon-carat-1-s';
		$(grid_id+' thead tr').find('span').each(function() {
			$(this).removeClass('sort ui-icon-carat-1-s ui-icon ui-icon-carat-1-n');
		})
		$('#'+id +' span').addClass(new_class);
		$.cookie("sort_field", id);
		$.cookie("sort_type", sort_type);
		$(grid_id+'').find('.cari').each(function() {
			s_val 		 = $(this).val();
			s_field		 = $(this).attr('id');
			s_url 		+= '&'+s_field+'='+s_val;
		})
        s_url           += '&perpage='+per_page+'&sort_field='+id+'&sort_type='+sort_type;
        $.cookie('url',s_url);
        load_data(grid_id,url_grid+'/'+page+'?page='+page+s_url);
	}
    
    //header table di klik
	$(grid_id+' thead tr th').click(function(){
		var id 			= $(this).attr('id');
		var is_sort		= $(this).attr('title');
		if(is_sort !='Sort') return false;
		my_grid(id);
		
	})
    
	//tambahin class sort utk kolom yg mau di sort
	$(grid_id+' thead tr').find('th').each(function() {
		if($(this).attr('title')=='Sort'){
			$(this).addClass('sort');
		}
	})
    
	//panggil grid pertama kali
	$(grid_id+' thead tr th.sort').each(function() {
		var sort_field 	= $.cookie("sort_field");
		var sort_type 	= $.cookie("sort_type");
		sort_field 		= (sort_field) ? sort_field : 'id';
		sort_type		= (sort_type) ? sort_type : 'desc';
		my_grid(sort_field,sort_type);
		return false;
	})
    
	//set value pencarian dari cookie
	$(grid_id+'').find('.cari').each(function() {
		$(this).val($.cookie($(this).attr('id')));
	})
	
	//pencarian
	$('.cari').keypress(function (e) {
		if (e.which == 13) {
			var val;
			var key;
			var kelas;
			var id;
			var sort_field 	 = $.cookie("sort_field");
			var sort_type 	 = $.cookie("sort_type");
			var url 		 = '';
			$(grid_id+'').find('.cari').each(function() {
				val 		 = $(this).val();
				key			 = $(this).attr('id');
				url 		+= '&'+key+'='+val;
				$.cookie(key,val);
			})
            url += '&perpage='+per_page+'&sort_field='+sort_field+'&sort_type='+sort_type;
            $.cookie('url',url);
            load_data(grid_id,url_grid+'?&page=0'+url);
		}
	});
}


function load_data(grid_id,url){
    $(grid_id+' tbody').html('<tr><td colspan="100" class="center"><br>'+loadingBtn+'</br></br></td></tr>');
    $.ajax({
        url		: url+'&'+Math.random(),
        success	: function(msg){
                    $(grid_id+' tbody').html(msg);
                    paging(grid_id);
                }
    });
}
function reload_grid(grid_id,url){
    grid_id     = (grid_id)     ? grid_id   : '#myGrid';
    $.removeCookie('sort_field');
    $.removeCookie('sort_ty pe');
    $.removeCookie('page');
    var field;
    $('#'+grid_id).find('.cari').each(function() {
        field = $(this).attr('id');
        $.removeCookie(field)
        $('#'+field).val('');
	})
    the_grid(grid_id,url);
    
}
