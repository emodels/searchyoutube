<?php $this->pageTitle=Yii::app()->name; ?>
<script type="text/javascript">
    var int_record_count = <?php echo $dataProvider->getTotalItemCount(); ?>;
    function SendVideoMessage(){
        if (int_record_count > 25){
            alert('Warning : Shortlisted Video count must be less than 25');
            return;
        }
        if (confirm('Are you sure you wanted to send Video Message to Authors ?')){
            $('#divProgress').html('Message sending in progress....');
            $("#dialogProgress").dialog("open").siblings(".ui-dialog-titlebar").remove();
            window.document.location.href = "<?php echo Yii::app()->baseUrl; ?>/email"; 
        }
    }
    function ExportVideos(){
        window.location.assign('<?php echo Yii::app()->baseUrl; ?>/site/export');
        $('#statusMsg').html('<ul class="flashes" style="list-style-type:none; margin: 0px; padding: 0px"><li><div class="flash-success">Videos listing exported successfully</div></li>\n');
        $('.flashes').animate({opacity: 1.0}, 3000).fadeOut("slow");
    }
    function DeleteVideos(){
        var atLeastOneIsChecked = $('input[name=\"cid[]\"]:checked').length > 0;

        if (!atLeastOneIsChecked)
        {
           alert('Please select at least one Video to delete');
        }
        else if (window.confirm('Are you sure you want to delete selected Videos ?'))
        {
            $.fn.yiiGridView.update('grid_videos', {
                    type:'POST',
                    url:$(this).attr('href'),
                    data: $('#grid-form').serialize(),
                    success:function(data) {
                        int_record_count = data; 
                        $.fn.yiiGridView.update('grid_videos');
                        $('#statusMsg').html('<ul class="flashes" style="list-style-type:none; margin: 0px; padding: 0px"><li><div class="flash-notice">Selected Videos Deleted</div></li>\n');
                        $('.flashes').animate({opacity: 1.0}, 3000).fadeOut("slow");
                    }
            });
        }
    }
    
    function PreviewVideo(strURL){
        $("#dialogVideo").dialog({
            autoOpen: true,
            modal: true,
            height: 390,
            width: 600,
            open: function(ev, ui){
                var videoAddress = strURL;

                $("#media-active").html(" ");
                $("#media-active").html('<object id="viewer" width="575" height="344"><param name="wmode" value="transparent" />' +
                '<param name="movie" value="' + videoAddress + '" /><param name="allowFullScreen" value="true" />' +
                '<embed id="embeddedPlayer" src="' + videoAddress + '" type="application/x-shockwave-flash" allowfullscreen="true" width="575" height="344" wmode="transparent"></embed></object>');
            }
        });        
    }
    
    function ToggleAdvancedSearch(){
        $('#advanced-search').slideToggle('slow', function() {
            if ($('#updown_arrow').attr('src') == 'images/down_arrow.png'){
                $('#updown_arrow').attr('src', 'images/up_arrow.png');
                $('#lnk_advanced_search').html('Hide Advanced Search Options');
            } else {
                $('#updown_arrow').attr('src', 'images/down_arrow.png');
                $('#lnk_advanced_search').html('View Advanced Search Options');
                $('#SearchForm_min').val('0');
                $('#SearchForm_max').val('0');
            }    
        });
    }
