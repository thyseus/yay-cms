<?

class Sitecontent extends CActiveRecord
{
	public $language;
	public $password;
	public $password_repeat;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function isVisible() {
		if(Yii::app()->user->id == 1 || Yii::app()->user->name == 'admin')
			return true;
		if($this->visible == 3 || $this->visible == 4)
			return true;
		else if($this->visible == 2) {
			if(Yii::app()->user->hasState('yay_cms_password')) {
				$pwd = Yii::app()->user->getState('yay_cms_password');
				if ($pwd == $this->password)
					return true;
			}
			if($this->password === null && !Yii::app()->user->isGuest)
				return true;

		}	
		return false;
	}

	public function primaryKey() {
		return array('id', 'language');	
	}

	public function behaviors() {
		return array(
				'CSerializeBehavior' => array(
					'class' => 'application.modules.cms.components.CSerializeBehavior',
					'serialAttributes' => array(
						'metatags', 'images')));
	}

	public static function itemAlias($alias, $value = -10) {
		// - 10 is needed to avoid that a sitecontent has a value of NULL and
		// a array gets returned accidentally
		$visible = array(
				'0' => Cms::t('System Page'),
				'1' => Cms::t('Hidden'),
				'2' => Cms::t('Restricted'),
				'3' => Cms::t('Public'),
				'4' => Cms::t('Redirect'),
				);

		if($alias == 'visible' && $value === -10)
			return $visible;

		if($alias == 'visible' && $value !== null)
			return $visible[$value];
	}

	public function beforeValidate() {
		if(Cms::module()->enableHtmlPurifier) {
			$purifier = new CHtmlPurifier();
			$this->content = $purifier->purify($this->content);
		}	

		if($this->visible == 2)
			$this->scenario = 'restricted';

		if($this->redirect && $this->redirect == $this->id)
			$this->addError('redirect', Cms::t('Redirect to self is not allowed'));

		return parent::beforeValidate();	
	}

	public function redirectUrl() {
		if(is_numeric($this->redirect)) {
			$sc = Sitecontent::model()->find('id = :id', array(
						':id' => $this->redirect));
			return Yii::app()->controller->createAbsoluteUrl(
					Cms::module()->sitecontentViewRoute, array(
						'page' => $sc->title_url));; 
		}
		else
			return $this->redirect;
	}

	public function registerMetaTags() {
		if(isset(Yii::app()->controller->pageTitle))
			Yii::app()->controller->pageTitle = $this->title_browser;

		if($this->metatags)
			foreach($this->metatags as $tag => $content)  {
				if($content == '' && isset(Cms::module()->defaultMetaTags[$tag]))
					$content = Cms::module()->defaultMetaTags[$tag];					
				Yii::app()->clientScript->registerMetaTag($content, $tag);
			}
		return true;

	}

	public function tableName()
	{
		return 'sitecontent';
	}

	public static function nextAvailableId() {
		$sql = 'select id from sitecontent order by id DESC limit 1';
		$result = Yii::app()->db->createCommand($sql)->queryColumn();
		if(isset($result[0]))
			return (int) $result[0] + 1;	
		else
			return 1;
	}

	public function getImage($image = 0, $htmlOptions = array()) {
		if(isset($this->images[$image]))
			return CHtml::image(sprintf('%s/%s%s',
						Yii::app()->baseUrl ,
						Cms::module()->imagePath ,
						$this->images[$image]),
					$this->title, $htmlOptions);			
	}

	public function order($order)
	{
		$this->getDbCriteria()->mergeWith(array(
					'order'=>$order
					));
		return $this;
	}

	public static function listData() {
		$listData = array();

		$root = Sitecontent::model()->findAll('parent is NULL or parent = 0');
		foreach($root as $model) {
			$listData[$model->id] = $model->title;
			if($model->childs)
				foreach($model->childs as $child) {
					$listData[$child->id] = ' - '.$child->title;
					if($child->childs)
						foreach($child->childs as $subchild) {
							$listData[$subchild->id] = ' -- '.$subchild->title;
							if($subchild->childs)
								foreach($subchild->childs as $subsubchild)
									$listData[$subsubchild->id] = ' --- '.$subsubchild->title;
						}
				}
		}
		return $listData;
	}

