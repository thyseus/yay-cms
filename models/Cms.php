<?

Yii::import('application.modules.cms.models.*');
Yii::import('application.modules.cms.controllers.*');

class Cms {
	/* set a flash message to display after the request is done */
	public static function setFlash($message, $delay = 5000) 
	{
		$_SESSION['cms_message'] = Cms::t($message);
		$_SESSION['cms_delay'] = $delay;
	}

	public static function hasFlash() 
	{
		return isset($_SESSION['cms_message']);
	}

	/* retrieve the flash message again */
	public static function getFlash() {
		if(Cms::hasFlash()) {
			$message = @$_SESSION['cms_message'];
			unset($_SESSION['cms_message']);
			return $message;
		}
	}

	/* A wrapper for the Yii::log function. If no category is given, we
	 * use the YumController as a fallback value.
	 * In addition to that, the message is being translated by Yum::t() */
	public static function log($message,
			$level = 'info',
			$category = 'application.modules.cms.controllers.SitecontentController') {
		return Yii::log(Cms::t($message), $level, $category);
	}

	public static function renderFlash()
	{
		if(Cms::hasFlash()) {
			echo '<div class="info">';
			echo Cms::getFlash();
			echo '</div>';
			Yii::app()->clientScript->registerScript('fade',"
					setTimeout(function() { $('.info').fadeOut('slow'); },
						{$_SESSION['cms_delay']});	
					"); 
		}
	}

	public static function module() {
		return Yii::app()->getModule('cms');

	}

	public static function register($file)
	{
		$path = Yii::app()->getAssetManager()->publish(
				Yii::getPathOfAlias('application.modules.cms.assets').'/'.$file);

		if(strpos($file, 'js') !== false)
			return Yii::app()->clientScript->registerScriptFile($path);
		else if(strpos($file, 'css') !== false)
			return Yii::app()->clientScript->registerCssFile($path);

		return $path;
	}

	public static function t($string, $params = array())
	{
		Yii::import('application.modules.cms.CmsModule');
		return Yii::t('CmsModule.cms', $string, $params);
	}

	public static function render($id = null, $lang = null) {
		echo Cms::get($id, $lang, true);
	}

	public static function get($id = null, $lang = null, $render = false) {
		if(Cms::module()->pageCache) {
			$sitecontent=Yii::app()->cache->get('sitecontent_'.$id.'_'.$lang);

			if($sitecontent===false) {
				$sitecontent = Cms::retrieveContent($id, $lang, $render);
				Yii::app()->cache->set('sitecontent_'.$id.'_'.$lang, $sitecontent);
				return $sitecontent;
			}
		} else return Cms::retrieveContent($id, $lang, $render);
	}

	public static function retrieveContent($id, $lang, $render) {
		if($lang === null)
			$lang = Yii::app()->language;

		$column = 'id';
		if(!is_numeric($id))
			$column = 'title_url';

		if($id) {
			$sitecontent = Sitecontent::model()->find(
					$column . ' = :id and language = :lang', array(
						':id' => $id,
						':lang' => $lang));

			// If the sitecontent is not available in the requested language,
			// try to fallback to the first natural found sitecontent in the db
			if(!$sitecontent)
				$sitecontent = Sitecontent::model()->find(
						$column .' = :id', array(
							':id' => $id));

			if(!$sitecontent && Cms::module()->strict404raising)
				throw new CHttpException(404);

			if ($render && $sitecontent && !$sitecontent->isVisible())
				throw new CHttpException(403);
		}

		if($sitecontent)
			return $sitecontent->content;	

	}

	public static function authDialog($label,
			$options = array(),
			$dialogOptions = array()) {
		return Yii::app()->controller->renderPartial(
				'application.modules.cms.views.sitecontent.authdialog', array(
					'options' => $options,
					'dialogOptions' => $dialogOptions,
					'label' => $label), true, true);
	}

	public static function getImage($image, $alt = '', $thumb = false) {
		if($thumb)
			return CHtml::image(
					Yii::app()->baseUrl.'/'.Cms::module()->imagePath . $image, $alt,
					array('width' => '150px'));
		else
			return CHtml::image(Cms::module()->imagePath . $image, $alt);

	}


	// for usage in CMenu Widget
	public static function getMenuPoints($id,
			$lang = null,
			$route = '//cms/sitecontent/view') {
		if(!$lang)
			$lang = Yii::app()->language;

	$column = 'title_url';
	if(is_numeric($id))
		$column = 'id';

	$sitecontent = Sitecontent::model()->find(
			$column.' = :id and language = :lang', array(
				':lang' => $lang,
				':id' => $id,
				));
	$items = array();
	if($sitecontent) {
		$childs = $sitecontent->childs;
		if($childs)  {
			foreach($sitecontent->childs as $child) {
				if($child->language == $lang)
					$items[] = array(
							'visible' => $child->isVisible(),
							'active' => isset($_GET['page']) && Cms::isMenuPointActive($child, $_GET['page']),
							'label' => $child->title,
							'url' => $child->getUrl($route),
							);
			}
		}
	}
	return $items;

}

public static function isMenuPointActive($sitecontent, $page) {
	if(!$sitecontent instanceof Sitecontent)
		return false;

	$titles = array($sitecontent->title_url);
	$titles = array_merge($titles, $sitecontent->getChildTitles());

	return in_array($page, $titles);
}


public static function renderMenuPoints($id, $lang = null) {
	if(!$lang)
		$lang = Yii::app()->language;

	if(is_numeric($id))
		$sitecontent = Sitecontent::model()->find(
				'id = :id and language = :lang', array(
					':lang' => $lang,
					':id' => $id,
					));
	$childs = $sitecontent->childs;
	if($childs)  {
		echo '<ul>';
		foreach($sitecontent->childs as $child) {
			printf('<li>%s</li>',
					CHtml::link($child->title, array(
							'/cms/sitecontent/view', 'page' => $child->title_url) ));
		}
		echo '</ul>';
	}
}
}
