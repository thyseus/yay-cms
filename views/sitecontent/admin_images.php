<h2> <? echo Cms::t('Manage Images'); ?> </h2>

<?

if($images) {
	printf('<table><tr><td>%s</td><td>%s</td><td>%s</td></tr>',
			Cms::t('Filename'),
			Cms::t('Image'),
			Cms::t('Options'));

foreach($images as $image) {
	printf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
			$image,
			CHtml::image(
				Yii::app()->baseUrl.'/'.Cms::module()->imagePath.$image, $image, array(
					'style' => 'width: 200px;',
							'title' => $image)),

			CHtml::link(Cms::t('Remove image'), array(
						'//cms/sitecontent/unlinkImage', 'filename' => $image), array(
				'confirm' => Cms::t('Are you sure to remove this image permanently?')
				)));
				}

echo '</table>';
} else echo Cms::t('No images available yet');


echo CHtml::beginForm(array(
			'//cms/sitecontent/adminImages'), 'POST', array(
'enctype' => 'multipart/form-data'));
echo '<table>';
for($i = 0; $i < 20; $i++) {
	printf('<tr><td>%s</td></tr>', CHtml::fileField('image_'.$i));
}
echo '</table>';

echo CHtml::submitButton(Cms::t('Upload images'));
echo CHtml::endForm();

?>
