@CHARSET "UTF-8";

{foreach $sizes as $val}
{if $val@first}
.button, 
{/if}
.button_{$val}{if !$val@last},{/if} 
{foreachelse}
.button
{/foreach}
{
	border: 0px none transparent;
	display: inline-block;
	outline: none;
}

{foreach $sizes as $val}
{if $val@first}
.button:active, 
{/if}
.button_{$val}:active{if !$val@last},{/if} 
{foreachelse}
.button:active
{/foreach}
{
	position: relative;
	top: 1px;
}

{foreach $sizes as $val}
.button_{$val}
{
	height: {$val}px;
	width: {$val}px;
}
{/foreach}

/**
 * Specific buttons
 */
 
{foreach $types as $type}
.{$type}
{
	background: url(icons/{$defaultSize}/Button-{ucfirst($type)}-icon.png) no-repeat;
}
{foreach $sizes as $val}
.button_{$val}.{$type}
{
	background: url(icons/{$val}x{$val}/Button-{ucfirst($type)}-icon.png) no-repeat;
}
{/foreach}
{/foreach}
