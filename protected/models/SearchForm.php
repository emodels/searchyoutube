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

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('subject, viewcount', 'required'),
                        array('viewcount', 'numerical', 'integerOnly'=>true, 'message'=>'View Count must be a whole number.', 'min'=>0, 'tooSmall'=>'View Count cannot be negative.')
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
			'viewcount'=>'View Count'
		);
	}
}