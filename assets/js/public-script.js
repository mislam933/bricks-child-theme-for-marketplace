(function($) {
	$( document ).ready(function() {
	    var is_login = $("body").hasClass("logged-in");
	    let exbp_ajaxurl = exbp_ajax_object.ajaxurl;
	    if (! is_login) {
	    	$('#exbp-download-btn').addClass('xoo-el-login-tgr')
	    }else {
			$('#exbp-download-btn').on( 'click', function(event){
				event.preventDefault();
				$(this).append(' <i class="fas fa-refresh fa-spin" style="color: #fff !important"></i>');
				let post_id  = $(this).attr('post-id');

		        $.ajax({
		            url: exbp_ajaxurl,
		            type: 'POST',
		            data: { 
		            	action: 'exbp_download_teplate_file',
		            	post_id: post_id
		            },
		            // beforeSend: function () {
		            //     parent.find('#wbtm-form-builder .wbtm-loading').show();
		            // },
		            success: function (data) {

		                if (data !== '') {
		                    download_file(data);
		                    $(".fas.fa-refresh.fa-spin").remove();

		                } else {
							console.log('error');
							$(".fas.fa-refresh.fa-spin").remove();
							$('<p style="color: red;">Something wrong with your membership!</p>').insertAfter('#exbp-download-btn');
		                }
		                // Loading hide
		               // parent.find('.wbtm-form-builder .wbtm-loading').hide();
		            }
		        });
			});
	    }
	});


	function download_file(file_link){
	    $.ajax({
	        url: file_link,
	        method: 'GET',
	        xhrFields: {
	            responseType: 'blob'
	        },
	        success: function (data) {
	            var a = document.createElement('a');
	            var url = window.URL.createObjectURL(data);
	            a.href = url;
	            a.download = 'myfile.zip';
	            document.body.append(a);
	            a.click();
	            a.remove();
	            window.URL.revokeObjectURL(url);
	        }
	    });
	}


})(jQuery);