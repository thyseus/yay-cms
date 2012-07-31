<?
/**
 * Checks if given tags have the correct format
 **/
class TagValidator extends CValidator
{
	public $minTags = 0;
	public $maxTags = 7;

	public function validateAttribute($model, $attribute) {
		$tags = explode(',', $model->{$attribute});

		foreach($tags as $k => $v)
			$tags[$k] = trim($v);

		if(count($tags) < $this->minTags)
			$model->addError(
					$attribute, Cms::t(
						'Please choose at least {min} tags', array(
							'{min}' => $this->minTags)));

		if(count($tags) > $this->maxTags)
			$model->addError(
					$attribute, Cms::t(
						'Please do not choose more than {max} tags', array(
							'{max}' => $this->maxTags)));
	
		if(count($tags) !== count(array_unique($tags)))
			$model->addError($attribute, 
					Cms::t('Please choose every tag only once'));
	}
}
