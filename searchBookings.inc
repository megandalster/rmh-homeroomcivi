<?php

// Begin a form
echo ("<form method=\"post\">");

// start a table to hold all the search forms
echo ("<p><table>");

// begin adding entries to the tables
echo ("<tr>");
echo ("<td>Primary Guest's First Name: </td>");
echo ("<td><input type=\"text\" name=\"p_first_name\" value=\"".$_POST['p_first_name']."\"></td>");
echo ("</tr>");

echo ("<tr>");
echo ("<td>Primary Guest's Last Name: </td>");
echo ("<td><input type=\"text\" name=\"p_last_name\" value=\"".$_POST['p_last_name']."\"></td>");
echo ("</tr>");

echo ("<tr>");
echo ("<td>Patient's Name: </td>");
echo ("<td><input type=\"text\" name=\"pat_first_name\" value=\"".$_POST['pat_first_name']."\"></td>");
echo ("</tr>");
	
echo ("<tr>");
echo ("<td>Room Number: </td>");
echo ("<td><input type=\"text\" name=\"room_no\" value=\"".$_POST['room_no']."\"></td>");
echo ("</tr>");
	
echo ("<tr>");
echo ("<td>Date Submitted: </td>");
echo ("<td>Month:<select name=\"month\">");
echo ("<option value=''></option>");
for($i = 1; $i<=12; $i++){
	echo ("<option value=".$i.($_POST["month"]==$i?" selected":" ").">".$i."</option>");
}
echo ("</select>, Day:<select name=\"day\">");
echo ("<option value=''></option>");
for($i = 1; $i<=31; $i++){
	echo ("<option value=".$i.($_POST["day"]==$i?" selected":" ").">".$i."</option>");
}
echo ("</select>, Year:<select name=\"year\">");
echo ("<option value=''></option>");
for($i = 10; $i<=date('y'); $i++){
	echo ("<option value=".$i.($_POST["year"]==$i?" selected":" ").">20".$i."</option>");
}
echo ("</select>");
echo ("</td></tr>");
	
echo ("<tr>");
echo ("<td>Status: </td>");
echo ("<td><select name=\"type\">");
echo ("<option value=\"\"></option>");
echo ("<option value=\"active\" ".($_POST['type']=='active'?'selected':'').">active</option>");
echo ("<option value=\"pending\" ".($_POST['type']=='pending'?'selected':'').">pending</option>");
echo ("<option value=\"closed\" ".($_POST['type']=='closed'?'selected':'').">closed</option>");	
echo ("<option value=\"closed-deceased\" ".($_POST['type']=='closed-deceased'?'selected':'').">closed-deceased</option>");	
echo ("</select></td></tr>");
	
echo ("<tr>");
echo ("<td>Additional Notes: </td>");
echo ("<td><input type=\"text\" name=\"notes\" value=\"".$_POST['notes']."\"></td></tr>");

echo ("<tr>");
echo ("<td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Search\"/></td></tr>");
	
echo ("</table></form>");
?>