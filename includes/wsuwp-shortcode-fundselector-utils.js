// Does our namespace exist
window.wsuwpUtils = window.wsuwpUtils || {};

(function () {

    window.wsuwpUtils = {

		addListItem: function ( $list, name, designationId ) {
			var html = '<li class="list-group-item" data-designation_id="' + designationId + '">' + name + '<a href="#" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span><span class="sr-only">Remove Fund button</span></a></li>'; 
			$list.append(html);
		},

		enableButton: function ( $button ) {
			$button.prop("disabled", false);
			$button.button("enable");
		},

		htmlDecode: function (value) {
			return jQuery("<textarea/>").html(value).text();
		}
	}

})();
