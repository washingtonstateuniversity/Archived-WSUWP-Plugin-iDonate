jQuery(document).ready(function($) {
    var complete = '';
    var label = '';
    var text = '';
    
	if ( 'idonate_fund' === wpData.post_type ) {
		if ( 'archive' === wpData.post_status || 'searchable' === wpData.post_status ) {
			complete = ' selected="selected"';
			label = '<span id="post-status-display">Archived</span>';
			'archive' === wpData.post_status ? $("#post-status-display").text("Archived") : $("#post-status-display").text("Searchable");
        } 
        else {
			$(".misc-pub-section label").append("' + $label + '");;
        }

        $('select#post_status').append('<option value="archive" ' + complete + '="">Archive</option>');
        $('select#post_status').append('<option value="searchable" ' + complete + '="">Searchable</option>');
	}
});