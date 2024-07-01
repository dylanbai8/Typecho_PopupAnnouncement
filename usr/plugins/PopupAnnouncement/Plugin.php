<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 活动倒计时，弹框公告。
 *
 * @package PopupAnnouncement
 * @version 3.0
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
        $announcement = new Typecho_Widget_Helper_Form_Element_Textarea('announcement', null, '清仓大甩卖！<br>清仓大甩卖！', _t('公告内容'), _t('请输入公告内容，插入&lt;br&gt;换行'));
        $form->addInput($announcement);

        $countdownEndTime = new Typecho_Widget_Helper_Form_Element_Text('countdownEndTime', null, '2050-10-01 01:01:01', _t('倒计时结束时间'), _t('请添加倒计时结束时间，例如 2050-10-01 01:01:01'));
        $form->addInput($countdownEndTime);

        $stayDuration = new Typecho_Widget_Helper_Form_Element_Text('stayDuration', null, '3', _t('停留时长 (秒)'), _t('请输入弹框在中央停留的时长，以秒为单位'));
        $form->addInput($stayDuration);

        $popupWidth = new Typecho_Widget_Helper_Form_Element_Text('popupWidth', null, '250px', _t('自定义弹框宽度'), _t('请输入弹框的宽度，例如 290px'));
        $form->addInput($popupWidth);

        $popupColor = new Typecho_Widget_Helper_Form_Element_Text('popupColor', null, '#d1d1d1d1', _t('自定义弹框颜色'), _t('请输入弹框的颜色，例如 #d1d1d1d1'));
        $form->addInput($popupColor);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function header()
    {
        $announcement = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->announcement;
        $countdownEndTime = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->countdownEndTime;
        $stayDuration = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->stayDuration;
        $popupWidth = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->popupWidth;
        $popupColor = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->popupColor;

        // 去除公告内容中的换行符
        $announcement = str_replace(array("\r", "\n", "'"), '', $announcement);

        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        echo '<style>
        @keyframes moveToCorner {
            0% {
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            100% {
                top: auto;
                left: auto;
                bottom: 10px;
                right: 10px;
                transform: none;
            }
        }
        #popup-announcement {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: ' . $popupColor . ';
            width: ' . $popupWidth . ';
            padding: 20px;
            border: 1px solid #ccc;
            z-index: 9999;
            display: none;
        }
        .fixed-bottom-right {
            top: auto !important;
            left: auto !important;
            bottom: 10px !important;
            right: 10px !important;
            transform: none !important;
        }
        </style>';
        echo <<<EOT
<script>
$(document).ready(function() {
    var announcement = '$announcement';
    var countdownEndTime = new Date('$countdownEndTime').getTime();
    var stayDuration = parseInt('$stayDuration') * 1000;

    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = countdownEndTime - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        if (distance < 0) {
            clearInterval(x);
            $('#popup-announcement-content').html('<p>活动已结束！</p>');
        } else {
            $('#popup-announcement-content').html('<p>' + announcement + '</p><p>距离结束: ' + days + '天 ' + hours + '时 ' + minutes + '分 ' + seconds + '秒</p>');
        }
    }, 1000);

    $('body').append('<div id="popup-announcement"><span id="popup-announcement-close" style="position: absolute; top: 5px; right: 5px; cursor: pointer;">&times;</span><div id="popup-announcement-content"></div></div>');

    if (localStorage.getItem('popupSeen') === null) {
        $('#popup-announcement').fadeIn(500).delay(stayDuration).queue(function(next) {
            $(this).css('animation', 'moveToCorner 1s forwards').removeClass('fixed-bottom-right');
            localStorage.setItem('popupSeen', 'true');
            next();
        });
    } else {
        $('#popup-announcement').fadeIn(500).addClass('fixed-bottom-right');
    }

    $('#popup-announcement-close').click(function() {
        $('#popup-announcement').hide();
    });
});
</script>
EOT;
    }
}
?>
