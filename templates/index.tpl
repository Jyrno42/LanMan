{extends file="page.tpl"}

{block name=PageContents}

<p>Nonii.<p> <p>See on siis Alagruppide ning Bracketite mootor erinevatele üritustele, hetkel on arendus alles käimas ja kogu funksionaalsus pole veel valmis. Loome peagi paar turniiri, et testida süsteemi tööd.</p>
<p>League of Legends kohapealt niipalju, et süsteem toetab ka uut RIOTi ja ESL koostöös loodud Tournament Codet, mille abil saavad kaks tiimi omavahelise mängu kiirelt käima. Samuti saab antud süsteem kätte Rioti poolt saadud mängu tulemuse.</p> 

<b>Valmis asjad</b>

<ul>

	<li><b>Mootor</b></li>
		<ul>
			<li>Alagruppide loomine</li>
			<li>Seedimine</li>
			<li>Andmete hoiustamine</li>
			<li>Mängude tulemused</li>
			<li>Mängude järjekorra genereerimine</li>
		</ul>
	<li>API</li>
	<ul>
		<li><b>GetTeam</b></li>
  			<ul><li>JSON tiimi info</li></ul>
 		<li><b>GetPlayers</b></li>
  			<ul><li>JSON playerite andmed</li></ul>
 		<li><b>GetTeamInfo</b></li>
  			<ul><li>Tiimi info</li></ul>
 		<li><b>SeedComplete</b></li>
  			<ul><li>Viib turniiri seedimisest edasi live olukorda</li></ul>
 		<li><b>AddTeam</b></li>
  			<ul><li>Tiimide lisamiseks turniirile.</li></ul>
 		<li><b>Render</b></li>
  			<ul>
  				<li>Config m22rab mida kuvatakse</li>
  				<li>Saab kuvada eraldi alagruppe</li>
  			</ul>
 		<li><b>TypeBuilder</b></li>
  			<ul>
  				<li>Tyybi loomiseks abimees.</li>
			</ul>
	</ul>
	<li>Kasutajate süsteem</li>

{foreach $GitData as $val}

	{foreach explode("\n", $val->message) as $line}
		<li>{$line}</li>
		{if $line@first}<ul>{/if}
		{if $line@last}</ul>{/if}
	{/foreach}

{/foreach}

</ul>

{/block}