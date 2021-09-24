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
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Controllers\ContentController;
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
                    _t(__CLASS__ . ".TITLE", "Title")
                )->setDescription(
                    _t(
                        __CLASS__ . '.TITLE_DESCRIPTION',
                        'Provide a short, one-line, description of the announcement.'
                    )
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.TITLE_RIGHT',
                        'Schema.org entry:\'name\''
                    )
                ),
                CheckboxField::create(
                    'IsGlobal',
                    _t(__CLASS__ . ".TITLE", "Site-wide announcement")
                )->setDescription(
                    _t(
                        __CLASS__ . '.ISGLOBAL_DESCRIPTION',
                        'When checked, this announcement will be displayed on every page on the website, where supported'
                    )
                ),
                TextField::create(
                    'Category',
                    _t(__CLASS__ . ".CATEGORY", "Category")
                )->setValue( $this->config()->get('default_category_url') )
                ->setRightTitle(
                    _t(
                        __CLASS__ . '.DEFAULT_CATEGORY_INFO',
                        "Schema.org entry:'category'.\nBy default, the COVID-19 category URL is {url}",
                        [
                            'url' => $this->config()->get('default_category_url')
                        ]
                    )
                )->setDescription(
                    _t(
                        __CLASS__ . '.DEFAULT_CATEGORY_DESCRIPTION',
                        'Providing a category allows search engines to categorise this Special Announcement. Generally, your reference documentation will provide the relevant category value. Currently supported values are either a URL or text.'
                    )
                ),
                TextareaField::create(
                    'ShortDescription',
                    _t(__CLASS__ . ".SHORT_DESCRIPTION", "Short description")
                )
                ->setRows(3)
                ->setDescription(
                    _t(
                        __CLASS__ . '.SHORT_DESCRIPTION_DESCRIPTION',
                        'A text summary of the announcement. Usually restriced to a paragraph of text.'
                    )
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.TITLE_RIGHT',
                        'Schema.org entry:\'text\''
                    )
                ),
                HTMLEditorField::create(
                    'Content',
                    _t(__CLASS__ . ".CONTENT", "Content")
                )->setDescription(
                    _t(
                        __CLASS__ . '.CONTENT_DESCRIPTION',
                        'This content will be displayed when the announcement is display as part of the general page content. You may use all available HTML tags here'
                    )
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.CONTENT_RIGHT',
                        'This value is not used in Schema.org markup'
                    )
                ),
                DatetimeField::create(
                    'DatePosted',
                    _t(__CLASS__ . ".DATE_POSTED", "Date posted")
                )->setDescription(
                    _t(
                        __CLASS__ . '.DATE_POSTED_DESCRIPTION',
                        'This is the date that the announcement was published. It should be in the past.'
                    )
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.DATE_POSTED_RIGHT',
                        'Schema.org entry:\'datePosted\''
                    )
                ),
                DatetimeField::create(
                    'Expires',
                    _t(__CLASS__ . ".EXPIRES", "Expires")
                )->setDescription(
                    _t(
                        __CLASS__ . '.EXPIRES_DESCRIPTION',
                        'This is the date where this announcement will no longer be considered useful. Do not provide anything here  if you don\'t know when the announcement will expire.'
                    )
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.EXPIRES_RIGHT',
                        'Schema.org entry:\'expires\''
                    )
                ),
                UploadField::create(
                    'Image',
                    _t(__CLASS__ . ".Image", "Image")
                )->setDescription(
                    _t(
                        __CLASS__ . '.IMAGE_DESCRIPTION',
                        'An image related to the announcement. This may be displayed in search engine listings showing the announcement'
                    )
                )->setRightTitle(
                    _t(
                        __CLASS__ . '.IMAGE_RIGHT',
                        'Schema.org entry:\'image\''
                    )
                )
            ]
        );

        $has_ones = $this->hasOne();

        foreach ($has_ones as $relation => $class) {
            if ($class != Link::class) {
                continue;
            }

            $fields->removeByName($relation . "ID");

            $fields->addFieldToTab(
                'Root.URLS',
                LinkField::create(
                    $relation,
                    FormField::name_to_label($relation),
                    $this
                )->setDescription(
                    $this->getLinkDescription($relation)
                )
            );
        }

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create(
                'SpecialAnnouncementHelper',
                '<p class="message warning">'
                . _t(
                        __CLASS__ . ".DESCRIPTION_FOR_MORE_INFO",
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

    public function getLinkDescriptions() {
        return [
            'Link' => _t(__CLASS__ . '.Link_INFO', 'The URL where a person can find more information about the announcement'),

            'NewsUpdatesAndGuidelines' => _t(__CLASS__ . '.NewsUpdatesAndGuidelines_INFO', 'A page with news updates and guidelines in the context of announcement, if applicable to the announcement. This could be (but is not required to be) the main page containing SpecialAnnouncement markup on a site.'),

            'DiseasePreventionInfo' =>  _t(__CLASS__ . '.DiseasePreventionInfo_INFO', 'Information about disease prevention, if applicable to the announcement.'),

            'DiseaseSpreadStatistics' => _t(__CLASS__ . '.DiseasePreventionInfo_INFO', 'If applicable to the announcement, a link to the statistical information about the spread of a disease.'),

            'GettingTestedInfo' => _t(__CLASS__ . '.GettingTestedInfo_INFO', 'A link to information about getting tested (for a MedicalCondition) in the context of COVID-19, if applicable to the announcement.'),

            'GovernmentBenefitsInfo' => _t(__CLASS__ . '.GovernmentBenefitsInfo_INFO', 'A link to information about new government benefits in the context of COVID-19, if applicable to the announcement'),

            'PublicTransportClosuresInfo' => _t(__CLASS__ . '.PublicTransportClosuresInfo_INFO', 'A link to information about public transport closures in the context of COVID-19, if applicable to the announcement'),

            'QuarantineGuidelines' => _t(__CLASS__ . '.QuarantineGuidelines_INFO', 'A link to guidelines about quarantine rules in the context of COVID-19, if applicable to the announcement.'),

            'SchoolClosuresInfo' => _t(__CLASS__ . '.SchoolClosuresInfo_INFO', 'A link to information about school closures in the context of COVID-19, if applicable to the announcement.'),

            'TravelBans' => _t(__CLASS__ . '.TravelBans_INFO', 'A link to nformation about travel bans in the context of COVID-19, if applicable to the announcement.'),
        ];
    }

    public function getLinkDescription($key) {
        $descriptions = $this->getLinkDescriptions();
        if(isset($descriptions[ $key ])){
            return $descriptions[ $key ];
        }
        return "";
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
        if ($controller && ($controller instanceof ContentController)) {
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
