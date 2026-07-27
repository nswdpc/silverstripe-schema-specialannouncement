# Schema.org Special Announcement for Silverstripe

This module provides support for the [SpecialAnnouncement term within the schema.org specification](https://schema.org/SpecialAnnouncement) within the Silverstripe CMS.

## Features

+ Add an [elemental block](https://github.com/dnadesign/silverstripe-elemental) to a page, to select and display a single special announcement
+ Automatically renders JSON-LD supporting schema.org metadata
+ Provides a model admin with permission management to add,edit and delete multiple special announcements
+ Select a global special announcement for your site
+ Optionally link specific special announcements to one or more pages
+ Link support for internal and external URLs

## Supported schema.org/SpecialAnnouncement metadata

This module was originally introduced to support COVID-19 announcements. It can be used for any type of "special announcement".

```javascript
<script type="application/ld+json">
{
    "@context": "http:\/\/schema.org",
    "@type": "SpecialAnnouncement",
    "category": "https:\/\/www.wikidata.org\/wiki\/Q81068910",
    "name": "COVID-19 Update",
    "text": "Stay safe with the latest COVID Safe information",
    "image": "https:\/\/example.com\/assets\/Uploads\/stay-covid-safe.jpg",
    "datePosted": "2020-11-06T12:00:00+1100",
    "expires": "2021-01-14T12:00:00+1100",
    "url": "https:\/\/example.com"
}
</script>
```

You can also specify the following values as links to further content (external or internal)

```
diseasePreventionInfo
diseaseSpreadStatistics
gettingTestedInfo
governmentBenefitsInfo
newsUpdatesAndGuidelines
publicTransportClosuresInfo
quarantineGuidelines
schoolClosuresInfo
travelBans
````


## Installation

The recommended way of installing this module is via [composer](https://getcomposer.org/download/)

```
composer require nswdpc/silverstripe-schema-specialannouncement
```

Ensure you add the `'NSWDPC\Schema\SpecialAnnouncement\ElementSpecialAnnouncement'` as `allowed_element` in your project configuration.

## Templating

This module provides basic templates to implement a special announcement on your site. Your developer can provide a specific template to meet the requirements of your project

## Documentation

* [General information](./docs/en/001_index.md)
* [Global site-wide announcements](./docs/en/002_sitewide-announcements.md)

## Configuration

There is no configuration outside of the administration area, currently.


## License

[BSD-3-Clause](./LICENSE.md)

## Maintainers

+ PD Web Team

## TODO

+ spatialCoverage support

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
