<div class="content-element__content<% if $Style %> $StyleVariant<% end_if %>">

	<% if $ShowTitle %>
        <h2 class="content-element__title">$Title</h2>
    <% end_if %>

    <% with $SpecialAnnouncement %>
        <% include Schema_SpecialAnnouncement %>
    <% end_with %>

</div>
