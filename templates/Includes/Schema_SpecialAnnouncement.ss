
<div class="announcement">

    <% if $Title %>
        <h3>{$Title.XML}</h3>
    <% end_if %>

    <% if $Image %>
        <div class="image">
            $Image
        </div>
    <% end_if %>

    <% if $ShortDescription %>
        <div class="text">
            $ShortDescription.XML
        </div>
    <% end_if %>

    <% if $Content %>
        <div class="content">
            $Content
        </div>
    <% end_if %>

    <% if $Link %>
        <% with $Link %>
        <div class="link">
            <a href="$LinkURL"<% if $OpenInNewWindow %> target="_blank"<% end_if %>>$Title.XML</a>
        </div>
        <% end_with %>
    <% end_if %>

    <% if $SchemaJSON %>
    <script type="application/ld+json">
        $SchemaJSON.RAW
    </script>
    <% end_if %>


</div>
