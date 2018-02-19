jQuery(document).ready(function($) {

	/*
	language[0] = code
	language[1] = name
	language[2] = base_language
	language[3] = has_translation
	language[4] = translation_item_id
	language[5] = url
	*/

	var html = '';

	itemState.reverse().forEach(function(language) {

		if (language[2]) { // The base language

			current  = itemLanguageCode == language[0] ? 'current' : '';

			html += '<div class="language-info">';
			html += '<a href="'+ language[5] +'" class="' + current + '">';
			if (language[3])
				html += '<i class="fa fa-pencil"></i>&nbsp;';
			html += language[1] + ' - original</a>';
			html += '</div>';

		} else { // The translationss

			current  = itemLanguageCode == language[0] ? 'current' : '';

			html += '<div class="language-info">';
			html += '<a href="'+ language[5] +'" class="' + current + '">';
			if (language[3])
				html += '<i class="fa fa-pencil"></i>&nbsp;';
			html += language[1] + '</a>';
			html += '</div>';

		}

	});


	$('.items.show #content').before(html);
	$('.items.edit #content').before(html);
});