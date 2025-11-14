<?php

namespace NSWDPC\Schema\SpecialAnnouncement;

use SilverStripe\Admin\ModelAdmin;

/**
 * Provides a  model admin for special announcements
 */
class SpecialAnnouncementAdmin extends ModelAdmin
{
    private static string $url_segment = 'special-announcement';

    private static string $menu_title = 'Announcements';


    private static string $menu_icon_class = 'font-icon-chat';

    private static array $managed_models = [
        SpecialAnnouncement::class
    ];
}
