<?
$url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('CmsAssets'));         
$file = $url . DIRECTORY_SEPARATOR . 'jquery.jshowoff.min.js';
Yii::app()->clientScript->registerScriptFile($file);
Yii::app()->clientScript->registerScript('advertising', "$('#adverts').jshowoff({links: false, controls:false});");

echo '<div id="adverts">';
foreach($adverts as $advert) {
	echo '<div class="advert">';
	echo $advert->content;
	echo '</div>';
}

echo '</div>';
?>
<div style="clear:both;"></div>
