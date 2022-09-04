jQuery(document).ready(function($) {
	$("#csv-form").submit(function() {
		console.log($('input#csvmanager_file').prop('files'))
		var file_data = $('input#csvmanager_file').prop('files')[0]; 
		if (file_data['name'].toString().endsWith('.csv')) {
		var form_data = new FormData();                  
		form_data.append('file', file_data);
		form_data.append('action', 'csvmanager_get_results');
		form_data.append('_ajax_nonce', csvmanager_vars.nonce);
		console.log(form_data)
		$.ajax({
			url: csvmanager_vars.ajax_url,
			dataType: 'json',
			type: 'post',
			contentType: false,
			processData: false,
			cache: false,
			data: form_data,
			success: function(response) {
			$("#csvmanager_results").html(response);
					
			},
			error: function(data) {
				console.error(data)
			}
		});
		} else {
			alert("not a CSV-File (*.csv)")
		}
		return false;
	})
});