<?
echo CHtml::beginForm(array('//cms/sitecontent/search'));
echo CHtml::textField('search', '', array('size' => 10));
echo CHtml::submitButton('Suche');
echo CHtml::endForm();
?>
