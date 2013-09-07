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
                /*
                 * Validate User Authantication
                 */      
                if(Yii::app()->user->isGuest)
                {
                    $this->redirect(array('site/login'));
                }
            
                $model = new SearchForm();
                $advance_search = 'none';
                
                $criteria=new CDbCriteria(array(
                        'condition' => 'user = ' . Yii::app()->user->id,
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
                                'condition' => 'user = ' . Yii::app()->user->id . ' AND viewcount > ' . $model->min . ' AND viewcount < ' . $model->max,
                                'order' => 'viewcount DESC'
                        ));
                        $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50, 'params' => array('min' => $model->min, 'max' => $model->max))));
                        $advance_search = 'block';
                    }
                    else{ //---------------------------Standard Data Capturing Process----------
                        
                        Entry::model()->deleteAll('user = :user', array(':user' => Yii::app()->user->id));
                        
                        $count = 1;
                        for ($index = 0; $index < 500; $index+=51) {
                            $str_URL = 'https://gdata.youtube.com/feeds/api/videos?q=' . $str_subject . '&orderby=viewCount&start-index=' . ($index == 0 ? '1' : $index) . '&max-results=50&hl=' . $model->language . '&lr=' . $model->language . '&v=2&fields=entry[yt:statistics/@viewCount>' . $model->viewcount . ']';
                            try
                            {
                                $obj_result = file_get_contents($str_URL);
                                
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
                                    $entry->embed_url = (string)$value->content['src'] == '' ? 'n/a' : $value->content['src'];
                                    $entry->user = Yii::app()->user->id;
                                    
                                    if ($entry->save()){
                                        
                                    }
                                    else{
                                        print_r($entry->getErrors()); 
                                    }
                                    
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
                                'condition' => 'user = ' . Yii::app()->user->id,
                                'order' => 'viewcount DESC'
                        ));
                        $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50)));
                    }
                }
                else{
                    if (Yii::app()->request->isAjaxRequest){
                        if (isset($_GET['min']) && isset($_GET['max'])){
                            $criteria=new CDbCriteria(array(                    
                                    'condition'=> 'user = ' . Yii::app()->user->id . ' AND viewcount > ' . $_GET['min'] . ' AND viewcount < ' . $_GET['max'],
                                    'order' => 'viewcount DESC'
                            ));
                            $dataProvider = new CActiveDataProvider('Entry', array('criteria'=>$criteria, 'pagination' => array('pageSize' => 50, 'params' => array('min' => $_GET['min'], 'max' => $_GET['max']))));
                        }
                        if (isset($_GET['ajax']) && $_GET['ajax'] == "grid_videos" && isset($_POST['cid'])) {
                            $entry_ids = implode(', ', $_POST['cid']);
                            Entry::model()->deleteAll('id IN (' . $entry_ids . ')');
                        }
                    } else {
                        Entry::model()->deleteAll('user = :user', array(':user' => Yii::app()->user->id));
                    }
                }
                
 		$this->render('index', array('model' => $model, 'dataProvider' => $dataProvider, 'advance_search' => $advance_search));
	}

        public function actionExport(){
            $model = Entry::model()->findAll('user = :user', array(':user' => Yii::app()->user->id));
            
            $contents = '<table>
            <tr>
                <td>Title</td>
                <td>Author</td>
                <td>View Count</td>
                <td>link</td>
            </tr>';
            foreach ($model as $entry) {
                $contents = $contents . 
                '<tr>
                    <td>' . $entry->title . '</td>
                    <td>' . $entry->author . '</td>
                    <td>' . $entry->viewcount . '</td>
                    <td>' . $entry->link . '</td>
                </tr>';
            }
            $contents = $contents . '</table>';        
            
            $filename = 'video_listing.xls';
            header( 'Content-type: application/ms-excel' );
            header( 'Content-Disposition: attachment; filename=' . $filename );

            echo $contents;            
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

        public function actionProfile(){
                $model = User::model()->findByPk(Yii::app()->user->id);
                
                if (isset($_POST['User'])) {
                    $model->attributes = $_POST['User'];
                    
                    if ($model->save()){
                        Yii::app()->user->setFlash('success', "User Profile Updated");
                        $this->redirect(Yii::app()->homeUrl);
                    }
                    else{
                        Yii::app()->user->setFlash('notice', $model->getErrors());
                    }
                }
                
		$this->render('profile',array('model'=>$model));
        }
        
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
            $model=new LoginForm;

            /**
             * Check for Remember me cookie and show user name
             */
            if (isset(Yii::app()->request->cookies['remember_me'])) {
               $model->username = Yii::app()->request->cookies['remember_me']->value;
               $model->rememberMe = 1;
            }
            
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
                    if($model->validate() && $model->login()){
                        
                        /**
                         * Configure remember me cookie
                         */
                        if ($model->rememberMe == 1) {
                            unset(Yii::app()->request->cookies['remember_me']);
                            $cookie = new CHttpCookie('remember_me', $model->username);
                            $cookie->expire = time()+60*60*24*180; 
                            Yii::app()->request->cookies['remember_me'] = $cookie;
                        }
                        else{
                            if (isset(Yii::app()->request->cookies['remember_me'])) {
                                unset(Yii::app()->request->cookies['remember_me']);
                            }
                        }
                        
                        $this->redirect(Yii::app()->user->returnUrl);
                    }        
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