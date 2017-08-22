<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2005/02/20 17:12:37 $
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
	<div class='loginwidget'>
	<xsl:choose>
		<xsl:when test="$links_or_form='links'">
			<ul class='loginlinks'>
				<xsl:choose>
					<xsl:when test="//session/@user_identifier>0">
						<li class='text'>
							<xsl:if test="$show_welcome_back_msg=1">
								<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template><xsl:value-of select="//session/name/first_name"/> : 
							</xsl:if>
						</li>
						<li class='logout'>
							<xsl:if test="$show_logout=1">
								<a href="-/-logout.php">
								<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute>
								<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></span></span></a>
							</xsl:if>
						</li>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="$show_login=1">
							<li class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NOT_LOGGED_IN'"/></xsl:call-template>: 						</li>
							<li class='login'>
							<a>
							<xsl:attribute name="href"><xsl:choose>
								<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
								<xsl:otherwise>-/-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute>
							<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></span></span></a></li>
						</xsl:if>
						<xsl:if test="$show_register=1">
							<li class='joinnow'><a>
							<xsl:attribute name="href"><xsl:choose>
								<xsl:when test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_REGISTER']]/url"/></xsl:when>
								<xsl:otherwise>-join-now.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute>
							<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></span></span></a></li>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</ul>
		</xsl:when>
		<xsl:when test="$links_or_form='fancy_column'">
			<xsl:choose>
				<xsl:when test="//session/@user_identifier>0">
						<xsl:if test="property/option[name='label']/value=''">
							<h1 class='label'><span class='icon'><span class='text'>Logout</span></span></h1>
						</xsl:if>
					<div class="table">
						<div class='text'>
							<xsl:if test="$show_welcome_back_msg=1">
								<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/> </p> 
							</xsl:if>
							<xsl:if test="$show_logout=1">
								<ul class='loginlinks'>
									<li class='logout'><a href="-/-logout.php">
										<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute>
										<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></span></span></a></li>
									<li class='profile'><a href="-profile.php">
										<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_PROFILE'"/></xsl:call-template></xsl:attribute>
										<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_PROFILE'"/></xsl:call-template></span></span></a></li>
								</ul>
							</xsl:if>
						</div>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<form id="client_member_login" method="post"><xsl:attribute name="action"><xsl:choose>
							<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
							<xsl:otherwise>-/-login.php</xsl:otherwise>
						</xsl:choose></xsl:attribute>
						<!-- <xsl:if test="property/option[name='label']/value=''"> -->
							<div class='label'><span class='icon'><span class='text'>Login</span></span></div>
							<!-- <h1 class='label'><span class='icon'><span class='text'>Login</span></span></h1> -->
						<!-- </xsl:if> -->
						<div class="table">
							<div class='row'>
								<div class='cell'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></div>
								<div class='cell'><input type="text" id="login_user_name" class="logininput" name="login_user_name">
									<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value">[<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template>]</xsl:attribute>
									</xsl:if>
								</input></div>
							</div>
							<div class='row'>
								<div class='cell'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></div>							
								<div class='cell'><input type="password" class="logininput" id="login_user_pwd" name="login_user_pwd">
									<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value">[<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template>]</xsl:attribute>
									</xsl:if>
								</input></div>
							</div>
							<div class='buttonrow'>
								<div class='alignright'><input type="submit" class="loginbutton">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN'"/></xsl:call-template></xsl:attribute>
								</input></div>
							</div>
						</div>
					</form>
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
					<ul class='loginlinks'>
						<xsl:if test="$show_welcome_back_msg=1">
							<li class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/></li>
						</xsl:if>
						<xsl:if test="$show_logout=1">
							<li class='logout'><a  href="-/-logout.php"><xsl:attribute name="class"><xsl:choose>
								<xsl:when test="$uses_class=''">loginlabel</xsl:when>
								<xsl:otherwise><xsl:value-of select="$uses_class"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute>
							<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></span></span></a></li>
						</xsl:if>
					</ul>
					</xsl:when>
					<xsl:otherwise>
					<form id="client_member_login" method="post"><xsl:attribute name="action"><xsl:choose>
							<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
							<xsl:otherwise>-/-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
						<div class='row'>
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template>
							[[nbsp]]<input type="text" id="login_user_name" class="logininput" name="login_user_name">
								<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
								  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
								<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
									<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template></xsl:attribute>
								</xsl:if>
							</input>
							[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template>[[nbsp]]<input type="password" class="logininput" id="login_user_pwd" name="login_user_pwd">
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
						</div>
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
					<div class='text'>
					<xsl:if test="$show_welcome_back_msg=1">
						<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_WELCOME_BACK_MSG'"/></xsl:call-template> <xsl:value-of select="//session/name/first_name"/> </p> 
					</xsl:if>
					<xsl:if test="$show_logout=1">
						<xsl:choose>
							<xsl:when test="$form_button_type='IMAGE'">
								<p><a class="loginlink" href="-/-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_LOGOUT.gif</xsl:attribute></img></a> </p>
							</xsl:when>
							<xsl:otherwise>
								<p><a class="loginlink" href="-/-logout.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGOUT'"/></xsl:call-template></a> </p>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="$links_or_form='form'">
						<xsl:if test="$show_login=1 or $show_register=1">
						<form id="logincontainer" name="client_member_login" method="post"><xsl:attribute name="action"><xsl:choose>
								<xsl:when test="//menu[display_options/display[.='USERS_SHOW_LOGIN']]"><xsl:value-of select="//menu[display_options/display[.='USERS_SHOW_LOGIN']]/url"/></xsl:when>
								<xsl:otherwise>-/-login.php</xsl:otherwise>
							</xsl:choose></xsl:attribute>
						<xsl:if test="$login_title!=''">
							<div class="text"><xsl:attribute name="align"><xsl:value-of select="$login_title_alignment"/></xsl:attribute><xsl:value-of select="$login_title"/></div>
						</xsl:if>
						<div class='table'>
							<div class='row'>
								<div class='cell'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></div>
								<div class='cell'><input type="text" id="login_user_name" name="login_user_name">
									<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
									  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_USERNAME'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
								</input></div>
							</div>
							<div class='row'>
								<div class='cell'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></div>
								<div class='cell'><input type="password"  id="login_user_pwd" name="login_user_pwd">
									<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="size"><xsl:value-of select="$field_size"/></xsl:attribute>
									<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
										<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_DEFAULT_STRING'"/></xsl:call-template> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_PASSWORD'"/></xsl:call-template></xsl:attribute>
									</xsl:if>
									</input></div>
							</div>
							<div class='row'>
							<div class='cell'>
							<input type="submit" class="loginbutton" ><xsl:attribute name="value">&gt; <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LOGIN_FORM'"/></xsl:call-template> &lt;</xsl:attribute></input>
								<xsl:if test="//menu[display_options/display[.='USERS_SHOW_REGISTER']]"><br/>
									<a class="loginlink"><xsl:attribute name="href">-join-now.php</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></xsl:attribute>
									<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JOIN_NOW'"/></xsl:call-template></span></span>
									</a>
								</xsl:if>
							</div>
							</div>
						</div></form><xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
								<script type="text/javascript">
								__FRM_add('logincontainer');
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
	</div>
</xsl:template>

</xsl:stylesheet>