<form name="guests">

<table align=right cellspacing=0 cellpadding=20 border=0>
<tr><td bgcolor=#933c93>

<select name="menu1" size="1" onChange="gone1()">
<option selected>Guests of the "Rock-Cafe": </option>
<option value="../guests/guests-aria.php">• "Ariya"</option>
<option value="../guests/guests-slade.php">• "Slade"</option>
<option value="../guests/guests-ken-hensley.php">• Ken Hensley</option>
<option value="../guests/guests-boneym.php">• "BoneyM"</option>
<option value="../guests/guests-nazareth.php">• "Nazareth"</option>
<option value="../guests/guests-scorps.php">• "Scorpions"</option>
<option value="../guests/guests-kuzmin.php">• Vladimir Kuzmin</option>
<option value="../guests/guests-marshal.php">• Alexander Marshal</option>
<option value="../guests/guests-ocean.php">• Svyatoslav Vakarchuck</option>
<option value="../guests/guests-meladze.php">• Valery Meladze</option>
<option value="../guests/guests-lyapis.php">• "Lyapis Trubetskoy"</option>
</select>

</td></tr>
</table>

<script language="javascript">
<!--
function gone1()
{
location=document.guests.menu1.options[document.guests.menu1.selectedIndex].value
}
//-->
</script>
</form>
