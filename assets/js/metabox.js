jQuery( function( $ ) {
	$(document).on('click', '#wppm_connect_envato', function() {
		$.ajax({
			url: wppm.ajax_url,
			data: {
				action:     'wppm_connect_envato'
			},
			type: 'POST',
			datatype: 'json',
			success: function( rs ) {

			},
			error:function(){
				alert('There was an error when processing data, please try again !');
			}
		});
	});
});