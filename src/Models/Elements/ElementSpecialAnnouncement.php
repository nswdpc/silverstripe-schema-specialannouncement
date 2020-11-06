<?php

namespace NSWDPC\Schema\SpecialAnnouncement;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;

/**
 * ElementSpecialAnnouncement class
 * Each element simply references an {@link NSWDPC\Schema\SpecialAnnouncement\SpecialAnnouncement} managed in the model admin
 *
 * @author James
 */
class ElementSpecialAnnouncement extends BaseElement
{
    private static $table_name = 'ElementSpecialAnnouncement';

    private static $singular_name = 'special announcement';
    private static $plural_name = 'special announcements';

    private static $icon = 'font-icon-chat';

    private static $inline_editable = true;


    private static $title = 'Special Announcement';
    private static $description = 'Select and display a single special announcement';

    private static $has_one = [
        'SpecialAnnouncement' => SpecialAnnouncement::class,
    ];

    public function getSummary()
    {
        return "Special announcement";
    }

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', "Special announcement");
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $list = SpecialAnnouncement::get()->sort('IsGlobal DESC')->map('ID', 'TitleWithGlobalStatus');
        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'SpecialAnnouncementID',
                _t("NSWDPC\\Schema\\SpecialAnnouncement.SPECIAL_ANNOUNCEMENT_SELECT", "Select an announcement to display"),
                $list->toArray()
            )->setEmptyString('')
        );
        return $fields;
    }
}
