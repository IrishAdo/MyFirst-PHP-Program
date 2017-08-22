<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/11 09:58:47 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_signup">
	<xsl:param name="show_hr">1</xsl:param>
	<xsl:param name="field_size">5</xsl:param>
    <!-- Display a Sign up  Form -->
	<xsl:choose>
		<xsl:when test="$links_or_form='links'">
			<xsl:if test="//menu[display_options/display[.='USERS_SHOW_LOGIN']] or //menu[display_options/display[.='USERS_SHOW_REGISTER']]">
				<xsl:choose>
					<xsl:when test="//session/@user_identifier>0">
						<xsl:if test="$show_welcome_back_msg=1">
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template><xsl:value-of select="//session/name/first_name"/> : 
						</xsl:if>
						<xsl:if test="$show_logout=1">
						<a href="-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a> :
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]">
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NOT_LOGGED_IN'"/></xsl:call-template>: 
							<a><xsl:attribute name="href">-login.php</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></a>
						</xsl:if>
						<xsl:if test="//menu[display_options/display[.='USERS_SHOW_LOGIN']] or //menu[display_options/display[.='USERS_SHOW_REGISTER']]">
							: 
						</xsl:if>
						<xsl:if test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]">
							<a><xsl:attribute name="href">-join-now.php</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></a>&#32;:
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>    
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="//session/@user_identifier>0">
					<xsl:if test="$show_welcome_back_msg=1">
						<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/> </p> 
					</xsl:if>
					<xsl:if test="$show_logout=1">
						<xsl:choose>
							<xsl:when test="$form_button_type='IMAGE'">
								<p><a href="-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGOUT.gif</xsl:attribute></img></a> </p>
							</xsl:when>
							<xsl:otherwise>
								<p><a href="-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a> </p>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:if test="$show_hr=1"><br/><hr width="90%"/></xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="$links_or_form='form'">
						<xsl:if test="//menu[display_options/display[.='USERS_SHOW_LOGIN']] or //menu[display_options/display[.='USERS_SHOW_REGISTER']]">
						<form id="client_member_login" method="post">
							<xsl:attribute name="action">-login.php</xsl:attribute>
						<!--
<INPUT type="hidden" name="command" value="ENGINE_LOGIN"/>
-->
						<table cellspacing="0" cellpadding="0" border="0">
						<xsl:if test="$login_title!=''">
							<tr>
								<td colspan="2" class="logintitle"><xsl:attribute name="align"><xsl:value-of select="$login_title_alignment"/></xsl:attribute><xsl:value-of select="$login_title"/></td>
							</tr>
						</xsl:if>
						
							<tr>
								<td align="right"><LABEL for="login_user_name"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></LABEL> : </td>
								<td><input type="text" id="login_user_name" name="login_user_name">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 10"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
								</input></td>
							</tr>
							<tr>
								<td align="right"><LABEL for="login_user_pwd"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></LABEL> : </td>
								<td><input type="password"  id="login_user_pwd" name="login_user_pwd">
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
									<xsl:attribute name="style">width: <xsl:value-of select="$field_size * 10"/></xsl:attribute>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
									</input></td>
							</tr>
							<tr><td align="center" colspan="2"><xsl:choose>
							<xsl:when test="$form_button_type='IMAGE'">
							<xsl:if test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]">
								<a><xsl:attribute name="href">-join-now.php</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute>
								<img class="button" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_REGISTER.gif</xsl:attribute>
								<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute></img></a> [[nbsp]]
							</xsl:if>
							<input type="image" class="button">
							<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGIN.gif</xsl:attribute>
							<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN_FORM'"/></xsl:call-template></xsl:attribute>
							</input></xsl:when>
							<xsl:otherwise>
							<input type="submit" class="button"><xsl:attribute name="value">&gt; <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN_FORM'"/></xsl:call-template> &lt;	</xsl:attribute></input>
							<xsl:if test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]"><br/>
								<a><xsl:attribute name="href">-join-now.php</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></a>
							</xsl:if>
							</xsl:otherwise>
							</xsl:choose></td></tr>
						</table></form><xsl:if test="$show_hr=1"><br/>
						<hr width="90%"/></xsl:if>
						<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
							<script type="text/javascript">
							__FRM_add('client_member_login');
							</script>
						</xsl:if>
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