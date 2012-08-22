{foreach $formItems as $k2 => $v2}

	<div style="margin-left: 20px" <label="" for="{$formKey}_args[]">
		{if is_array($v2[1])}{$v2[1][0]}{else}{$v2[1]}{/if}
		<div class="PaddedInput">
			{if $v2[0] == "text"}
				<input type="text" name="{$formKey}_args[]" />
				{else if $v2[0] == "select"}
				<select name="{$formKey}_args[]">
					{foreach $v2[1][1] as $optv => $optl}
						<option value="{$optv}">{$optl}</option>
					{/foreach}
				</select>
			{/if}
		</div>
	</div>

{/foreach}