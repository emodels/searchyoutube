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
                $advance_search = 'none';
                
                $criteria=new CDbCriteria(array(                    
                        'order' => 'viewcount DESC'
                ));
                $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50)));
                
                if (isset($_POST['SearchForm'])){
                    $model->attributes = $_POST['SearchForm'];
                    
                    $str_subject = $model->subject;
                    $str_subject = str_replace(' ', '+', $str_subject);
                    $str_subject = '%22' . $str_subject . '%22';
                    
                    if ($model->max > 0){ //------------Advanced Search Filter------------------
                        $criteria=new CDbCriteria(array(                    
                                'condition' => 'viewcount > ' . $model->min . ' AND viewcount < ' . $model->max,
                                'order' => 'viewcount DESC'
                        ));
                        $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50, 'params' => array('min' => $model->min, 'max' => $model->max))));
                        $advance_search = 'block';
                    }
                    else{ //---------------------------Standard Data Capturing Process----------
                        
                        Entry::model()->deleteAll();
                        
                        $count = 1;
                        for ($index = 0; $index < 500; $index+=51) {
                            $str_URL = 'https://gdata.youtube.com/feeds/api/videos?q=' . $str_subject . '&orderby=viewCount&start-index=' . ($index == 0 ? '1' : $index) . '&max-results=50&hl=' . $model->language . '&lr=' . $model->language . '&v=2&fields=entry[yt:statistics/@viewCount>' . $model->viewcount . ']';
                            try
                            {
                                $obj_result = file_get_contents($str_URL);
                                
                                $obj_XML = new SimpleXMLElement($obj_result);

                                foreach ($obj_XML->entry as $value) {
                                    $entry = new Entry();

                                    $entry->id = $count;
                                    $entry->title = (string)$value->title;
                                    $entry->author = (string)$value->author->name;

                                    $yt = $value->children('http://gdata.youtube.com/schemas/2007');
                                    $attrs = $yt->statistics->attributes();
                                    $viewCount = (string)$attrs['viewCount']; 

                                    $entry->viewcount = $viewCount;
                                    $entry->link = (string)$value->link['href'];
                                    $entry->embed_url = (string)$value->content['src'];
                                    
                                    $entry->save();
                                    $count++;
                                }
                            } 
                            catch(Exception $e)
                            {
                                echo $e->getMessage(). '<br/>';
                            }
                        }
                        
                        $advance_search = 'none';
                        $criteria=new CDbCriteria(array(                    
                                'order' => 'viewcount DESC'
                        ));
                        $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50)));
                    }
                }
                else{
                    if (Yii::app()->request->isAjaxRequest){
                        if (isset($_GET['min']) && isset($_GET['max'])){
                            $criteria=new CDbCriteria(array(                    
                                    'condition'=> 'viewcount > ' . $_GET['min'] . ' AND viewcount < ' . $_GET['max'],
                                    'order' => 'viewcount DESC'
                            ));
                            $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50, 'params' => array('min' => $_GET['min'], 'max' => $_GET['max']))));
                        }
                    } else {
                        Entry::model()->deleteAll();
                    }
                }
                
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