<div class="entries">
<% control Entries %>
    <div class="entry $EvenOdd $FirstLast">
        <h5 class="title"><a href="$Link">$Title</a></h5>
        <h5 class="date" title="$Date.Ago">$Date.Nice</h5>
        <span class="text">$Content.Summary(500)<br/>
        <a href="$Link">Read more</a></span>
    </div>
<% end_control %>
</div>
