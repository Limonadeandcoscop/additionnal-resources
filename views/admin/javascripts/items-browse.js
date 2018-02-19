jQuery(document).ready(function($) {

	/*
	language[0] = code
	language[1] = name
	language[2] = base_language
	language[3] = count
	*/

	var html = l = '';
	html += '<div class="language-info">';
	html += '	<form action="" method="post">';
	enabledLanguages.forEach(function(language) {
		l += '<a ';
		if (currentLanguage == language[0])
			l += ' class="current" '
		l += 'rel="'+language[0]+'" href="#">' + language[1];
		if (baseLanguage == language[0])
			l += ' - base';
		l += ' ('+language[3]+')</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
	});
	html += 		l.slice(0,-13);
	html += '		<input type="hidden" name="choosen-language" class="choosen-language" value="' + currentLanguage + '">';
	html += '	</form>';
	html += '</div>';

	$('.items.browse #content').before(html);

	$('.language-info form > a').click(function() {
		var chooseLanguage = $(this).attr('rel');
		$('.choosen-language').val(chooseLanguage);
		$('.language-info form').submit();
		return false;
	});

});