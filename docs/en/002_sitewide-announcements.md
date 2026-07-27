# Site-wide announcements

You can set an announcement to be site-wide and/or relevant to one or more pages.

To enable this feature, your main templates should include the relevant template:

```html
<% include GlobalSpecialAnnouncement %>
```

Your developer can override this template to meet project requirements.

When this is enabled, a special announcement with linked "Pages" will only display on those pages. A special announcement marked as "Site-wide" will display everywhere the GlobalSpecialAnnouncement template is included.
