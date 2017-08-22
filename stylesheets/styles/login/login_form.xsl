<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/09/11 10:01:47 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_login">
	<xsl:param name="spacer"></xsl:param>
	<xsl:param name="spacer_width">25</xsl:param>
	<xsl:param name="links_or_form"><xsl:value-of select="$type_of_form"/></xsl:param>
	<xsl:param name="show_hr">1</xsl:param>
	<xsl:param name="show_logout">1</xsl:param>
	<xsl:param name="show_login">1</xsl:param>
	<xsl:param name="show_register">1</xsl:param>
	<xsl:param name="show_welcome_back_msg">1</xsl:param>
	<xsl:param name="field_size">5</xsl:param>
	<xsl:param name="login_title"></xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="login_title_alignment">LEFT</xsl:param>
    <xsl:comment> Login Form </xsl:comment>
	<xsl:choose>
		<xsl:when test="$links_or_form='links'">
				<xsl:choose>
					<xsl:when test="//session/@user_identifier>0">
						<xsl:if test="$show_welcome_back_msg=1">
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template><xsl:value-of select="//session/name/first_name"/> : 
						</xsl:if>
						<xsl:if test="$show_logout=1">
						<a href="-logout.php"><xsl:attribute name="class"><xsl:choose>
							<xsl:when test="$uses_class=''">loginlink</xsl:when>
							<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
						</xsl:choose></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a> :
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="$show_login=1">
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NOT_LOGGED_IN'"/></xsl:call-template>: 
							<a><xsl:attribute name="class"><xsl:choose>
							<xsl:when test="$uses_class=''">loginlink</xsl:when>
							<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
						</xsl:choose></xsl:attribute><xsl:attribute name="href"><xsl:choose>
							<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
							<xsl:otherwise>-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></a>
						</xsl:if>
						<xsl:if test="$show_login=1 or $show_register=1">
							: 
						</xsl:if>
						<xsl:if test="$show_register=1">
							<a><xsl:attribute name="class"><xsl:choose>
							<xsl:when test="$uses_class=''">loginlink</xsl:when>
							<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
						<xsl:attribute name="href"><xsl:choose>
							<xsl:when test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_REGISTER']]/url"/></xsl:when>
							<xsl:otherwise>-join-now.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute>
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></a>&#32;:
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
		</xsl:when>
		<xsl:when test="$links_or_form='fancy_column'">
				<xsl:choose>
					<xsl:when test="//session/@user_identifier>0">
					
					<table cellspacing="0" cellpadding="0" border="0" summary="login form" class="width100percent">
						<xsl:if test="property/option[name='label']/value=''">
							<tr>
								<td class="menuheader"><xsl:attribute name="align"><xsl:value-of select="$login_title_alignment"/></xsl:attribute>Logout</td>
							</tr>
						</xsl:if>
							<tr><td class="centerTable"><table summary="" class="width100percent">
							<tr><td class="loginfields">
					<xsl:if test="$show_welcome_back_msg=1">
						<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/> </p> 
					</xsl:if>
					<xsl:if test="$show_logout=1">
						<xsl:choose>
							<xsl:when test="$form_button_type='IMAGE'">
								<p><a href="-logout.php"><xsl:attribute name="class"><xsl:choose>
							<xsl:when test="$uses_class=''">loginlink</xsl:when>
							<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
						</xsl:choose></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGOUT.gif</xsl:attribute></img></a> </p>
							</xsl:when>
							<xsl:otherwise>
								<p><a href="-logout.php"><xsl:attribute name="class"><xsl:choose>
							<xsl:when test="$uses_class=''">loginlink</xsl:when>
							<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
						</xsl:choose></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a> </p>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if></td>
							</tr></table></td></tr>
						</table>
					</xsl:when>
					<xsl:otherwise>
					<form id="client_member_login" method="post"><xsl:attribute name="action"><xsl:choose>
							<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
							<xsl:otherwise>-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>

						<!--
<INPUT type="hidden" name="command" value="ENGINE_LOGIN"/>
-->
						<table cellspacing="0" cellpadding="0" border="0" summary="login form" class="width100percent">
						<xsl:if test="property/option[name='label']/value=''">
							<tr>
								<td class="menuheader"><xsl:attribute name="align"><xsl:value-of select="$login_title_alignment"/></xsl:attribute>Login</td>
							</tr> 
						</xsl:if>
							<tr><td class="centerTable"><table summary="">
							<tr><td class="loginfields"><LABEL><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></LABEL> : </td>
								<td rowspan="4" class="loginsubmit">
						<xsl:choose>
							<xsl:when test="$form_button_type='IMAGE' or $image_path='/libertas_images/themes/magherafeltcouncil'">
									<input type="image" class="loginbutton">
										<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGIN.gif</xsl:attribute>
										<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute>
									</input>
							</xsl:when>
							<xsl:otherwise>
									<input type="submit" class="loginbutton">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute>
									</input>
							</xsl:otherwise>
						</xsl:choose>
								</td>
							</tr>
							<tr><td><input type="text" id="login_user_name" class="logininput" name="login_user_name">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
<!--									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 20"/></xsl:attribute> -->
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
								</input></td></tr>
							<tr>
								<td class="loginfields"><LABEL><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></LABEL> : </td></tr>
							<tr><td><input type="password" class="logininput" id="login_user_pwd" name="login_user_pwd">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
