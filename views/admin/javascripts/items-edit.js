jQuery(document).ready(function($) {

	var html = '';
	html += '<div class="language-info">';
	html += 'Vous saisissez en : ' + inputLanguage;
	html += '</div>';

	$('.items #content').before(html);

});