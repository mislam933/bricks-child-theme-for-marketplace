(function($) {
	$( document ).ready(function() {
	    var is_login = $("body").hasClass("logged-in");
	    let exbp_ajaxurl = exbp_ajax_object.ajaxurl;
	    if (! is_login) {
	    	$('#exbp-download-btn').addClass('lrm-login');
	    	$('.pld-like-wrap.pld-common-wrap a').attr('class', '');
	    	$('.pld-like-wrap.pld-common-wrap a').addClass('lrm-login');
	    	$('.rmp-rating-widget__icons-list li').addClass('lrm-login');
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

		if ($.trim($(".pld-like-count-wrap.pld-count-wrap").html())==''){
			$(".pld-like-count-wrap.pld-count-wrap").html("0");
		}

        if ($("body").hasClass("single-bricks-templates")) {
            var link = $('#exbp-live-demo').attr('href');
            $(document.body).append('<div class="responsive-checker-wrapper responsive-checker after-open" style="display:none;"><div class="responsive-checker-inner responsive-checker-content live-demo" style="position:absolute;inset:50% 0 auto 50%;border:none;background:#fff;overflow:auto;border-radius:2.5rem;outline:0;padding:0;transform:translate(-50%,-50%);width:95%;max-width:96%;height:90vh"><div class="responsive-checker-preview-head"><h5 class="responsive-checker-preview-product-name">Live Demo</h5><div class="responsive-checker-preview-controller-group"><button class="responsive-checker-preview-control desktop activate"><i class="fa-solid fa-desktop"></i></button><button class="responsive-checker-preview-control tablet"><i class="fa-solid fa-tablet"></i></button><button class="responsive-checker-preview-control phone"><i class="fa-solid fa-mobile"></i></button></div><div class="responsive-checker-preview-live-link"><a class="responsive-checker-preview-live-btn" href="'+ link +'" target="_blank">Go Demo</a></div></div><div class="responsive-checker-preview-body"><iframe src="'+ link +'" frameborder="0" width="100%" height="100%"></iframe></div><div class="responsive-checker_modal__closer"></div></div></div>');
        }

        $(document).on("click","#exbp-live-demo",function(e){
            e.preventDefault();
            $(".responsive-checker").fadeIn("slow");
        });

        $(document).on('click', ".responsive-checker",function (event) {
		  if (!$(event.target).closest('.responsive-checker-inner').length) {
		    $(".responsive-checker").fadeOut("slow");
		  }
		});

        $(document).on("click",".responsive-checker-preview-control.desktop",function(e){
            $(".responsive-checker-inner").addClass("desktop");
            $(".responsive-checker-inner").removeClass("tablet");
            $(".responsive-checker-inner").removeClass("phone");
        });
        $(document).on("click",".responsive-checker-preview-control.tablet",function(e){
            $(".responsive-checker-inner").addClass("tablet");
            $(".responsive-checker-inner").removeClass("desktop");
            $(".responsive-checker-inner").removeClass("phone");
        });
        $(document).on("click",".responsive-checker-preview-control.phone",function(e){
            $(".responsive-checker-inner").addClass("phone");
            $(".responsive-checker-inner").removeClass("tablet");
            $(".responsive-checker-inner").removeClass("desktop");
        });
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