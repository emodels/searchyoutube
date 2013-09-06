<?php
$this->pageTitle = Yii::app()->name . ' - Edit Profile';
?>

<div class="row">
        <div class="column"><img src="../images/gdm_login_photo.png" /></div>
        <div class="column" style="padding-top: 25px"><h1>Edit Profile</h1></div>
        <div class="clearfix"></div>
</div>
<div>
    <div class="form box" style="text-align: left; width: 95%; display: inline-block">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'profile-form',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
                ));
        ?>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 100px"><b>First Name</b></div>
            <div class="column"><?php echo $form->textField($model, 'first_name'); ?><?php echo $form->error($model, 'first_name'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 100px"><b>Last Name</b></div>
            <div class="column"><?php echo $form->textField($model, 'last_name'); ?><?php echo $form->error($model, 'last_name'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 100px"><b>Email</b></div>
            <div class="column"><?php echo $form->textField($model, 'email'); ?><?php echo $form->error($model, 'email'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 100px"><b>User Name</b></div>
            <div class="column"><?php echo $form->textField($model, 'username'); ?><?php echo $form->error($model, 'username'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 100px"><b>Password</b></div>
            <div class="column"><?php echo $form->passwordField($model, 'password'); ?><?php echo $form->error($model, 'password'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 100px"><b>Confirm Password</b></div>
            <div class="column"><?php echo $form->passwordField($model, 'conf_password'); ?><?php echo $form->error($model, 'conf_password'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row buttons">
            <div class="column" style="padding-left: 108px"><?php echo CHtml::submitButton('Login', array('class' => 'button')); ?></div>
            <div class="clearfix"></div>
        </div>

        <?php $this->endWidget(); ?>
    </div><!-- form -->
</div>
