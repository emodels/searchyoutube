<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
                $model = new SearchForm();
                $entry_collection = array();
                $advance_search = 'none';
                
                if (isset($_POST['SearchForm'])){
                    $model->attributes = $_POST['SearchForm'];
                    
                    $str_subject = $model->subject;
                    $str_subject = str_replace(' ', '%7C', $str_subject);
                    
                    if ($model->max > 0){
                        $str_URL = 'https://gdata.youtube.com/feeds/api/videos?q=' . $str_subject . '&orderby=viewCount&start-index=1&max-results=50&hl=' . $model->language . '&lr=' . $model->language . 
                                   '&v=2&fields=entry[yt:statistics/@viewCount>' . $model->min . '%20and%20yt:statistics/@viewCount<' . $model->max . ']';
                    
                        $advance_search = 'block';
                    }
                    else{
                        $str_URL = 'https://gdata.youtube.com/feeds/api/videos?q=' . $str_subject . '&orderby=viewCount&start-index=1&max-results=50&hl=' . $model->language . '&lr=' . $model->language . '&v=2&fields=entry[yt:statistics/@viewCount>' . $model->viewcount . ']';
                        $advance_search = 'none';
                    }
                    
                    $obj_result = file_get_contents($str_URL);
                    file_put_contents("result.xml", $obj_result);

                    $obj_XML = new SimpleXMLElement($obj_result);

                    foreach ($obj_XML->entry as $value) {
                        $entry = new Entry();
                        
                        $entry->title = (string)$value->title;
                        $entry->author = (string)$value->author->name;
                        
                        $yt = $value->children('http://gdata.youtube.com/schemas/2007');
                        $attrs = $yt->statistics->attributes();
                        $viewCount = (string)$attrs['viewCount']; 
      
                        $entry->viewcount = $viewCount;
                        $entry->link = (string)$value->link['href'];
                        $entry->embed_url = (string)$value->content['src'];
                        
                        $entry_collection[] = $entry;
                    }
                }
                
                $dataProvider = new CArrayDataProvider($entry_collection, array(
                'keyField'=>false,
                'pagination' => array(
                   'pageSize' => 10
                )    
                //'pagination'=>false
                ));                
                
		$this->render('index', array('model' => $model, 'dataProvider' => $dataProvider, 'advance_search' => $advance_search));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}