<!--									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 20"/></xsl:attribute> -->
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
									</input></td>
							</tr></table></td></tr>
						</table></form>
							<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
							<script type="text/javascript">
							__FRM_add('client_member_login');
							</script>
							</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
		</xsl:when>
		<xsl:when test="$links_or_form='fancy_row'">
				<xsl:choose>
					<xsl:when test="//session/@user_identifier>0">
					<p>
					<xsl:if test="$show_welcome_back_msg=1">
						<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/> :: 
					</xsl:if>
					<xsl:if test="$show_logout=1">
						<xsl:choose>
							<xsl:when test="$form_button_type='IMAGE'">
								<a  href="-logout.php"><xsl:attribute name="class"><xsl:choose>
										<xsl:when test="$uses_class=''">loginlabel</xsl:when>
										<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
									</xsl:choose></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGOUT.gif</xsl:attribute></img></a>
							</xsl:when>
							<xsl:otherwise>
								<a  href="-logout.php"><xsl:attribute name="class"><xsl:choose>
									<xsl:when test="$uses_class=''">loginlabel</xsl:when>
									<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					</p>
					</xsl:when>
					<xsl:otherwise>
					<form id="client_member_login" method="post"><xsl:attribute name="action"><xsl:choose>
							<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
							<xsl:otherwise>-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
						<!--
<INPUT type="hidden" name="command" value="ENGINE_LOGIN"/>
-->
						[[nbsp]]<LABEL><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></LABEL> : 
								[[nbsp]]<input type="text" id="login_user_name" class="logininput" name="login_user_name">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
<!--									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 20"/></xsl:attribute> -->
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
								</input>
								[[nbsp]]<LABEL><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></LABEL> : 
						[[nbsp]]<input type="password" class="logininput" id="login_user_pwd" name="login_user_pwd">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
<!--							<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 20"/></xsl:attribute>-->
							<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
							<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
								<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template></xsl:attribute>
							</xsl:if>
						</input>
						[[nbsp]]<input type="submit" class="button">
							<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN_FORM'"/></xsl:call-template></xsl:attribute>
						</input>
					</form>
							<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
							<script type="text/javascript">
							__FRM_add('client_member_login');
							</script>
							</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="//session/@user_identifier>0">
				<table sumamry="this table contains the system logout option">
				<tr>
								<xsl:if test="$spacer='left'">
								<td><img alt="" src="/libertas_images/themes/1x1.gif"><xsl:attribute name="width"><xsl:value-of select="$spacer_width"/></xsl:attribute></img></td>
								</xsl:if>
					<td class="logindata">
					<xsl:if test="$show_welcome_back_msg=1">
						<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/> </p> 
					</xsl:if>
					<xsl:if test="$show_logout=1">
						<xsl:choose>
							<xsl:when test="$form_button_type='IMAGE'">
								<p><a class="loginlink" href="-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGOUT.gif</xsl:attribute></img></a> </p>
							</xsl:when>
							<xsl:otherwise>
								<p><a class="loginlink" href="-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a> </p>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if></td>
					</tr>
					</table>
					<xsl:if test="$show_hr=1"><br/><hr width="90%"/></xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="$links_or_form='form'">
						<xsl:if test="$show_login=1 or $show_register=1">
						<form id="logincontainer" name="client_member_login" method="post"><xsl:attribute name="action"><xsl:choose>
								<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
								<xsl:otherwise>-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
						<!--
<INPUT type="hidden" name="command" value="ENGINE_LOGIN"/>
-->
						<table cellspacing="0" cellpadding="0" border="0" summary="login form">
						
						<xsl:if test="$login_title!=''">
							<tr>
								<xsl:if test="$spacer='left'">
								<td><img alt="" src="/libertas_images/themes/1x1.gif"><xsl:attribute name="width"><xsl:value-of select="$spacer_width"/></xsl:attribute></img></td>
								</xsl:if>
								<td colspan="2" class="logintitle"><xsl:attribute name="align"><xsl:value-of select="$login_title_alignment"/></xsl:attribute><xsl:value-of select="$login_title"/></td>
							</tr>
						</xsl:if>
						
							<tr>
								<xsl:if test="$spacer='left'">
								<td rowspan="3"><img alt="" src="/libertas_images/themes/1x1.gif"><xsl:attribute name="width"><xsl:value-of select="$spacer_width"/></xsl:attribute></img></td>
								</xsl:if>
								<td align="right" class="loginfields"><LABEL><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></LABEL> : </td>
								<td><input type="text" id="login_user_name" name="login_user_name">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
<!--									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 10"/></xsl:attribute>-->
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
								</input></td>
							</tr>
							<tr>
								<td align="right" class="loginfields"><LABEL><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></LABEL> : </td>
								<td><input type="password"  id="login_user_pwd" name="login_user_pwd">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
<!--									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 10"/></xsl:attribute>-->
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
									</input></td>
							</tr>
							<tr><td align="right" colspan="2" class="loginfields"><xsl:choose>
								<xsl:when test="$form_button_type='IMAGE'">
									<input type="image" class="loginbutton" >
										<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGIN.gif</xsl:attribute>
										<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN_FORM'"/></xsl:call-template></xsl:attribute>
									</input><br/>
								<xsl:if test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]">
									<a><xsl:attribute name="href"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_REGISTER']]/url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute>
									<img alt="" class="button" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_REGISTER.gif</xsl:attribute>
								<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute></img></a>
							</xsl:if></xsl:when>
							<xsl:otherwise>
							<input type="submit" class="loginbutton" ><xsl:attribute name="value">&gt; <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN_FORM'"/></xsl:call-template> &lt;</xsl:attribute></input>
								<xsl:if test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]"><br/>
									<a class="loginlink"><xsl:attribute name="href">-join-now.php</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></a>
								</xsl:if>
							</xsl:otherwise>
							</xsl:choose></td></tr>
						</table></form><xsl:if test="$show_hr=1"><br/>
						<hr width="90%"/></xsl:if>
						</xsl:if>
													<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
							<script type="text/javascript">
							__FRM_add('logincontainer');
							</script>
							</xsl:if>

						</xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>

</xsl:template>

</xsl:stylesheet>