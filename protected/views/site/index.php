<?php $this->pageTitle=Yii::app()->name; ?>

<div class="left"><img src="../images/edit_find.png" /></div><h2 class="left" style="color: gray; padding: 12px 0 0 10px">Search english speaking "YouTube" videos went viral</h2>
<div class="clearfix"></div>
<div class="box">
    <!--Form-->
    <div class="form">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'search-form',
            'htmlOptions' => array('autocomplete' => 'off'),
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => true
            ),
        ));
        ?>  
        <div class="row">
            <div class="column" style="width: 80px; padding-top: 6px; font-size: 18px">Subject</div>
            <div class="column"><?php echo $form->textField($model, 'subject', array('class' => 'text_box')); ?><?php echo $form->error($model, 'subject'); ?></div>
            <div class="column" style="width: 110px; padding-top: 6px; padding-left: 50px; font-size: 18px">View Count</div>
            <div class="column"><?php echo $form->textField($model, 'viewcount', array('class' => 'text_box')); ?><?php echo $form->error($model, 'viewcount'); ?></div>
            <div class="column"><div style="position: absolute; margin: -6px 0 0 30px"><?php echo CHtml::submitButton(' Search ', array('class' => 'button')); ?></div></div>
            <div class="clearfix"></div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
    <div>
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => $dataProvider,
            'ajaxUpdate' => false,
            'enablePagination' => false,
            'columns' => array(
                array(
                    'name'=>'Title',
                    'value'=>'$data->title'
                ),
                array(
                    'name'=>'Author',
                    'value'=>'$data->author'
                ),
                array(
                    'name'=>'View Count',
                    'value'=>'$data->viewcount'
                ),
                array(
                    'name'=>'URL Link',
                    'type'=>'raw',
                    'value'=>'CHtml::link("View Video", $data->link, array("target"=>"_blank"))'
                ),
            )));
        ?>
    </div>    
</div>
