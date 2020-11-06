<?php

namespace NSWDPC\Schema\SpecialAnnouncement;

use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Silverstripe\Control\Controller;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\CMS\Model\SiteTree;
use Page;

/**
 * Provides a SpecialAnnouncement model
 * See: https://schema.org/SpecialAnnouncement
 * See: https://developers.google.com/search/docs/data-types/special-announcements#add-structured-data
 * See: https://developers.google.com/search/docs/data-types/special-announcements#structured-data-type-definitions
 * @author James
 */
class SpecialAnnouncement extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var string
     * By default, this is the Covid19 category URL
     */
    private static $default_category_url = 'https://www.wikidata.org/wiki/Q81068910';

    private static $_mapping = [
        'Link' => 'url',
    ];

    private static $table_name = "SchemaSpecialAnnouncement";

    private static $db = [
        'Title' => 'Varchar(255)',
        'IsGlobal' => 'Boolean',
        'Category' => 'Varchar(255)',
        'ShortDescription' => 'Text',
        'Content' => 'HTMLText',
        'DatePosted' => 'Datetime',
        'Expires' => 'Datetime',
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Category' => 'Category',
        'IsGlobal.Nice' => 'Global?',
        'ShortDescription' => 'Description',
        'DatePosted.Nice' => 'Date posted',
        'Expires.Nice' => 'Expires'
    ];

    private static $has_one = [
        'Image' => Image::class,
        'Link' => Link::class,
        'NewsUpdatesAndGuidelines' => Link::class,
        'DiseasePreventionInfo' => Link::class,
        'DiseaseSpreadStatistics' => Link::class,
        'GettingTestedInfo' => Link::class,
        'GovernmentBenefitsInfo' => Link::class,
        'PublicTransportClosuresInfo' => Link::class,
        'QuarantineGuidelines' => Link::class,
        'SchoolClosuresInfo' => Link::class,
        'TravelBans' => Link::class,
    ];

    private static $many_many = [
        'Pages' => Page::class
    ];

    private static $owns = [
        'Image'
    ];

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->IsGlobal == 1) {
            DB::query("UPDATE `SchemaSpecialAnnouncement` SET IsGlobal = 0 WHERE ID <> '" . Convert::raw2sql($this->ID) . "'");
        }
    }

    public function TitleWithGlobalStatus()
    {
        return $this->Title . ($this->IsGlobal == 1 ? " (global)" : "");
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Title',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.TITLE", "Title")
                ),
                TextField::create(
                    'Category',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.CATEGORY", "Category")
                )->setValue( $this->config()->get('default_category_url') ),
                TextareaField::create(
                    'ShortDescription',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.SHORT_DESCRIPTION", "Short description")
                )->setRows(3),
                HTMLEditorField::create(
                    'Content',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.CONTENT", "Content")
                ),
                DatetimeField::create(
                    'DatePosted',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.DATE_POSTED", "Date posted")
                ),
                DatetimeField::create(
                    'Expires',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.EXPIRES", "Expires")
                ),
                UploadField::create(
                    'Image',
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.Image", "Image")
                )
            ]
        );

        $has_ones = $this->hasOne();

        foreach ($has_ones as $relation => $class) {
            if ($class != Link::class) {
                continue;
            }

            $fields->removeByName($relation . "ID");

            $key = isset(self::$_mapping[ $relation ]) ? self::$_mapping[ $relation ] : lcfirst($relation);

            $fields->addFieldToTab(
                'Root.Main',
                LinkField::create(
                    $relation,
                    _t("NSWDPC\\Schema\\SpecialAnnouncement.LINK_{$relation}", $key),
                    $this
                )
            );
        }

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create(
                'SpecialAnnouncementHelper',
                '<p class="message warning">'
                . _t(
                        "NSWDPC\\Schema\\SpecialAnnouncement.DESCRIPTION_FOR_MORE_INFO",
                        "Please read {url} before completing these fields",
                        [
                            "url" => "https://schema.org/SpecialAnnouncement"
                        ]
                )
                . "</p>"
            ),
            'Title'
        );

        return $fields;
    }

    public function SchemaJSON()
    {
        $schema = self::get_schema_json($this);
        $this->extend('updateSpecialAnnnouncementSchema', $schema);
        return json_encode($schema, JSON_PRETTY_PRINT);
    }

    public function getCategoryUrl() {
        $url = $this->Category;
        if(!$url) {
            $url = $this->config()->get('default_category_url');
        }
        return $url;
    }

    public static function get_schema_json(self $record)
    {
        $schema = [
            "@context" => "http://schema.org",
            "@type" => 'SpecialAnnouncement',
            "category" => $record->getCategoryUrl(),
            "name" => $record->Title,
            "text" => strip_tags($record->ShortDescription),
        ];

        $image = $record->Image();
        if ($image && $image->exists()) {
            $schema['image'] = $image->AbsoluteLink();
        }

        if ($record->DatePosted) {
            try {
                $dt = new \DateTime($record->DatePosted);
                $schema['datePosted'] = $dt->format(\DateTime::ISO8601);
            } catch (\Exception $e) {
            }
        }

        if ($record->Expires) {
            try {
                $dt = new \DateTime($record->Expires);
                $schema['expires'] = $dt->format(\DateTime::ISO8601);
            } catch (\Exception $e) {
            }
        }

        $has_ones = $record->hasOne();

        // add Links to provided information
        foreach ($has_ones as $relation => $class) {
            if ($class != Link::class) {
                continue;
            }

            $link = Link::get()->byId($record->getField("{$relation}ID"));
            if ($link) {
                // does the link have a mapping ?
                $key = isset(self::$_mapping[ $relation ]) ? self::$_mapping[ $relation ] : lcfirst($relation);
                $url = $link->getLinkURL();
                if ($url) {
                    $schema[ $key ] = $url;
                }
            }
        }

        return $schema;
    }

    public function canView($member = null)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        return Permission::check('SPECIALANNOUNCEMENT_EDIT');
    }

    public function canDelete($member = null)
    {
        return Permission::check('SPECIALANNOUNCEMENT_DELETE');
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('SPECIALANNOUNCEMENT_CREATE');
    }

    public function providePermissions()
    {
        return [
            'SPECIALANNOUNCEMENT_EDIT' => [
                'name' => _t(
                    __CLASS__ . '.EditPermissionLabel',
                    'Edit a special announcement'
                ),
                'category' => _t(
                    __CLASS__ . '.Category',
                    'Special announcements'
                ),
            ],
            'SPECIALANNOUNCEMENT_DELETE' => [
                'name' => _t(
                    __CLASS__ . '.DeletePermissionLabel',
                    'Delete a special announcement'
                ),
                'category' => _t(
                    __CLASS__ . '.Category',
                    'Special announcements'
                ),
            ],
            'SPECIALANNOUNCEMENT_CREATE' => [
                'name' => _t(
                    __CLASS__ . '.CreatePermissionLabel',
                    'Create a special announcement'
                ),
                'category' => _t(
                    __CLASS__ . '.Category',
                    'Special announcements'
                ),
            ]
        ];
    }

    /**
     * Render this special announcement into HTML
     */
    public function forTemplate()
    {
        return $this->renderWith(SpecialAnnouncement::class);
    }

    /**
     * Return the currently configured global announcement OR an announcement for the current page
     * @return DataList
     */
    public static function get_special_announcements()
    {
        $controller = Controller::curr();
        $page = null;

        $announcements = SpecialAnnouncement::get();
        if ($controller) {
            $page = $controller->data();
        }

        if ($page instanceof SiteTree) {
            $announcements = $announcements->filterAny([
                'Pages.ID' => $page->ID,
                'IsGlobal' => 1,
            ]);
        } else {
            $announcements = $announcements->filter('IsGlobal', 1);
        }

        $announcements->sort('IsGlobal DESC');

        return $announcements;
    }

    /**
     * Specify global template for current global or page specific SpecialAnnouncement
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'GlobalSpecialAnnouncements' => 'get_special_announcements'
        ];
    }
}
