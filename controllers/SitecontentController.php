<?

Yii::import('application.modules.cms.models.Sitecontent');
class SitecontentController extends Controller
{
	public $defaultAction='admin';
	public $pageTitle = '';
	private $_model;

	public function beforeAction($action)
	{
		$this->layout = Cms::module()->layout;
		return true;
	}

	public function actionSearch($search = null) 
	{
		if($search === null && isset($_POST['search']))
			$search = $_POST['search'];

		if($search) {
			$results = Sitecontent::model()->findAll(
					'visible > 0 and (
						title like :search 
						or title_browser like :search 
						or title_url like :search 
						or content like :search
						or tags like :search)', array(
							':search' => "%$search%"));

			$this->render(Cms::module()->searchResultsViewFile, array(
						'results' => $results,
						'search' => $search));
		} else throw new CHttpException(404);
	}

	public static function getContent($id) {
		if($model = Sitecontent::model()->findByPk($id)) {
			return $model->content;
		}
	}

	public function filters()
	{
		$filters = array('accessControl');
		
		if(Cms::module()->httpCache)
			$filters[] = array(
					'CHttpCacheFilter + index',
					'lastModified'=>Yii::app()->db->createCommand(
						"SELECT MAX(`updatetime`) FROM sitecontent")->queryScalar(),
					);
		return $filters;
	}

	public function actionAuth() {
		if(isset($_POST['password'])) {
			$password = md5($_POST['password']);
			Yii::app()->user->setState('yay_cms_password', $password);

			$valid = false;
			foreach(Sitecontent::model()->findAll('visible = 2') as $content) 
				if($content->password !== null) 
					if($password == $content->password)
						$valid = true;

			if($valid)
				Cms::setFlash('The password is correct');
			else
				Cms::setFlash('The password is incorrect');

			$this->redirect($_POST['returnUrl']);
		}
	}

	public function accessRules() {
		return array(
				array('allow',
					'actions'=>array('view', 'auth', 'search'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('updateValue', 'update', 'create', 'admin',
						'adminImages', 'delete', 'moveImage', 'deleteImage', 'unlinkImage'),
					'users'=>array('admin'),
					),
				array('deny',  // deny all other users
					'users'=>array('*'),
					),
				);

	}

	public function actionUpdateValue() {
		if(Yii::app()->user->isGuest)
			throw new CHttpException(403);

		if(!Yii::app()->request->isAjaxRequest)
			throw new CHttpException(403, Cms::t('This is not an ajax request'));

		if(!isset($_POST['column']) || !isset($_POST['value']))
			throw new CHttpException(400, Cms::t('Invalid request'));

		$model = Sitecontent::model()->find(
				'id = :id and language = :language', array(
					'id' => $_POST['id'],
					'language' => $_POST['language'],
					));
		if(!$model)
			throw new CHttpException(404);

		$column = $_POST['column'];

		if(isset($model->$column))
			$model->$column = $_POST['value'];

		return $model->save();
	}

	public function actionView($id, $lang = false, $ajax = false)
	{
		$model = $this->loadContent($id, $lang);

		// is this page a redirection?
		if($model->visible == 4 && $model->redirect !== null) 
			$this->redirect($model->redirectUrl());

		if($model->title_browser)
			$this->pageTitle = $model->title_browser;

		$model->registerMetaTags();

		if(!isset($this->breadcrumbs))
			$this->breadcrumbs = array($model->title);

		// update view counter
		$model->views++;
		$model->save(false, array('views'));

		if($ajax) {
			if(Cms::module()->pageCache
					&& $this->beginCache('yiicms_'.$model->id, array(
							'dependency'=>array(
								'class'=>'CDbCacheDependency',
								'sql'=>'SELECT MAX(updatetime) FROM Sitecontent',
								))))
			{
				$this->renderPartial(Cms::module()->sitecontentViewFile, array(
							'sitecontent' => $model,
							));

				$this->endCache();
			} else
				$this->renderPartial(Cms::module()->sitecontentViewFile, array(
							'sitecontent' => $model,
							));
		}
		else {
			if(Cms::module()->pageCache
					&& $this->beginCache('yiicms_'.$model->id, array(
							'dependency'=>array(
								'class'=>'CDbCacheDependency',
								'sql'=>'SELECT MAX(updatetime) FROM Sitecontent',
								))))
			{
				$this->render(Cms::module()->sitecontentViewFile, array(
							'sitecontent' => $model,
							));

			} else
				$this->render(Cms::module()->sitecontentViewFile, array(
							'sitecontent' => $model,
							));
		}
	}

	public function actionMoveImage($id, $language, $image, $direction) {
		$sitecontent = $this->loadContent($id, $language);
		if($sitecontent) {
			$images = $sitecontent->images;

			foreach($images as $key => $value) {
				if($image == $value) {
					// Image already at top ?
					if($direction == 'up' && $key == 0)
						break;
					// Image already at bottom ?
					if($direction == 'down' && $key == count($images) - 1)
						break;

					// swap	
					if($direction == 'up') {
						$tmp = $images[$key - 1];
						$images[$key - 1] = $image;
						$images[$key] = $tmp;
					}else if($direction == 'down') {
						$tmp = $images[$key + 1];
						$images[$key + 1] = $image;
						$images[$key] = $tmp;
					}
				}
			}
			$sitecontent->images = $images;

			$sitecontent->save(false, array('images'));

			$this->redirect( array(
						Cms::module()->sitecontentUpdateRoute,
						'id' => $sitecontent->title_url,
						'lang' => $sitecontent->language,
						));
		}
		throw new CHttpException(404);

	}

	public function checkPassword (&$model, $password, $password_repeat) {
		if(($model->visible == 2 || $model->isNewRecord)
				&& $password == $password_repeat) {
			if($model->password != $password)
				$model->password = md5($password);
			if($password == '' && $password_repeat == '')
				$model->password = null;
		}
		unset($_POST['Sitecontent']['password']);
		unset($_POST['Sitecontent']['password_repeat']);
	}

	public function actionCreate()
	{
		$this->layout = Cms::module()->adminLayout;
		$model = new Sitecontent;

		if($model->visible === null)
			$model->visible = 3;

		$this->performAjaxValidation($model);

		if(isset($_POST['Sitecontent']))
		{
			$model->attributes = $_POST['Sitecontent'];

			if(isset($_POST['Sitecontent']['password'])
					&& isset($_POST['Sitecontent']['password_repeat']))
				$this->checkPassword($model,
						$_POST['Sitecontent']['password'],
						$_POST['Sitecontent']['password_repeat']);

			$model->processImages($_FILES);

			$model->createtime = time();
			$model->updatetime = time();

			if(isset(Yii::app()->user->id))
				$model->authorid = Yii::app()->user->id;

			if($model->validate(null, false) && $model->save()) {
				Cms::setFlash('The page has been created');
				if(isset($_POST['yt0']))
					$this->redirect(array(
								'//cms/sitecontent/update',
								'id' => $model->id,
								'lang' => $model->language));
				else
					$this->redirect(array('admin'));
			}
		}

		if(isset($_GET['position']))
			$model->position = $_GET['position'];

		if(!isset($model->id) || $model->id === null)
			$model->id = Sitecontent::nextAvailableId();

		$this->render('create',array(
					'model'=>$model,
					));
	}

	public function actionUpdate($id, $lang = false)
	{
		$this->layout = Cms::module()->adminLayout;

		$model = $this->loadContent($id, $lang);

		$this->performAjaxValidation($model);

		if(isset($_POST['Sitecontent']))
		{
			$model->attributes=$_POST['Sitecontent'];
			if(isset($_POST['Sitecontent']['password'])
					&& isset($_POST['Sitecontent']['password_repeat']))
				$this->checkPassword($model,
						$_POST['Sitecontent']['password'],
						$_POST['Sitecontent']['password_repeat']);

			$model->processImages($_FILES);

			$model->updatetime = time();

			if($model->validate(null, false) && $model->save()) {
				Cms::setFlash('The page has been updated');
				if(isset($_POST['yt0']))
					$this->redirect(array(
								Cms::module()->sitecontentUpdateRoute,
								'id' => $model->id,
								'lang' => $model->language));
				else if(isset($_POST['yt1']))
					$this->redirect(array(
								Cms::module()->sitecontentViewRoute,
								'id' => $model->id,
								'lang' => $model->language));
				else
					$this->redirect(array('admin'));
			}
		}

		$this->render(Cms::module()->sitecontentUpdateFile,array(
					'model'=>$model,
					));
	}

	public function actionUnlinkImage($filename) {
		unlink(Yii::app()->basePath.'/../'.Cms::module()->imagePath.$filename);
		$this->redirect(array(Cms::module()->imageAdminRoute));
	}

	public function actionDeleteImage($model_id, $language, $image) {
		$model = Sitecontent::model()->find(
				'id = :model_id and language = :language', array(
					':model_id' => $model_id,
					':language' => $language));

		if($model && $image) {
			$images = $model->images;
			foreach($images as $i => $img)
				if($image == $img)
					unset($images[$i]);
			$model->images = $images;

			if($model->save(false, array('images')))
				Cms::setFlash('Image has been removed');
			else
				Cms::setFlash('Error while removing image');

			$this->redirect(array(
						Cms::module()->sitecontentUpdateRoute,
						'id' => $model_id,
						'lang' => $language));

		}	

	}

	public function actionDelete($id, $lang)
	{
		if(Yii::app()->request->isPostRequest)
			$this->loadContent($id, $lang)->delete();
		else
			throw new CHttpException(400,Yii::t('App','Invalid request. Please do not repeat this request again.'));
	}

	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Sitecontent');
		$this->render('index',array(
					'dataProvider'=>$dataProvider,
					));
	}

	public function actionAdmin($preview = false)
	{
		$this->layout = Cms::module()->adminLayout;

		$model=new Sitecontent('search');
		if(isset($_GET['Sitecontent']))
			$model->attributes=$_GET['Sitecontent'];

		$this->render('admin',array(
					'model'=>$model,
					'preview'=>$preview,
					));
	}

	public function actionAdminImages()
	{
		$this->layout = Cms::module()->adminLayout;

		if(isset($_FILES['image_0'])) {
			foreach($_FILES as $key => $image) {
				if($image['name']) {
					move_uploaded_file($image['tmp_name'],
							Cms::module()->imagePath . $image['name']);
				}
			}

			$this->redirect(array(Cms::module()->sitecontentAdminRoute));
		}

		$handle = opendir(Yii::app()->basePath . '/../' . Cms::module()->imagePath);

		while($image = readdir($handle))
			if($image != '.' && $image != '..') 
				$images[] = $image;

		$this->render('admin_images',array(
					'images'=>$images,
					));
	}

	public function loadContent($id, $lang)
	{
		if($this->_model===null)
		{
			$attr = is_numeric($id) ? 'id' : 'title_url';

			if(!$lang)
				$lang = Cms::module()->defaultLanguage;

			$this->_model = Sitecontent::model()->find(
					$attr.' = :id and language = :language',  array(
						':id' => $id,
						':language' => $lang,
						));

			// fallback to default language
			if($this->_model === null) {
				$this->_model = Sitecontent::model()->find(
						$attr.' = :id and language = :language',  array(
							':id' => $id,
							':language' => Cms::module()->defaultLanguage,
							));
				if($this->_model)
					Cms::setFlash(
							Cms::t(
								'{title} not found in requested language {language_requested}. Falling back to {language_default} version.', array(
									'{title}' => $this->_model->title,
									'{language_requested}' => $lang,
									'{language_default}' => Cms::module()->defaultLanguage,
									)));
			}	

			if($this->_model===null)
				throw new CHttpException(404,Cms::t(
							'The requested page does not exist'));
		} 

		if($this->_model) {
			if(Yii::app()->user->isGuest && !$this->_model->isVisible()) 
				throw new CHttpException(403, Cms::t(
							'This page is not available to the public'));
			else if(!Yii::app()->user->isGuest 
					&& !$this->_model->isVisible())
				throw new CHttpException(403, Cms::t(
							'Only authenticated members can view this resource'));
		}

		return $this->_model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sitecontent-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
