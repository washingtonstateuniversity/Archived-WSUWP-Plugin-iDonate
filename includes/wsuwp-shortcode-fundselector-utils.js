// Does our namespace exist
window.wsuwpUtils = window.wsuwpUtils || {};

(function () {

    window.wsuwpUtils = {

		addListItem: function ( $list, name, designationId ) {
			var html = '<li class="list-group-item" data-designation_id="' + designationId + '">' + name + '<a href="#" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span><span class="sr-only">Remove Fund button</span></a></li>'; 
			
			if(!this.isDuplicateDesignation(designationId, $list))
			{
				$list.append(html);
			}
		},

		isDuplicateDesignation: function (designationId, $list)
		{
			var duplicate = false;
			$list.each(function()
			{
				if(designationId == jQuery(this).find("li").attr("data-designation_id"))
				{ 
					duplicate = true;
					return false;
				}
			});

			 return duplicate;
		},

		enableButton: function ( $button ) {
			$button.prop("disabled", false);
			$button.button("enable");
		},

		disableButton: function ( $button ) {
			$button.prop("disabled", true);
			$button.button("disable");
		},

		htmlDecode: function (value) {
			return jQuery("<textarea/>").html(value).text();
		}
	}
	
})();
