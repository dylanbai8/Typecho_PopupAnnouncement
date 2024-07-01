<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * PopupAnnouncement 插件
 *
 * @package PopupAnnouncement
 * @version 1.0
 * @author chatgpt
 * @link https://github.com/dylanbai8
 */

class PopupAnnouncement_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('PopupAnnouncement_Plugin', 'header');
    }

    public static function deactivate()
    {
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $announcement = new Typecho_Widget_Helper_Form_Element_Textarea('announcement', null, '', _t('公告内容'), _t('请输入公告内容'));
        $form->addInput($announcement);

        $countdownEndTime = new Typecho_Widget_Helper_Form_Element_Text('countdownEndTime', null, '', _t('倒计时结束时间'), _t('请输入倒计时结束时间，格式为 YYYY-MM-DD HH:MM:SS'));
        $form->addInput($countdownEndTime);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function header()
    {
        $announcement = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->announcement;
        $countdownEndTime = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->countdownEndTime;

        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        echo <<<EOT
<script>
$(document).ready(function() {
    var announcement = '$announcement';
    var countdownEndTime = new Date('$countdownEndTime').getTime();

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = countdownEndTime - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        if (distance < 0) {
            clearInterval(x);
            $('#popup-announcement-content').html('<p>活动已结束</p>');
        } else {
            $('#popup-announcement-content').html('<p>' + announcement + '</p><p>距离结束: ' + days + '天 ' + hours + '小时 ' + minutes + '分钟 ' + seconds + '秒</p>');
        }
    }, 1000);

    $('body').append('<div id="popup-announcement" style="position: fixed; bottom: 10px; right: 10px; background: rgba(209, 209, 209, 0.9); width: 230px; padding: 20px; border: 1px solid #ccc; z-index: 9999;"><span id="popup-announcement-close" style="position: absolute; top: 5px; right: 5px; cursor: pointer;">&times;</span><div id="popup-announcement-content"></div></div>');
    
    $('#popup-announcement-close').click(function() {
        $('#popup-announcement').hide();
    });
});
</script>
EOT;
    }
}
?>