	public function processImages ($images) {
		if(isset($images['image_new']) && $images['image_new']['name'] != '') {
			$image = $images['image_new'];

			if(!in_array($image['type'], Cms::module()->allowedImageMimeTypes))
				$this->addError('images', Cms::t(
							'File type {mime_type} is not allowed!', array(
								'{mime_type}' => $image['type'],
								)));
			else {
				// File is valid
				$images = $this->images;
				$images[] = $image['name'];
				$this->images = $images;	
				move_uploaded_file($image['tmp_name'],
						Cms::module()->imagePath . $image['name']);
			}
		}
	}

	public function getBreadcrumbs($route = '//cms/sitecontent/view') {
		$breadcrumbs = array();
	$breadcrumbs[$this->title] = $this->getAbsoluteUrl($route);

	return $breadcrumbs;
}

public function getAbsoluteUrl($route = '//cms/sitecontent/view') {
return Yii::app()->controller->createAbsoluteUrl($route, array(
			'page' => $this->title_url));
}

public function getParentTitles() {
	$titles = array($this->title_url);
	if($this->parent)
		$titles = array_merge($titles, $this->Parent->getParentTitles());

	unset ($titles[0]);
	return $titles;
}

public function getUrl($route = '//cms/sitecontent/view') {
if($this->visible == 4)
	return $this->redirectUrl();
	return $this->getAbsoluteUrl($route);
	}

public function getChildTitles() {
	$titles = array($this->title_url);
	if($this->childs)
		foreach($this->childs as $child)
			$titles = array_merge($titles, $child->getChildTitles());

	return $titles;
}

public function rules()
{
	return array(
			array('id, title, language', 'required'),
			array('id, title_url', 'CmsUniqueValidator'),
			array('parent, position, createtime, updatetime, visible', 'numerical', 'integerOnly'=>true),
			array('password, password_repeat', 'length', 'max' => 255, 'on' => 'restricted'),
			array('password, password_repeat', 'safe'),
			array('title, redirect, tags', 'length', 'max'=>255),
			array('tags', 'TagValidator'),
			array('images, metatags, redirect', 'safe'),
			array('content, title_url, title_browser', 'safe'),
			array('id, position, title, metatags, images, content, authorid, createtime, updatetime, language, tags', 'safe', 'on'=>'search'),
			);
}

public function relations()
{
	return array(
			// Parent is uppercase to differentiate it from the column
			'Parent' => array(self::BELONGS_TO, 'Sitecontent', 'parent'),
			'childs' => array(self::HAS_MANY, 'Sitecontent', 'parent', 'order' => 'position', 'condition' => 'language = \''.Yii::app()->language. '\''),
			);
}

public function attributeLabels()
{
	return array(
			'id' => '#',
			'parent' => Yii::t('CmsModule.cms', 'Parent'), 
			'position' => Yii::t('CmsModule.cms', 'Position'),
			'title' => Yii::t('CmsModule.cms', 'Title'),
			'title_url' => Yii::t('CmsModule.cms', 'URL title'),
			'title_browser' => Yii::t('CmsModule.cms', 'Browser title'),
			'content' => Yii::t('CmsModule.cms', 'Content'),
			'authorid' => Yii::t('CmsModule.cms', 'Authorid'),
			'createtime' => Yii::t('CmsModule.cms', 'Createtime'),
			'updatetime' => Yii::t('CmsModule.cms', 'Updatetime'),
			'language' => Yii::t('CmsModule.cms', 'Language'),
			'visible' => Cms::t('Visible'),
			'redirect' => Cms::t('Redirect'),
			'password' => Cms::t('Password'),
			'password_repeat' => Cms::t('Repeat Password'),
			'metatags' => Cms::t('Metatags'),
			'images' => Cms::t('Images'),
			'image_new' => Cms::t('Upload a new Image'),	
			);
}

public function limit($limit=10)
{
	$this->getDbCriteria()->mergeWith(array(
				'limit'=>$limit,
				));
	return $this;
}

public function group($group='id')
{
	$this->getDbCriteria()->mergeWith(array(
				'group'=>$group,
				));
	return $this;
}



public function search()
{
	$criteria=new CDbCriteria;

	if($this->id)
		$criteria->compare('id',$this->id);
	else {
		$criteria->compare('parent',$this->parent);
		$criteria->compare('position',$this->position);
		$criteria->compare('language',$this->language);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('title_url',$this->title_url,true);
		$criteria->compare('metatags',$this->metatags,true);
		$criteria->compare('images',$this->images,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('authorid',$this->authorid);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('updatetime',$this->updatetime);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('tags',$this->tags, true);
	}

	return new CActiveDataProvider('Sitecontent', array(
				'criteria'=>$criteria,
				'pagination' => array(
					'pageSize' => 50 
					)
				));
}
}	
