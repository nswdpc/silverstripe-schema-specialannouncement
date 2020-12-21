<?php

namespace NSWDPC\Schema\SpecialAnnouncement;

use SilverStripe\Admin\ModelAdmin;

/**
 * Provides a  model admin for special announcements
 */
class SpecialAnnouncementAdmin extends ModelAdmin
{
    private static $url_segment = 'special-announcement';

    private static $menu_title = 'Announcements';


    private static $menu_icon_class = 'font-icon-chat';

    private static $managed_models = [
        SpecialAnnouncement::class
    ];
}
