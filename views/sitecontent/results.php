<?
$this->breadcrumbs = array(Cms::t('Search results'));

if($results != array()) {
	printf('<h2>%s %s</h2>',
			count($results),
			Cms::t('Results:'));

	echo '<ul>';	
	foreach($results as $result) {
		printf('<li>%s</li>',
				CHtml::link($result->title, array(
						Cms::module()->sitecontentViewRoute,
						'id' => $result->title_url,
						'lang' => $result->language,
						'highlight' => $search)));
	}
	echo '</ul>';	
} else {
	echo Cms::t('No results found');
}
?>
