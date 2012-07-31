<?
/**
 * Checks if a id is unique for the specified language
 **/
class CmsUniqueValidator extends CValidator
{
	public $allowEmpty = false;

	public function validateAttribute($model, $attribute) {
		if(Sitecontent::model()->find('id = :id and language = :language', array(
					'id' => $model->$attribute,
					'language' => $model->language)) !== null && $model->isNewRecord)
			$this->addError($model, $attribute, Cms::t(
						'The {attribute} {id} for language {language} is already in use', array(
							'{attribute}' => $attribute,
							'{id}' => $model->$attribute,
							'{language}' => $model->language)));
	}
}
