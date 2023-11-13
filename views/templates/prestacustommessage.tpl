<section class="prestacustommessage"
{if !empty($prestacustommessage_bg_color)}
    style="
        background-color: {$prestacustommessage_bg_color};
        color: #000;
    "
{/if}
>
    {if !empty($prestacustommessage_heading)}
        <h2 class="h2">{$prestacustommessage_heading}</h2>
    {/if}

    {if !empty($prestacustommessage_desc)}
        <div class="prestacustommessage__content">{$prestacustommessage_desc}</div>
    {/if}
</section>
