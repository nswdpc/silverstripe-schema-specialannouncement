<?php

namespace NSWDPC\Schema\SpecialAnnouncement;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;

/**
 * ElementSpecialAnnouncement class
 * Each element simply references an {@link NSWDPC\Schema\SpecialAnnouncement\SpecialAnnouncement} managed in the model admin
 *
 * @author James
 * @property int $SpecialAnnouncementID
 * @method \NSWDPC\Schema\SpecialAnnouncement\SpecialAnnouncement SpecialAnnouncement()
 */
class ElementSpecialAnnouncement extends BaseElement
{
    private static string $table_name = 'ElementSpecialAnnouncement';

    private static string $singular_name = 'special announcement';

    private static string $plural_name = 'special announcements';

    private static string $icon = 'font-icon-chat';

    private static bool $inline_editable = true;


    private static string $title = 'Special Announcement';

    private static string $class_description = 'Select and display a single special announcement';

    private static array $has_one = [
        'SpecialAnnouncement' => SpecialAnnouncement::class,
    ];

    #[\Override]
    public function getSummary()
    {
        return "Special announcement";
    }

    #[\Override]
    public function getType()
    {
        return _t(self::class . '.BlockType', "Special announcement");
    }

    #[\Override]
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
