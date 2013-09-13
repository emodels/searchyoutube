<?php

class EmailController extends Controller
{
	public function actionIndex()
	{
            if (isset($_GET['recepent'])) {
                Yii::app()->session['recepent'] = $_GET['recepent'];
            }
            
            $client_id = "1026252441498.apps.googleusercontent.com"; //your client id
            $client_secret = "10sfw3e8-QNJQtdJ4O_JYZul"; //your client secret
            $redirect_uri = "http://" . $_SERVER['SERVER_NAME'] . "/email";
            $scope = "https://www.google.com/m8/feeds/ https://gdata.youtube.com"; //google scope to access
            $state = "profile"; //optional
            $access_type = "offline"; //optional - allows for retrieval of refresh_token for offline access
            
            if(isset($_REQUEST['code'])){
                $oauth2token_url = "https://accounts.google.com/o/oauth2/token";
                $clienttoken_post = array(
                    "code" => $_REQUEST['code'],
                    "client_id" => $client_id,
                    "client_secret" => $client_secret,
                    "redirect_uri" => $redirect_uri,
                    "grant_type" => "authorization_code"
                );

                $curl = curl_init($oauth2token_url);

                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttoken_post);
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $json_response = curl_exec($curl);
                //echo '<u>Errors list after Authantication</u><br>/';
                //echo curl_getinfo($curl) . '<br/>';
                //echo curl_errno($curl) . '<br/>';
                //echo curl_error($curl) . '<br/>';                

                curl_close($curl);

                $authObj = json_decode($json_response);

                if (isset($authObj->refresh_token)){
                    global $refreshToken;
                    $refreshToken = $authObj->refresh_token;
                }

                $accessToken = $authObj->access_token;
                
                //--------------------Read Contacts listing----------------------
                /*$headers = array(
                'Host: www.google.com',
                'Gdata-version: 3.0',
                'Content-type: application/atom+xml',
                'Authorization: OAuth '.$accessToken
                );
                
                $contactQuery = 'https://www.google.com/m8/feeds/contacts/default/full';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $contactQuery );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                
                $reulst = curl_exec($ch);                
                
                echo '<u>Errors list after read contact list</u><br>/';
                echo curl_getinfo($ch) . '<br/>';
                echo curl_errno($ch) . '<br/>';
                echo curl_error($ch) . '<br/>';                
                
                file_put_contents("contacts_list.xml", $reulst);
                curl_close($ch);
                */

                //-----------Add new contact----------------------------------------------
                /*$contactXML = '<?xml version="1.0" encoding="utf-8"?>
                <atom:entry xmlns:atom="http://www.w3.org/2005/Atom" xmlns:gd="http://schemas.google.com/g/2005">
                <atom:category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/contact/2008#contact"/>
                <gd:name>
                <gd:givenName>Jackie</gd:givenName>
                <gd:fullName>Jackie Frost</gd:fullName>
                <gd:familyName>Frost</gd:familyName>
                </gd:name>
                <gd:email rel="http://schemas.google.com/g/2005#home" address="jackfrost@gmail.com"/>
                <gd:phoneNumber rel="http://schemas.google.com/g/2005#home" primary="true">1111111111</gd:phoneNumber>
                </atom:entry>';

                $headers = array(
                'Host: www.google.com',
                'Gdata-version: 3.0',
                'Content-length: '.strlen($contactXML),
                'Content-type: application/atom+xml',
                'Authorization: OAuth '.$accessToken
                );

                $contactQuery = 'https://www.google.com/m8/feeds/contacts/default/full/';

                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_URL, $contactQuery );
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $contactXML);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                
                $reulst = curl_exec($ch);                
                echo '<u>Errors list after adding new contact</u><br>/';
                echo curl_getinfo($ch) . '<br/>';
                echo curl_errno($ch) . '<br/>';
                echo curl_error($ch) . '<br/>';                
                
                file_put_contents("contact_added.xml", $reulst);
                curl_close($ch);
                
                echo 'added';*/
                
                //------------Send Video message-----------
                $contactXML = '<?xml version="1.0" encoding="UTF-8"?>
                <entry xmlns="http://www.w3.org/2005/Atom"
                    xmlns:yt="http://gdata.youtube.com/schemas/2007">
                <id>utwP4W9RpDE</id>
                <summary>Hi Rob, Please put a link on my site at http://ymbrealty.com</summary>
                </entry>';

                $headers = array(
                'Host: gdata.youtube.com',
                'Content-type: application/atom+xml',
                'Authorization: OAuth '.$accessToken,
                'Gdata-version: 2',
                'X-GData-Key: key=AI39si7D5IeYMG48b-UZPxbzv5pXk8q7jvDAmSRX8Sq6g2DaFfr4I7dmcV9SAdxtiDCcTrmldIy48CIJ7gXz7CKbKAu4dEtX3A'    
                );

                $contactQuery = 'https://gdata.youtube.com/feeds/api/users/' . Yii::app()->session['recepent'] . '/inbox';

                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_URL, $contactQuery );
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $contactXML);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                
                $reulst = curl_exec($ch);                
                //echo '<u>Errors list after sending video message</u><br>/';
                //echo curl_getinfo($ch) . '<br/>';
                //echo curl_errno($ch) . '<br/>';
                //echo curl_error($ch) . '<br/>';                
                //var_dump($reulst);
                
                file_put_contents("video_message_sent.xml", $reulst);
                curl_close($ch);
                
                Yii::app()->user->setFlash('success', "Video message sent to recepent's email");
                $this->redirect(Yii::app()->homeUrl);
                //-----------------------------------------
            }
            else{
                $loginUrl = sprintf("https://accounts.google.com/o/oauth2/auth?scope=%s&state=%s&redirect_uri=%s&response_type=code&client_id=%s&access_type=%s", $scope, $state, $redirect_uri, $client_id, $access_type);
                $this->redirect($loginUrl);
            }
 
      }
}