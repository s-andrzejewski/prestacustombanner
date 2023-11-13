<section class="presta-custom-message"
{if isset($prestacustommessage_text_color) || isset($prestacustommessage_img) }
    style="       
        {if isset($prestacustommessage_text_color)}
            color: {$prestacustommessage_text_color};
        {/if}
        
        {if isset($prestacustommessage_img)}
            background-image: url({$prestacustommessage_img});
        {/if}
    "
{/if}
>
    <div class="inner">
        {if isset($prestacustommessage_heading)}
            <h2 class="h2 prestacustommessage__heading">
                {$prestacustommessage_heading}
            </h2>
        {/if}

        {if isset($prestacustommessage_desc)}
            <div class="prestacustommessage__content">
                {$prestacustommessage_desc}
            </div>
        {/if}

        {if isset($prestacustommessage_btn_txt) && isset($prestacustommessage_btn_url)}
            <a href="{$prestacustommessage_btn_url}" target="_blank" class="presta-custom-message__button">
                {$prestacustommessage_btn_txt}
            </a>
        {/if}
    </div>
</section>