</script>
<div class="left"><img src="images/edit_find.png" /></div><h2 class="left" style="color: gray; padding: 12px 0 0 10px">Search "YouTube" videos went viral</h2>
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
                'validateOnChange' => true,
                'afterValidate'=>'js:function(form, data, hasError) 
                                 {   
                                    if(!hasError){
                                       $("#dialogProgress").dialog("open").siblings(".ui-dialog-titlebar").remove();
                                    }
                                    return true;
                                 }'           
            ),
        ));
        ?>  
        <div class="row">
            <div class="column" style="width: 60px; padding-top: 6px; font-size: 18px">Subject</div>
            <div class="column"><?php echo $form->textField($model, 'subject', array('class' => 'text_box')); ?><?php echo $form->error($model, 'subject'); ?></div>
            <div class="column" style="width: 100px; padding-top: 6px; padding-left: 10px; font-size: 18px">View Count</div>
            <div class="column"><?php echo $form->textField($model, 'viewcount', array('class' => 'text_box')); ?><?php echo $form->error($model, 'viewcount'); ?></div>
            <div class="column" style="width: 80px; padding-top: 6px; padding-left: 10px; font-size: 18px">Language</div>
            <div class="column"><?php echo $form->dropDownList($model, 'language', array('ar' => 'Arabic', 'zh' => 'Chinese', 'en' => 'Englih', 'fr' => 'French', 'de' => 'German', 'hi' => 'Hindi', 'es' => 'Spanish'), array('empty' => 'Select language', 'class' => 'text_box')); ?><?php echo $form->error($model, 'language'); ?></div>
            <div class="column"><div style="position: absolute; margin: -6px 0 0 18px"><?php echo CHtml::submitButton(' Search ', array('class' => 'button')); ?></div></div>
            <div class="clearfix"></div>
        </div>
        <?php if ($dataProvider->getTotalItemCount() > 0){ ?>
        <img id="updown_arrow" src="images/<?php echo $advance_search == 'none' ? 'down_arrow.png' : 'up_arrow.png' ?>" style="float: left; padding: 3px 5px 0 0" /><h4 style="color: gray; font-size: 12px"><a id="lnk_advanced_search" href="javascript:ToggleAdvancedSearch();"><?php echo $advance_search == 'none' ? 'View' : 'Hide'; ?> Advanced Search Options</a><i>&nbsp;&nbsp;( Filter results by range of view counts )</i></h4>
        <?php } ?>
        <div id="advanced-search" class="row box" style="display: <?php echo $advance_search; ?>">
            <div class="column" style="width: 180px; padding-top: 6px; padding-left: 10px; font-size: 18px">Minimum View Count</div>
            <div class="column"><?php echo $form->textField($model, 'min', array('class' => 'text_box')); ?><?php echo $form->error($model, 'min'); ?></div>
            <div class="column" style="width: 190px; padding-top: 6px; padding-left: 40px; font-size: 18px">Maximum View Count</div>
            <div class="column"><?php echo $form->textField($model, 'max', array('class' => 'text_box')); ?><?php echo $form->error($model, 'max'); ?></div>
            <div class="clearfix"></div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'grid-form',
        'htmlOptions' => array('autocomplete' => 'off'),
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => true
        ),
    ));
    ?>  
    <?php if ($dataProvider->getTotalItemCount() > 0){ ?>
    <input id="btnDelete" type="button" class="button" value="Delete Selected Videos" onclick="javascript:DeleteVideos();" />
    <input id="btnExport" type="button" class="button" style="margin-left: 15px; margin-right: 15px" value="Export Video Listing" onclick="javascript:ExportVideos();" />
    <input id="btnEmail" type="button" class="button" style="margin-left: 15px" value="Send Video Message" onclick="javascript:SendVideoMessage();" />    <?php } ?>
    <div style="padding-top: 20px">
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'grid_videos',
            'dataProvider' => $dataProvider,
            'ajaxUpdate' => true,
            'enablePagination' => true,
            'template'=>"{summary}{pager}<br>{items}{pager}",
            'columns' => array(
                array(
                    'name'=>'',             
                    'value'=>'CHtml::checkBox("cid[]",null,array("value"=>$data->id,"id"=>"cid_".$data->id))',
                    'type'=>'raw',
                    'htmlOptions'=>array('width'=>5),
                ),                
                array(
                    'name'=>'Title',
                    'value'=>'$data->title'
                ),
                array(
                    'name'=>'Author',
                    'type'=>'raw',
                    'value'=>'CHtml::link($data->author, "http://www.youtube.com/user/$data->author", array("target" => "_blank"))'
                ),
                array(
                    'name'=>'View Count',
                    'value'=>'$data->viewcount'
                ),
                array(
                    'name'=>'Video',
                    'type'=>'raw',
                    'value'=>'CHtml::link("View", "javascript:PreviewVideo(\'$data->embed_url\')", array("style" => "padding:0 5px 0 5px"))'
                ),
                array(
                    'name'=>'URL Link',
                    'type'=>'raw',
                    'value'=>'CHtml::link("Open", $data->link, array("target"=>"_blank", "style" => "padding-left:10px"))'
                ),
            )));
        ?>
    </div>
    <?php $this->endWidget(); ?>
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
    <?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'dialogProgress',
        'options'=>array(
            'title'=>'Search in Progress',
            'autoOpen'=>false,
            'modal'=>true,
            'resizable'=>false,
            'width'=>370,
            'height'=>100,
            'show'=>'puff',
            'hide'=>'puff'
        ),
    ));?>
    <div class="row" style="padding-top: 5px">
        <div class="column"><img src="images/ajax-loader.gif" /></div>
        <div class="column" style="font-size: 18px; padding-top: 14px" id="divProgress">Search in progress, please wait....</div>
        <div class="clearfix"></div>
    </div>    
    <?php $this->endWidget();?>
</div>
