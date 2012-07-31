<?

class DefaultController extends Controller
{
	public function beforeAction($action)
	{
		$this->layout = Yii::app()->controller->module->layout;
		return true;
	}

	public function actionIndex()
	{
		$this->render('index');	
	}
	public function actionAdmin()
	{
		$this->render('admin');	
	}

}
