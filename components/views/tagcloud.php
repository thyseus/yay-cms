<?
function getWeight($weight) {
	if($weight > 100)
		return 1;
	if($weight > 50)
		return 2;
	if($weight > 25)
		return 3;
	if($weight > 10)
		return 4;
	if($weight > 5)
		return 5;
	return 6;
}
?>
<div class="tag-cloud">
<? if($tags) { ?>
<? foreach($tags as $tag => $weight) { ?>
	<?
		if($tag)
			printf('%s, ',
					CHtml::link($tag, array(
					$linkUrl, 'search' => $tag), array(
				'class' => 'weight-'.getWeight($weight),
			))); ?>
				<? } ?>
<? } ?>
</div> 
<div style="clear: both;"></div>
