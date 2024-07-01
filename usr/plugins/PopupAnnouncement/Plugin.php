<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * PopupAnnouncement 插件
 *
 * @package PopupAnnouncement
 * @version 2.0
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
        $announcement = new Typecho_Widget_Helper_Form_Element_Textarea('announcement', null, '', _t('公告内容'), _t('请输入公告内容，插入&lt;br&gt;换行'));
        $form->addInput($announcement);

        $countdownEndTime = new Typecho_Widget_Helper_Form_Element_Text('countdownEndTime', null, '', _t('倒计时结束时间'), _t('请输入倒计时结束时间，格式为 YYYY-MM-DD HH:MM:SS'));
        $form->addInput($countdownEndTime);

        $stayDuration = new Typecho_Widget_Helper_Form_Element_Text('stayDuration', null, '3000', _t('停留时长 (毫秒)'), _t('请输入弹框在中央停留的时长，以毫秒为单位 (1000毫秒=1秒)'));
        $form->addInput($stayDuration);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function header()
    {
        $announcement = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->announcement;
        $countdownEndTime = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->countdownEndTime;
        $stayDuration = Typecho_Widget::widget('Widget_Options')->plugin('PopupAnnouncement')->stayDuration;

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
            background: rgba(209, 209, 209, 0.9);
            width: 230px;
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
    var stayDuration = parseInt('$stayDuration');

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
