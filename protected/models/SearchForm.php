<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SearchForm extends CFormModel
{
	public $subject;
	public $viewcount;
	public $language;
        public $min = 0;
        public $max = 0;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('subject, viewcount, language, min, max', 'required'),
                        array('viewcount, min, max', 'numerical', 'integerOnly'=>true, 'message'=>'{attribute} must be a whole number.', 'min'=>0, 'tooSmall'=>'{attribute} cannot be negative.'),
                        array('min','compare','compareAttribute'=>'max','operator'=>'<=', 'allowEmpty'=>false , 'message'=>'{attribute} must be lesser than "{compareValue}".'),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'subject'=>'Subject',
			'viewcount'=>'View Count',
                        'language' => 'Language', 
			'min'=>'Minimum Count',
			'max'=>'Maximum Count',
		);
	}
}