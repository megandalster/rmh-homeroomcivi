<?php

// Begin a form
echo ("<form method=\"post\">");

// start a table to hold all the search forms
echo ("<p><table>");

// begin adding entries to the tables
echo ("<tr>");
echo ("<td>Primary Guest's First Name: </td>");
echo ("<td><input type=\"text\" name=\"p_first_name\"/></td>");
echo ("</tr>");

echo ("<tr>");
echo ("<td>Patient Name: </td>");
echo ("<td><input type=\"text\" name=\"pat_first_name\"/></td>");
echo ("</tr>");
	
echo ("<tr>");
echo ("<td>Room Number: </td>");
echo ("<td><input type=\"text\" name=\"room_no\"/></td>");
echo ("</tr>");
	
echo ("<tr>");
echo ("<td>Date (mm,dd,yy): </td>");
echo ("<td>M:<select name=\"month\">");
echo ("<option value=''></option>");
for($i = 1; $i<=12; $i++){
	echo ("<option value='".$i."'>".$i."</option>");
}
echo ("</select>, D:<select name=\"day\">");
echo ("<option value=''></option>");
for($i = 1; $i<=31; $i++){
	echo ("<option value='".$i."'>".$i."</option>");
}
echo ("</select>, Y:");
echo ("<input type=\"text\" name=\"year\" maxLength=\"2\" size=\"3\"/>");
echo ("</td></tr>");
	
echo ("<tr>");
echo ("<td>Status: </td>");
echo ("<td><select name=\"type\">");
echo ("<option value=\"\"></option>");
echo ("<option value=\"active\">active</option>");
echo ("<option value=\"pending\">pending</option>");
echo ("<option value=\"closed\">closed</option>");	
echo ("<option value=\"closed\">closed-deceased</option>");	
echo ("</select></td></tr>");
	
echo ("<tr>");
echo ("<td>Manager's Notes: </td>");
echo ("<td><input type=\"text\" name=\"notes\"/></td></tr>");

echo ("<tr>");
echo ("<td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Search\"/></td></tr>");
	
echo ("</table></form>");
?>