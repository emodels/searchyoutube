<?php
$this->pageTitle = Yii::app()->name . ' - Video Message Settings';
?>

<script type="text/javascript">
function PreviewVideo(strURL){
    $("#dialogVideo").dialog({
        autoOpen: true,
        modal: true,
        height: 390,
        width: 600,
        open: function(ev, ui){
            strURL = 'https://www.youtube.com/v/' + strURL.substring(strURL.lastIndexOf('=') + 1, strURL.length) + '?version=3&f=videos&app=youtube_gdata';
            var videoAddress = strURL;

            $("#media-active").html(" ");
            $("#media-active").html('<object id="viewer" width="575" height="344"><param name="wmode" value="transparent" />' +
            '<param name="movie" value="' + videoAddress + '" /><param name="allowFullScreen" value="true" />' +
            '<embed id="embeddedPlayer" src="' + videoAddress + '" type="application/x-shockwave-flash" allowfullscreen="true" width="575" height="344" wmode="transparent"></embed></object>');
        }
    });        
}
</script>

<div class="row">
    <div class="column"><img src="../images/email_open.png" /></div>
    <div class="column" style="padding-top: 25px"><h1>Video Message Settings</h1></div>
    <div class="clearfix"></div>
</div>
<div>
    <div class="form box" style="text-align: left; width: 95%; display: inline-block">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'settings-form',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 150px"><b>Video URL</b></div>
            <div class="column"><?php echo $form->textField($model, 'video', array('style' => 'width: 400px')); ?><?php echo $form->error($model, 'video'); ?></div>
            <div class="column"><a href="javascript: PreviewVideo('<?php echo $model->video; ?>')" target="_blank">View Video</a></div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="column" style="padding-top: 5px; width: 150px"><b>Message</b></div>
            <div class="column"><?php echo $form->textArea($model, 'message', array('rows' => '5', 'style' => 'width: 400px')); ?><?php echo $form->error($model, 'message'); ?></div>
            <div class="clearfix"></div>
        </div>
        <div class="row buttons">
            <div class="column" style="padding-left: 158px"><?php echo CHtml::submitButton('Update Settings', array('class' => 'button', 'style' => 'width:155px')); ?></div>
            <div class="clearfix"></div>
        </div>

        <?php $this->endWidget(); ?>
    </div><!-- form -->
</div>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'dialogVideo',
    'options'=>array(
        'title'=>'Preview YouTube Video',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>390,
        'height'=>600,
        'show'=>'puff',
        'hide'=>'puff'
    ),
));?>
<div id="media-active"></div>
<?php $this->endWidget();?>    

