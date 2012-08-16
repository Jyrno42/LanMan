(function($)
{
	
	$.fn.maskPost = function(options, blanket)
	{
		var settings = $.extend({
			"selector": ".error",
			"errorLabel": "Error: ",
			"confirm": false,
			"beforeSubmit": function ()
			{
				if(settings.confirm !== false)
				{
					return confirm(settings.confirm);
				}
			},
			"setResult": function (form, selector, value)
			{
				if($(form).children(selector).length)
				{
					if($(form).children(selector).get(0).tagName == "INPUT" || $(form).children(selector).get(0).tagName == "SELECT")
					{
						$(form).children(selector).val(value);	
					}
					else
					{
						$(form).children(selector).html(value);
					}
				}
				else
				{
					if($(selector).get(0).tagName == "INPUT" || $(selector).get(0).tagName == "SELECT")
					{
						$(selector).val(value);	
					}
					else
					{
						$(selector).html(value);
					}
				}
			}
		}, options);

		return this.each(
			function() 
			{
				var form = this;
				
			    $(form).ajaxForm({ dataType: "json", beforeSubmit: settings.beforeSubmit, success:
			    	function(responseText) 
			    	{
			    		if(responseText.error)
			    		{
			    			settings.setResult(form, settings.selector, settings.errorLabel + responseText.error);
			    		}
			    		else if(responseText.result)
			    		{
			    			settings.setResult(form, settings.selector, responseText.result);
			    			location.reload();
			    		}
			    		else
			    		{
			    			settings.setResult(form, settings.selector, "Bad: " + responseText);
			    		}
			    	},
			    	error: function()
			    	{
			    		settings.setResult(form, settings.selector, "Something went terribly wrong!");
			    	}
			    });
			}
		);
	};
	
})(jQuery);