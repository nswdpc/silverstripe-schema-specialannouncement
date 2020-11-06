<div class="text-center alert alert-specialannouncement">
    <p>
        <% if $Image %>$Image.ScaleHeight(32)<% end_if %>
        <strong>{$Title.XML}</strong>
        <span>$ShortDescription</span>
        <% if $Link %>
            <% with $Link %>
            <span><a href="$LinkURL"<% if $OpenInNewWindow %> target="_blank"<% end_if %>>$Title.XML</a></span>
            <% end_with %>
        <% end_if %>
    </p>

    <% if $SchemaJSON %>
    <script type="application/ld+json">
        $SchemaJSON.RAW
    </script>
    <% end_if %>

</div>
