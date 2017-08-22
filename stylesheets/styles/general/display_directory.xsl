<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.24 $
- Modified $Date: 2005/01/11 16:29:46 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_directory_atoz">
	<xsl:call-template name="display_atoz_links"/>
	<xsl:apply-templates select="text" />
	<xsl:apply-templates select="filter/form"/>
	<xsl:call-template name="display_list_results">
		<xsl:with-param name="showfilter">0</xsl:with-param>
		<xsl:with-param name="showtext">0</xsl:with-param>
	</xsl:call-template>
</xsl:template>

<xsl:template name="display_feature">
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="show_label">1</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<div class="FeatureResults">
		<xsl:if test="$show_label=1 and label!=''">
		<h1 class='directoryfeature'><span><xsl:value-of select="label"/></span></h1>
		</xsl:if>
		<xsl:variable name="furi"><xsl:value-of select="fake_uri"/></xsl:variable>
		<xsl:variable name="directory_identifier"><xsl:value-of select="directory_identifier"/></xsl:variable>
		<xsl:variable name="link_to_real_url"><xsl:value-of select="link_to_real_url/@type"/></xsl:variable>
		<xsl:variable name="link_to_real_url_link"><xsl:value-of select="link_to_real_url"/></xsl:variable>
		<xsl:for-each select="content/info[@list=$directory_identifier]/results/entry">
		<div class="featureEntry">
			<xsl:call-template name="display_entry">
				<xsl:with-param name="link_to_real_url"><xsl:value-of select="$link_to_real_url"/></xsl:with-param>
				<xsl:with-param name="link_to_real_url_link"><xsl:value-of select="$link_to_real_url_link"/></xsl:with-param>
				<xsl:with-param name="fake_path"><xsl:value-of select="$furi"/></xsl:with-param>
				<xsl:with-param name="entry_identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				<xsl:with-param name="directory_identifier"><xsl:value-of select="$directory_identifier"/></xsl:with-param>
				<xsl:with-param name="entry_type">INFORMATION</xsl:with-param>
				<xsl:with-param name="feature">1</xsl:with-param>
			</xsl:call-template>
		</div>
		</xsl:for-each>
	</div>
</xsl:template>

<xsl:template name="display_directory">
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="show_label">1</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:if test="$show_label=1 and label!=''">
		<h1 id='directory'><xsl:value-of select="label"/></h1>
	</xsl:if>
	<xsl:call-template name="display_atoz_links"><xsl:with-param name="prefix">_</xsl:with-param></xsl:call-template>
	<xsl:variable name="directory_identifier"><xsl:value-of select="list"/></xsl:variable>
	<xsl:variable name="entry_type"><xsl:value-of select="shop"/></xsl:variable>
	<xsl:variable name="link_to_real_url"><xsl:value-of select="link_to_real_url/@type"/></xsl:variable>
	<xsl:variable name="link_to_real_url_link"><xsl:value-of select="link_to_real_url"/></xsl:variable>
	<xsl:variable name="cat_label"><xsl:value-of select="cat_label"/></xsl:variable>
	<xsl:if test="form"><xsl:apply-templates select="form"/></xsl:if>
	<xsl:variable name="uri"><xsl:value-of select="substring-before(fake_uri,'index.php')"/></xsl:variable>
	<xsl:variable name="directory_type"><xsl:value-of select="display_type"/></xsl:variable>
	<xsl:variable name="info_add_label"><xsl:value-of select="info_add_label"/></xsl:variable>
	<xsl:variable name="info_no_stock_label"><xsl:value-of select="info_no_stock_label"/></xsl:variable>
	<xsl:variable name="cat"><xsl:value-of select="current_category"/></xsl:variable>
	<xsl:if test="$cat!='' and contains(//setting[@name='real_script'],'index.php')">
		<xsl:if test="workflow != '0'">
			<p class="addentry">
				<xsl:choose>
					<!-- Allow user to Add Entry Automatically -->
					<!-- Allow user to Add Entry Requires Approval by Administrator -->
					<xsl:when test="workflow='1' or workflow='2'">| <a><xsl:attribute name="href"><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/>/_add.php</xsl:attribute>Add new Entry</a> | 
						<xsl:if test="//xml_document/modules/module[@name='client']/licence/product/@type='ECMS' and //session/@logged_in!=0">
							<xsl:if test="elert='1'">
								<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=ELERT_SIGNUP&amp;category=<xsl:value-of select="$cat"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ELERT_SIGNUP'"/></xsl:call-template></a> |
							</xsl:if>
						</xsl:if>
					</xsl:when>
					<!-- Allow Registered User to Add Entry Automatically -->
					<!-- Allow Registered User to Add Entry Requires Approval by Administrator -->
					<xsl:when test="workflow='3' or workflow='4'">
						<xsl:if test="//session/@logged_in='1'">
							| <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=INFORMATION_ADD_ENTRY&amp;list=<xsl:value-of select="list"/>&amp;category=<xsl:value-of select="$cat"/></xsl:attribute>Add new Entry</a> |
							<xsl:if test="elert='1'">
								 <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=ELERT_SIGNUP&amp;category=<xsl:value-of select="$cat"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ELERT_SIGNUP'"/></xsl:call-template></a> |
							</xsl:if>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>| <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=INFORMATION_ADD_ENTRY&amp;list=<xsl:value-of select="list"/>&amp;category=<xsl:value-of select="$cat"/></xsl:attribute>Add new Entry</a> | 
						<!--			
						<xsl:if test="//xml_document/modules/module[@name='client']/licence/product/@type='ECMS' and //session/@logged_in!=0">
							<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=ELERT_SIGNUP&amp;category=<xsl:value-of select="$cat"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ELERT_SIGNUP'"/></xsl:call-template></a> |
						</xsl:if> 
						-->
					</xsl:otherwise>
				</xsl:choose>
			</p>		
		</xsl:if>
	</xsl:if>
	<xsl:if test="//category[@parent=$cat]">
		<hr class="directorysplitter"/>
	</xsl:if>
	<xsl:variable name="dformat"><xsl:value-of select="display_format"/></xsl:variable>
	<xsl:variable name="dcolumn"><xsl:value-of select="display_columns"/></xsl:variable>
	<!--
	[<xsl:value-of select="$dformat"/>]
	[<xsl:value-of select="$dcolumn"/>]
	[<xsl:value-of select="$cat"/>]
	[<xsl:value-of select="//setting[@name='real_script']"/>]
	[<xsl:value-of select="count(categorylist/category)"/>]
	-->
	<xsl:if test="contains(//setting[@name='real_script'],'index.php') and $dformat != 'hide_categories'">
	<div id="CategoryList">
	<xsl:choose>
		<xsl:when test="$cat=''">
			<ul><xsl:attribute name='class'>columns<xsl:value-of select="$dcolumn"/></xsl:attribute>
			<xsl:for-each select="category">
				<li><a><xsl:attribute name="href"><xsl:value-of select="$uri"/><xsl:value-of select="uri"/></xsl:attribute><xsl:value-of select="label"/></a>
				<xsl:if test="$dformat='display_2_lvl' and children/category"><ul class='sublevel'><xsl:for-each select="children/category[6 > position()]">
					<li><a><xsl:attribute name="href"><xsl:value-of select="$uri"/><xsl:value-of select="uri"/></xsl:attribute><xsl:value-of select="label"/></a></li> 
					</xsl:for-each>
					<xsl:if test="count(children/category) > 6">
						<li><a><xsl:attribute name="href"><xsl:value-of select="$uri"/><xsl:value-of select="uri"/></xsl:attribute>List more sub categories for (<xsl:value-of select="label"/>)</a></li>
					</xsl:if></ul> 
				</xsl:if>
				</li>
			</xsl:for-each>
			</ul>
		</xsl:when>
		<xsl:otherwise>
		<ul><xsl:attribute name='class'>columns<xsl:value-of select="$dcolumn"/></xsl:attribute>
		<xsl:for-each select="//categorylist[@parent=$cat]/category">
			<li class="folder"><a><xsl:attribute name="href"><xsl:value-of select="$uri"/><xsl:value-of select="uri"/></xsl:attribute><xsl:value-of select="label"/></a></li>
		</xsl:for-each>
		</ul>
		<xsl:if test="//category[@parent=$cat]">
		<hr class="directorysplitter"/>
		</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
	</div>
	</xsl:if>

	<xsl:choose>
		<xsl:when test="boolean(data_list)">
			<xsl:call-template name="display_list_results"/>
		</xsl:when>
		<xsl:when test="content/info[@list=$directory_identifier]/display[@type='1'] or content/info[@list=$directory_identifier]/display[@type='3']">
			<xsl:variable name="fake_path"><xsl:value-of select="substring-before(fake_uri,'index.php')"/></xsl:variable>
			<xsl:if test="content/info[@list=$directory_identifier]/display/@type='1'">
				<script type="text/javascript" src="/libertas_images/javascripts/sortabletable.js"><xsl:comment> load sortable table</xsl:comment></script>
			</xsl:if>
			<xsl:for-each select="//content/info[@list=$directory_identifier]/display">
				<xsl:if test=".//field[@id='__add_to_basket__']">
					<a class='basketview' href='_view-cart.php' title='View your basket'><span class='icon'><span class='text'>View basket</span></span></a>
				</xsl:if>
			</xsl:for-each>
			<link type="text/css" rel="StyleSheet" href="/libertas_images/themes/sortabletable.css" />
			<table summary="List of results" cellspacing="0" cellpadding="0" style="width:99%;margin:0px;">
				<xsl:attribute name="id">table-summary-<xsl:value-of select="content/info/@list"/></xsl:attribute>
				<xsl:attribute name="class">sortable</xsl:attribute>
				<xsl:for-each select="content/info[@list=$directory_identifier]/display">
					<tr>
					<xsl:for-each select=".//field">
						<th>
						<xsl:choose>
							<xsl:when test="@id!='__category__'"><xsl:value-of select="label"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="$cat_label"/></xsl:otherwise>
						</xsl:choose>
						</th>
					</xsl:for-each>
					</tr>
				</xsl:for-each>
				<xsl:for-each select="content/info[@list=$directory_identifier]/results/entry">
					<xsl:variable name="entry_identifier"><xsl:value-of select="@identifier"/></xsl:variable>
					<xsl:variable name="real_id"><xsl:value-of select="@real_id"/></xsl:variable>
					<xsl:variable name="md_identifier"><xsl:value-of select="//metadata[@linkto=$real_id]/@identifier"/></xsl:variable>
					
					<tr>
						<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable>
						<xsl:for-each select="//content/info[@list=$directory_identifier]/display">
							<xsl:for-each select=".//field">
								<xsl:variable name="id"><xsl:value-of select="@id"/></xsl:variable>
								<xsl:variable name="filterable"><xsl:choose><xsl:when test="@filter='1'">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose></xsl:variable>
								<xsl:variable name="entry_url"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name='uri' and @link='no' and @visible='no']"/></xsl:variable>
								<td>
									<xsl:choose>
										<xsl:when test="@id='__add_to_basket__'">
											<xsl:variable name="quantity"><xsl:value-of select="//metadata[@linkto=$real_id]/quantity"/></xsl:variable>
											<xsl:choose>
<!--												<xsl:when test="$can_buy=0"></xsl:when>-->
												<xsl:when test="$quantity!=0">
													<a class='basketadd'><xsl:attribute name="href">_add-to-cart.php?identifier=<xsl:value-of select="$md_identifier"/>&amp;type=<xsl:value-of select="$directory_type"/></xsl:attribute><span class='icon'><span class='text'><xsl:value-of select="$info_add_label"/></span></span></a>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="$info_no_stock_label"/>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="@id='__category__'">
											<xsl:choose>
												<xsl:when test="count(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory)!=1">
													<ul>
													<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory">
														<xsl:variable name='cc'><xsl:value-of select="@identifier"/></xsl:variable>
														<li>
															<a><xsl:attribute name="href"><xsl:value-of select="substring-before(//setting[@name='script'],'index.php')"/><xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:attribute>
																<xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/>
															</a>
														</li>
													</xsl:for-each>
													</ul>
												</xsl:when>
												<xsl:otherwise>
													<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory">
														<xsl:variable name='cc'><xsl:value-of select="@identifier"/></xsl:variable>
														<a><xsl:attribute name="href"><xsl:value-of select="substring-before(//setting[@name='script'],'index.php')"/><xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:attribute>
															<xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/>
														</a>
													</xsl:for-each>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/@type='LIST'">
											<xsl:choose>
												<xsl:when test="count(//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option)=1"><xsl:choose>
													<xsl:when test="@link='1'">
														<a>
															<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="$entry_url"/></xsl:attribute>
															<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$id]/option[position()=1]" disable-output-escaping="yes"/>
															</a></xsl:when>
													<xsl:otherwise>
														<xsl:choose>
															<!-- check filter -->
															<xsl:when test="$filterable=1">
																<a><xsl:attribute name="href"><xsl:choose>
																	<xsl:when test="contains($fake_path,'index.php')"><xsl:value-of select="substring-before($fake_path,'index.php')"/></xsl:when>
																	<xsl:otherwise><xsl:value-of select="$fake_path"/></xsl:otherwise>
																</xsl:choose><xsl:value-of select="filteroptions/option[.=//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option[position()=1]]/@value" disable-output-escaping="yes"/></xsl:attribute>
																<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option[position()=1]" disable-output-escaping="yes"/>
																</a>
															</xsl:when>
														<xsl:otherwise><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option[position()=1]" disable-output-escaping="yes"/></xsl:otherwise>
													</xsl:choose>
													</xsl:otherwise>
												</xsl:choose></xsl:when>
												<xsl:otherwise>
													<ul>
														<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option">
														<li class="plain"><xsl:choose>
															<xsl:when test="@link='1'"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="$entry_url"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></a></xsl:when>
															<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
														</xsl:choose></li>
														</xsl:for-each>
													</ul>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/@type='email'">
										<a><xsl:attribute name="href"><xsl:choose>
											<xsl:when test="//setting[@name='sp_use_antispam']!='Yes'">mailto:</xsl:when>
											<xsl:otherwise><xsl:value-of select="//setting[@name='base']"/>-/-anti-spam.php?to=</xsl:otherwise>
											</xsl:choose></xsl:attribute>
											<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]" disable-output-escaping="yes"/>
											</a>
										</xsl:when>
										<xsl:otherwise>
											<xsl:variable name="content"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]"/></xsl:variable>
											<xsl:choose>
												<xsl:when test="string-length($content)=0">[[nbsp]]</xsl:when>
												<xsl:otherwise>
													<xsl:choose>
														<xsl:when test="@link=1"><a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name='uri']/value"/></xsl:attribute><xsl:value-of select="$content"/></a></xsl:when>
														<xsl:otherwise><xsl:value-of select="$content"/></xsl:otherwise>
													</xsl:choose>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</xsl:for-each>
						</xsl:for-each>
					</tr>
				</xsl:for-each>
			</table>
			<xsl:if test="content/info[@list=$directory_identifier]/results/pages/page=2">
			<ul class="pages">
				<li>Go to page</li>
				<xsl:for-each select="content/info[@list=$directory_identifier]/results/pages/page">
					<xsl:variable name="path"><xsl:value-of select="//setting[@name='fake_script']"/>/_page<xsl:value-of select="."/>.php</xsl:variable>
					<xsl:variable name="index"><xsl:value-of select="//setting[@name='fake_script']"/>/_page1.php</xsl:variable>
					<xsl:choose>
						<xsl:when test="//setting[@name='real_script']=$path or ( $index=$path and contains(//setting[@name='real_script'],'index.php'))">
							<li><xsl:value-of select="."/></li>
						</xsl:when>
						<xsl:when test="$index=$path">
							<li><a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/index.php</xsl:attribute>
								<xsl:value-of select="."/></a></li>
						</xsl:when>
						<xsl:otherwise>
							<li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/_page<xsl:value-of select="."/>.php</xsl:attribute><xsl:value-of select="."/></a></li>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</ul>
			</xsl:if>
		</xsl:when>
		<xsl:when test="content/info[@list=$directory_identifier]/display[@type='4'] or content/info[@list=$directory_identifier]/display[@type='5'] or content/info[@list=$directory_identifier]/display[@type='6']">

			<table summary="Calendar of Events" cellspacing="0" cellpadding="0" style="width:99%;margin:0px;" class="calendarofevents" >
				<xsl:attribute name="id">table-summary-<xsl:value-of select="content/info/@list"/></xsl:attribute>
				<xsl:for-each select="content/info[@list=$directory_identifier]/display">
					<tr>
					<xsl:for-each select="seperator_row/seperator/field[position()=1]">
						<th>
						<xsl:choose>
							<xsl:when test="@id!='__category__'"><xsl:value-of select="label"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="$cat_label"/></xsl:otherwise>
						</xsl:choose>
						</th>
					</xsl:for-each>
					</tr>
				</xsl:for-each>
				<xsl:for-each select="content/info[@list=$directory_identifier]/results/entry">
					<xsl:variable name="entry_identifier"><xsl:value-of select="@identifier"/></xsl:variable>
					<xsl:variable name="real_id"><xsl:value-of select="@real_id"/></xsl:variable>
					<xsl:variable name="md_identifier"><xsl:value-of select="//metadata[@linkto=$real_id]/@identifier"/></xsl:variable>
					<tr>
						<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable>
						<xsl:variable name="entry_category"><xsl:value-of select="choosencategory[position()=1]/@identifier"/></xsl:variable>
						<xsl:for-each select="//content/info[@list=$directory_identifier]/display/seperator_row/seperator">
							<td>
								<xsl:for-each select="field">
								<div>
									<xsl:variable name="id"><xsl:value-of select="@id"/></xsl:variable>
									<xsl:variable name="filterable"><xsl:choose><xsl:when test="@filter='1'">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose></xsl:variable>
									<xsl:variable name="entry_url"><xsl:value-of select="substring-before(//category[@identifier=$entry_category]/uri,'index.php')"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name='uri' and @link='no' and @visible='no']"/></xsl:variable>
									<xsl:choose>
										<xsl:when test="@id='__add_to_basket__'">
											<xsl:variable name="quantity"><xsl:value-of select="//metadata[@linkto=$real_id]/quantity"/></xsl:variable>
											<xsl:choose>
<!--												<xsl:when test="$can_buy=0"></xsl:when>-->
												<xsl:when test="$quantity!=0">
													<a class='basketadd'><xsl:attribute name="href">_add-to-cart.php?identifier=<xsl:value-of select="$md_identifier"/>&amp;type=<xsl:value-of select="$directory_type"/></xsl:attribute><span class='icon'><span class='text'><xsl:value-of select="$info_add_label"/></span></span></a>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="$info_no_stock_label"/>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="@id='__category__'">
											<xsl:choose>
												<xsl:when test="count(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory)!=1">
													<ul>
													<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory">
														<xsl:variable name='cc'><xsl:value-of select="@identifier"/></xsl:variable>
														<li>
															<a><xsl:attribute name="href"><xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:attribute>
																<xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/>
															</a>
														</li>
													</xsl:for-each>
													</ul>
												</xsl:when>
												<xsl:otherwise>
													<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory">
														<xsl:variable name='cc'><xsl:value-of select="@identifier"/></xsl:variable>
														<a><xsl:attribute name="href"><xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:attribute>
															<xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/>
														</a>
													</xsl:for-each>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/@type='LIST'">
											<xsl:choose>
												<xsl:when test="count(//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option)=1"><xsl:choose>
													<xsl:when test="@link='1'">
														<a>
															<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="$entry_url"/></xsl:attribute>
															<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$id]/option[position()=1]" disable-output-escaping="yes"/>
															</a></xsl:when>
													<xsl:otherwise>
														<xsl:choose>
															<!-- check filter -->
															<xsl:when test="$filterable=1">
																<a><xsl:attribute name="href"><xsl:choose>
																	<xsl:when test="contains($fake_path,'index.php')"><xsl:value-of select="substring-before($fake_path,'index.php')"/></xsl:when>
																	<xsl:otherwise><xsl:value-of select="$fake_path"/></xsl:otherwise>
																</xsl:choose><xsl:value-of select="filteroptions/option[.=//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option[position()=1]]/@value" disable-output-escaping="yes"/></xsl:attribute>
																<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option[position()=1]" disable-output-escaping="yes"/>
																</a>
															</xsl:when>
														<xsl:otherwise><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option[position()=1]" disable-output-escaping="yes"/></xsl:otherwise>
													</xsl:choose>
													</xsl:otherwise>
												</xsl:choose></xsl:when>
												<xsl:otherwise>
													<ul>
														<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/option">
														<li class="plain"><xsl:choose>
															<xsl:when test="@link='1'"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="$entry_url"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></a></xsl:when>
															<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
														</xsl:choose></li>
														</xsl:for-each>
													</ul>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/@type='email'">
										<a><xsl:attribute name="href"><xsl:choose>
											<xsl:when test="//setting[@name='sp_use_antispam']!='Yes'">mailto:</xsl:when>
											<xsl:otherwise><xsl:value-of select="//setting[@name='base']"/>-/-anti-spam.php?to=</xsl:otherwise>
											</xsl:choose><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]" disable-output-escaping="yes"/>
											</a>
										</xsl:when>
										<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]/@type='datetime'">
											<xsl:call-template name="format_date">
												<xsl:with-param name="current_date"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]" disable-output-escaping="yes"/></xsl:with-param>
												<xsl:with-param name="output_format">d DxxRMMM YYYYrhour:minutes</xsl:with-param>
											</xsl:call-template>
										</xsl:when>
										<xsl:otherwise>
											<xsl:variable name="content"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[position()=$pos]/field[@name=$id]"/></xsl:variable>
											<xsl:choose>
												<xsl:when test="string-length($content)=0">[[nbsp]]</xsl:when>
												<xsl:otherwise>
													<xsl:choose>
														<xsl:when test="@link=1"><a><xsl:attribute name='href'><xsl:choose>
														<xsl:when test="substring($entry_url,1,1)='/'"><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/><xsl:value-of select="$entry_url"/></xsl:when>
														<xsl:otherwise><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/>/<xsl:value-of select="$entry_url"/></xsl:otherwise>
														</xsl:choose></xsl:attribute><xsl:value-of select="$content"/></a></xsl:when>
														<xsl:otherwise><xsl:value-of select="$content"/></xsl:otherwise>
													</xsl:choose>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:otherwise>
									</xsl:choose>
								</div>
							</xsl:for-each>
							</td>
						</xsl:for-each>
					</tr>
				</xsl:for-each>
			</table>
			<xsl:if test="boolean(content/info[@list=$directory_identifier]/results/pages/page=2)">
			<ul class="pages">
				<li>Go to page</li>
				<xsl:for-each select="content/info[@list=$directory_identifier]/results/pages/page">
					<xsl:variable name="path"><xsl:value-of select="//setting[@name='fake_script']"/>/_page<xsl:value-of select="."/>.php</xsl:variable>
					<xsl:variable name="index"><xsl:value-of select="//setting[@name='fake_script']"/>/_page1.php</xsl:variable>
					<xsl:choose>
						<xsl:when test="//setting[@name='real_script']=$path or ( $index=$path and contains(//setting[@name='real_script'],'index.php'))">
							<li><xsl:value-of select="."/></li>
						</xsl:when>
						<xsl:when test="$index=$path">
							<li><a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/index.php</xsl:attribute>
								<xsl:value-of select="."/></a></li>
						</xsl:when>
						<xsl:otherwise>
							<li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/_page<xsl:value-of select="."/>.php</xsl:attribute><xsl:value-of select="."/></a></li>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</ul>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
		<xsl:if test="count(content/info[@list=$directory_identifier]/results/entry)=1">
		<h1 class='entrylocation'><span><xsl:value-of select="content/info[@list=$directory_identifier]/results/entry/field[@name='ie_title']"/></span></h1>
		</xsl:if>
			<xsl:for-each select="content/info[@list=$directory_identifier]/results/entry">
				<div><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="(position() mod 2)=0">tablecellalt</xsl:when>
					<xsl:otherwise>tablecell</xsl:otherwise>
				</xsl:choose></xsl:attribute>
				<xsl:call-template name="display_entry">
					<xsl:with-param name="link_to_real_url"><xsl:value-of select="$link_to_real_url"/></xsl:with-param>
					<xsl:with-param name="link_to_real_url_link"><xsl:value-of select="$link_to_real_url_link"/></xsl:with-param>
					<xsl:with-param name="fake_path"><xsl:value-of select="$uri"/></xsl:with-param>
					<xsl:with-param name="entry_identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
					<xsl:with-param name="directory_identifier"><xsl:value-of select="$directory_identifier"/></xsl:with-param>
					<xsl:with-param name="entry_type"><xsl:value-of select="$entry_type"/></xsl:with-param>
				</xsl:call-template>
				</div>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_entry">
	<xsl:param name="fake_path"></xsl:param>
	<xsl:param name="entry_identifier">-1</xsl:param>
	<xsl:param name="entry_type"></xsl:param>
	<xsl:param name="directory_identifier">-1</xsl:param>
	<xsl:param name="link_to_real_url">1</xsl:param>
	<xsl:param name="link_to_real_url_link"></xsl:param>
	<xsl:param name="feature">0</xsl:param>
	
	<xsl:variable name="real_id"><xsl:value-of select="@real_id"/></xsl:variable>
	<xsl:variable name="user"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/@user"/></xsl:variable>
	<xsl:variable name="can_buy"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name='ie_canbuy']"/></xsl:variable>
	<xsl:if test="//session[@user_identifier=$user]">
		<ul class='entryoptions'>
			<li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=INFORMATION_EDIT_ENTRY&amp;list=<xsl:value-of select="$directory_identifier"/>&amp;identifier=<xsl:value-of select="//entry[@user=$user]/@identifier"/></xsl:attribute><span class='icon'><span class='text'>Edit Entry</span></span></a></li>
		</ul>
	</xsl:if>	
	<div class="directoryentry">
	<xsl:for-each select="//content/info[@list=$directory_identifier]/display/seperator_row">
		<xsl:call-template name="display_entry_row">
			<xsl:with-param name="fake_path"><xsl:value-of select="$fake_path"/></xsl:with-param>
			<xsl:with-param name="link_to_real_url"><xsl:value-of select="$link_to_real_url"/></xsl:with-param>
			<xsl:with-param name="link_to_real_url_link"><xsl:value-of select="$link_to_real_url_link"/></xsl:with-param>
			<xsl:with-param name="entry_type"><xsl:value-of select="$entry_type"/></xsl:with-param>
			<xsl:with-param name="entry_identifier"><xsl:value-of select="$entry_identifier"/></xsl:with-param>
			<xsl:with-param name="directory_identifier"><xsl:value-of select="$directory_identifier"/></xsl:with-param>
			<xsl:with-param name="can_buy"><xsl:value-of select="$can_buy"/></xsl:with-param>
			<xsl:with-param name="real_id"><xsl:value-of select="$real_id"/></xsl:with-param>
			<xsl:with-param name="feature"><xsl:value-of select="$feature"/></xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
	</div>
</xsl:template>

<xsl:template name="display_entry_row">
	<xsl:param name="fake_path"></xsl:param>
	<xsl:param name="entry_identifier">-1</xsl:param>
	<xsl:param name="entry_type"></xsl:param>
	<xsl:param name="link_to_real_url">1</xsl:param>
	<xsl:param name="link_to_real_url_link"></xsl:param>
	<xsl:param name="can_buy">0</xsl:param>
	<xsl:param name="directory_identifier">-1</xsl:param>
	<xsl:param name="real_id">-1</xsl:param>
	<xsl:param name="feature">0</xsl:param>
	<div class='row'>
		<xsl:variable name="count_children"><xsl:value-of select="count(seperator)"/></xsl:variable>
		<xsl:for-each select="seperator">
			<xsl:call-template name="display_entry_cell">
				<xsl:with-param name="fake_path"><xsl:value-of select="$fake_path"/></xsl:with-param>
				<xsl:with-param name="link_to_real_url"><xsl:value-of select="$link_to_real_url"/></xsl:with-param>
				<xsl:with-param name="link_to_real_url_link"><xsl:value-of select="$link_to_real_url_link"/></xsl:with-param>
				<xsl:with-param name="count_children"><xsl:value-of select="$count_children"/></xsl:with-param>
				<xsl:with-param name="entry_identifier"><xsl:value-of select="$entry_identifier"/></xsl:with-param>
				<xsl:with-param name="entry_type"><xsl:value-of select="$entry_type"/></xsl:with-param>
				<xsl:with-param name="directory_identifier"><xsl:value-of select="$directory_identifier"/></xsl:with-param>
				<xsl:with-param name="can_buy"><xsl:value-of select="$can_buy"/></xsl:with-param>
				<xsl:with-param name="real_id"><xsl:value-of select="$real_id"/></xsl:with-param>
				<xsl:with-param name="feature"><xsl:value-of select="$feature"/></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
		</div>
</xsl:template>

<xsl:template name="display_entry_cell">
	<xsl:param name="entry_identifier">-1</xsl:param>
	<xsl:param name="entry_type"></xsl:param>
	<xsl:param name="fake_path"></xsl:param>
	<xsl:param name="link_to_real_url">1</xsl:param>
	<xsl:param name="link_to_real_url_link"></xsl:param>
	<xsl:param name="count_children">-1</xsl:param>
	<xsl:param name="directory_identifier">-1</xsl:param>
	<xsl:param name="can_buy">0</xsl:param>
	<xsl:param name="real_id">-1</xsl:param>
	<xsl:param name="feature">0</xsl:param>
	
	<xsl:variable name="displayLocation"><xsl:choose>
		<xsl:when test="$feature=1"></xsl:when>
		<xsl:when test="contains(//setting[@name='real_script'],'index.php')">sumlabel</xsl:when>
		<xsl:otherwise>conlabel</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="quantity"><xsl:value-of select="//metadata[@linkto=$real_id]/quantity"/></xsl:variable>
	<div><xsl:attribute name="class">columncount<xsl:value-of select="$count_children"/></xsl:attribute>
	<xsl:for-each select="field">
		<xsl:variable name="name"><xsl:value-of select="@id"/></xsl:variable>
		<xsl:variable name="filterable"><xsl:choose><xsl:when test="@filter='1'">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose></xsl:variable>
		<xsl:variable name="entry_url"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name='uri' and @link='no' and @visible='no']"/></xsl:variable>
		<xsl:if test="@id='__add_to_basket__'">
			<xsl:choose>
				<xsl:when test="$can_buy=0"></xsl:when>
				<xsl:when test="$quantity=0"></xsl:when>
				<xsl:otherwise><a><xsl:attribute name="href">_add-to-cart.php?identifier=<xsl:value-of select="$entry_identifier"/>&amp;type=<xsl:value-of select="$entry_type"/></xsl:attribute>Add to Basket</a></xsl:otherwise>
			</xsl:choose>
		</xsl:if>			
		<xsl:if test="@id='__category__'">
			<div class="entry">
				<xsl:if test="(($displayLocation='sumlabel' and @sumlabel!=0) or ($displayLocation='conlabel' and @conlabel!=0))">
				<div>
					<xsl:attribute name="class"><xsl:choose>
						<xsl:when test="(($displayLocation='sumlabel' and @sumlabel!=1) or ($displayLocation='conlabel' and @conlabel!=1) or (@displaylabel!=1))">directoryLabelTop</xsl:when>
						<xsl:otherwise>directoryLabelLeft</xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<xsl:value-of select="//module/cat_label"/>
				</div>
				</xsl:if>
				<div>
				<xsl:attribute name="class"><xsl:choose>
					<xsl:when test="(($displayLocation='sumlabel' and @sumlabel!=1) or ($displayLocation='conlabel' and @conlabel!=1)  or (@displaylabel!=1))">directoryContentBottom</xsl:when>
					<xsl:when test="(($displayLocation='sumlabel' and @sumlabel!=0) and ($displayLocation='conlabel' and @conlabel!=0) or (@displaylabel!=0))">directoryContent</xsl:when>
					<xsl:otherwise>directoryContentRight</xsl:otherwise>
				</xsl:choose></xsl:attribute>
				<xsl:choose>
					<xsl:when test="count(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory)!=1">
						<ul class='categories'>
						<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory">
							<xsl:variable name='cc'><xsl:value-of select="@identifier"/></xsl:variable>
							<xsl:choose>
								<xsl:when test="$link_to_real_url=1">
									<li><a><xsl:attribute name="href"><xsl:choose>
										<xsl:when test="//cat_path[@id=$cc]"><xsl:value-of select="//cat_path[@id=$cc]"/></xsl:when>
										<xsl:when test="substring(//categorylist/category[@identifier=$cc]/uri,1,1)='/'"><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/><xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/>/<xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:otherwise>
									</xsl:choose></xsl:attribute><xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/></a></li>
								</xsl:when>
								<xsl:otherwise>
									<li><xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/></li>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						<ul class='categories'>
						<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory">
							<xsl:variable name='cc'><xsl:value-of select="@identifier"/></xsl:variable>
							<xsl:choose>
								<xsl:when test="$link_to_real_url=1">
									<li><a><xsl:attribute name="href"><xsl:choose>
										<xsl:when test="substring(//categorylist/category[@identifier=$cc]/uri,1,1)='/'"><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/><xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="substring-before(//setting[@name='script'],'/index.php')"/>/<xsl:value-of select="//categorylist/category[@identifier=$cc]/uri"/></xsl:otherwise>
									</xsl:choose></xsl:attribute><xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/></a></li>
								</xsl:when>
								<xsl:otherwise>
									<li><xsl:value-of select="//categorylist/category[@identifier=$cc]/label"/></li>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						</ul>
					</xsl:otherwise>
				</xsl:choose>
				</div>
			</div>
		</xsl:if>
		<xsl:if test="string-length(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name])!=0 or boolean(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name])">
			<xsl:variable name="show_field"><xsl:if test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name and @type='URL']/maps=''">1</xsl:if><xsl:if test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type!='URL'">1</xsl:if></xsl:variable>
			<xsl:if test="$show_field=1">
				<div class="entry">
				<xsl:if test="(($displayLocation='sumlabel' and @sumlabel!=0) or ($displayLocation='conlabel' and @conlabel!=0) or ($displayLocation='' and @displaylabel!=0))">
					<div>
						<xsl:attribute name="class"><xsl:choose>
							<xsl:when test="(($displayLocation='sumlabel' and @sumlabel!=1) or ($displayLocation='conlabel' and @conlabel!=1) or ($displayLocation='' and @displaylabel!=1))">directoryLabelTop</xsl:when>
							<xsl:otherwise>directoryLabelLeft</xsl:otherwise>
						</xsl:choose></xsl:attribute>
					<xsl:value-of select="label"/></div>
					</xsl:if>
					<div>
						<xsl:attribute name="class"><xsl:choose>
							<xsl:when test="(($displayLocation='sumlabel' and @sumlabel!=1) or ($displayLocation='conlabel' and @conlabel!=1) or ($displayLocation='' and @displaylabel!=1))">directoryContentBottom</xsl:when>
							<xsl:otherwise>directoryContentRight</xsl:otherwise>
						</xsl:choose></xsl:attribute>
					<xsl:comment> <xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type"/></xsl:comment>
					<xsl:choose>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='LIST'">
							<xsl:choose>
								<xsl:when test="count(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/option)=1"><xsl:choose>
									<xsl:when test="@link='1' and $link_to_real_url=1"><a><xsl:attribute name="href"><xsl:choose>
										<xsl:when test="$fake_path!=''"><xsl:value-of select="substring-before($fake_path,'/index.php')"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="//setting[@name='fake_script']"/></xsl:otherwise>
										</xsl:choose>/<xsl:value-of select="$entry_url"/></xsl:attribute>
											<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/option[position()=1]" disable-output-escaping="yes"/>
										</a></xsl:when>
									<xsl:otherwise><xsl:choose>
										<!-- check filter -->
										<xsl:when test="$filterable=1 and $link_to_real_url=1">
										<a>
										<xsl:attribute name="href"><xsl:choose>
											<xsl:when test="contains($fake_path,'index.php')"><xsl:value-of select="substring-before($fake_path,'index.php')"/></xsl:when>
											<xsl:otherwise><xsl:value-of select="$fake_path"/></xsl:otherwise>
										</xsl:choose><xsl:value-of select="filteroptions/option[.=//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/option[position()=1]]/@value" disable-output-escaping="yes"/></xsl:attribute>
										<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/option[position()=1]" disable-output-escaping="yes"/>
										</a>
										</xsl:when>
										<xsl:otherwise><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/option[position()=1]" disable-output-escaping="yes"/></xsl:otherwise>
									</xsl:choose></xsl:otherwise>
									</xsl:choose></xsl:when>
								<xsl:otherwise>
									<ul>
										<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/option">
											<li class="plain"><xsl:choose>
												<xsl:when test="@link='1' and $link_to_real_url=1"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="$entry_url"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></a></xsl:when>
												<xsl:otherwise><xsl:choose>
													<xsl:when test="$filterable=1">
														<xsl:variable name="v"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:variable>
														<xsl:variable name="h"><xsl:value-of select="//filteroptions/option[.=$v]/@value" disable-output-escaping="yes"/></xsl:variable>
														<a><xsl:attribute name="href"><xsl:value-of select="substring-before(//setting[@name='script'],'index.php')"/><xsl:value-of select="$h" disable-output-escaping="yes"/></xsl:attribute>
														<xsl:value-of select="$v" disable-output-escaping="yes"/></a></xsl:when>
													<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
												</xsl:choose></xsl:otherwise>
											</xsl:choose></li>
										</xsl:for-each>
									</ul>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='associations'">
							<ul class="dirfile">
								<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/files/file">
									<li>
										<a>
										<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="$entry_url"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
										<xsl:attribute name="title">Download the file '<xsl:value-of select="label"/>' now approximate download time on a 56k modem (<xsl:value-of select="download_time"/>)</xsl:attribute><xsl:value-of select="label" disable-output-escaping="yes"/></a> (<xsl:value-of select="size"/>) 
									</li>
								</xsl:for-each>
							</ul>
						</xsl:when>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='imageembed'"><xsl:choose>
								<xsl:when test="boolean(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file)"><xsl:choose>
									<xsl:when test="boolean(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file)"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/md5"/></xsl:attribute>
										<img>
											<xsl:attribute name='src'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/directory" disable-output-escaping="yes"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/md5" disable-output-escaping="yes"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/ext" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:attribute name='alt'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/label" disable-output-escaping="yes"/> (click for larger)</xsl:attribute>
											<xsl:attribute name='width'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/width" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:attribute name='height'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/height" disable-output-escaping="yes"/></xsl:attribute>
										</img>
									</a></xsl:when>
									<xsl:otherwise>
										<img>
											<xsl:attribute name='src'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/directory" disable-output-escaping="yes"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/md5" disable-output-escaping="yes"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/ext" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:attribute name='alt'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/label" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:attribute name='width'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/width" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:attribute name='height'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/thumb/files/file/height" disable-output-escaping="yes"/></xsl:attribute>
										</img>
									</xsl:otherwise>
									</xsl:choose>
								</xsl:when>
								<xsl:when test="boolean(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file)"><img>
										<xsl:attribute name='src'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file/directory" disable-output-escaping="yes"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file/md5" disable-output-escaping="yes"/><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file/ext" disable-output-escaping="yes"/></xsl:attribute>
										<xsl:attribute name='alt'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file/label" disable-output-escaping="yes"/></xsl:attribute>
										<xsl:attribute name='width'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file/width" disable-output-escaping="yes"/></xsl:attribute>
										<xsl:attribute name='height'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/main/files/file/height" disable-output-escaping="yes"/></xsl:attribute>
									</img></xsl:when>
								<xsl:otherwise></xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='image'">
							<xsl:for-each select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name and @type='image']/files/file">
								<img>
									<xsl:attribute name='src'><xsl:value-of select="directory" disable-output-escaping="yes"/><xsl:value-of select="md5" disable-output-escaping="yes"/><xsl:value-of select="ext" disable-output-escaping="yes"/></xsl:attribute>
									<xsl:attribute name='alt'><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:attribute>
									<xsl:attribute name='width'><xsl:value-of select="width" disable-output-escaping="yes"/></xsl:attribute>
									<xsl:attribute name='height'><xsl:value-of select="height" disable-output-escaping="yes"/></xsl:attribute>
								</img>
							</xsl:for-each>
						</xsl:when>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='email'">
									<a><xsl:attribute name="href"><xsl:choose>
										<xsl:when test="//setting[@name='sp_use_antispam']!='Yes'">mailto:</xsl:when>
										<xsl:otherwise><xsl:value-of select="//setting[@name='base']"/>-/-anti-spam.php?to=</xsl:otherwise>
										</xsl:choose><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]" disable-output-escaping="yes"/></xsl:attribute>
											<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]" disable-output-escaping="yes"/>
										</a>
						</xsl:when>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='URL'">
							<xsl:variable name="item_uri"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/value"/></xsl:variable>
							<a>
								<xsl:attribute name="href"><xsl:choose>
									<xsl:when test="contains($item_uri,'http:')"><xsl:value-of select="$item_uri" disable-output-escaping="yes"/></xsl:when>
									<xsl:when test="contains($item_uri,'ftp:')"><xsl:value-of select="$item_uri" disable-output-escaping="yes"/></xsl:when>
									<xsl:otherwise>http://<xsl:value-of select="$item_uri" disable-output-escaping="yes"/></xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/value" disable-output-escaping="yes"/></a>
						</xsl:when>
						<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/@type='datetime'">
							<xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]" disable-output-escaping="yes"/></xsl:with-param>
								<xsl:with-param name="output_format">Dxx MMMM YYYY</xsl:with-param>
							</xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="cid"><xsl:choose>
								<xsl:when test="boolean(//category/@identifier=@identifier) = false()"><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory/@identifier"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/choosencategory[//category/@identifier=@identifier]/@identifier"/></xsl:otherwise>
							</xsl:choose></xsl:variable>
							<xsl:choose>
								<xsl:when test="@link='1' and $link_to_real_url=1">
									<xsl:choose>
										<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier and boolean(//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]/field[@type='URL' and maps=$name and value!=''])]">
											<xsl:variable name='url'><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@type='URL' and maps=$name]/value"/></xsl:variable>
											<a>
												<xsl:attribute name="href"><xsl:if test="not(contains($url,'http'))">http://</xsl:if><xsl:value-of select="$url"/></xsl:attribute>
												<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/>
												</a>
										</xsl:when>
										<xsl:otherwise><a><xsl:attribute name="href"><xsl:choose>
											<xsl:when test="boolean(//cat_path[@id=$cid])"><xsl:value-of select="//cat_path[@id=$cid]"/></xsl:when>
											<xsl:when test="$fake_path!=''"><xsl:value-of select="substring-before($fake_path, 'index.php')"/><xsl:value-of select="substring-before(//category[@identifier = $cid]/uri,'/index.php')"/></xsl:when>
											<xsl:otherwise><xsl:value-of select="//setting[@name='fake_script']"/></xsl:otherwise>
										</xsl:choose>/<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name='uri' and @link='no' and @visible='no']"/></xsl:attribute><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/></a></xsl:otherwise>
										</xsl:choose>
									</xsl:when>
									<xsl:when test="@link='1' and $link_to_real_url=0">
										<xsl:variable name='url'><xsl:value-of select="$link_to_real_url_link"/></xsl:variable>
										<a>
											<xsl:attribute name="href"><xsl:value-of select="$url"/>?identifier=<xsl:value-of select="$entry_identifier"/></xsl:attribute>
											<xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/>
										</a>
									</xsl:when>
									<xsl:otherwise>
										<xsl:choose>
											<xsl:when test="@type='URL' and maps='' and $link_to_real_url=1"><a><xsl:attribute name='href'><xsl:if test="not(contains(value,'http'))">http://</xsl:if><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/></xsl:attribute><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/></a></xsl:when>
											<xsl:when test="@type='URL' and $link_to_real_url=1"><xsl:if test="maps=''"><a><xsl:attribute name='href'><xsl:if test="not(contains(value,'http'))">http://</xsl:if><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/></xsl:attribute><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/></a></xsl:if></xsl:when>
											<xsl:otherwise>
												<xsl:choose>
													<xsl:when test="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier and boolean(//field[@type='URL' and maps=$name and value!=''])] and $link_to_real_url=1"><xsl:variable name='url'><xsl:value-of select="//entry[@identifier=$entry_identifier]//field[@type='URL' and maps=$name]/value"/></xsl:variable><a><xsl:attribute name="href"><xsl:if test="not(contains($url,'http'))">http://</xsl:if><xsl:value-of select="$url"/></xsl:attribute><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field"/></a></xsl:when>
													<xsl:otherwise><xsl:value-of select="//content/info[@list=$directory_identifier]/results/entry[@identifier=$entry_identifier]/field[@name=$name]"/></xsl:otherwise>
												</xsl:choose>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</div>
			</xsl:if>
		</xsl:if>
	</xsl:for-each>
	</div>
</xsl:template>

<xsl:template name="build_directory_path">
	<xsl:param name="identifier">-1</xsl:param>
	<xsl:if test="$identifier!=//module/list/@identifier"><xsl:call-template name="build_directory_path">
		<xsl:with-param name="identifier"><xsl:value-of select="//category[@identifier=$identifier]/@parent"/></xsl:with-param>
	</xsl:call-template><xsl:value-of select="translate(translate(//category[@identifier=$identifier]/label,'/&amp;','-'),' /ABCDEFGHIJKLMNOPQRSTUVWXYZ,.','--abcdefghijklmnopqrstuvwxyz')"/>/</xsl:if></xsl:template>

<xsl:template name="build_directory_breadcrumb_tmp">
	<xsl:param name="identifier">-1</xsl:param>
	<xsl:param name="uri"></xsl:param>
	<xsl:variable name="id"><xsl:choose>
		<xsl:when test="//module[@display='search_results']/list"><xsl:value-of select="//module[@display='search_results']/list/@identifier"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="//module[@display='INFORMATION']/list/@identifier"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name='category'><xsl:value-of select="//module[@name='information_presentation']/content/@category"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="boolean(//categorylist[@parent=$category])">
			<xsl:for-each select="//categorylist[@parent=$category]/bread/crumb">[[rightarrow]] <a class='breadcrumb'>
				<xsl:attribute name="href"><xsl:value-of select="substring-before(//module[@display='INFORMATION']/fake_uri,'index.php')"/><xsl:value-of select="path"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute>
				<xsl:value-of select="label"/></a>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise></xsl:otherwise>
	</xsl:choose>
	<xsl:choose>
		<xsl:when test="not(//module[@display='INFORMATION']/categorylist[@parent=//module[@display='INFORMATION']/current_category])">
			[[rightarrow]] <a class='breadcrumb'>
			<xsl:attribute name="href"><xsl:value-of select="substring-before(//module[@display='INFORMATION']/fake_uri,'index.php')"/><xsl:value-of select="//module[@display='INFORMATION']/categorylist/category[@identifier=//module[@display='INFORMATION']/current_category]/uri"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="//module[@display='INFORMATION']/categorylist/category[@identifier=//module[@display='INFORMATION']/current_category]/label"/></xsl:attribute>
			<xsl:value-of select="//module[@display='INFORMATION']/categorylist/category[@identifier=//module[@display='INFORMATION']/current_category]/label"/></a>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="build_directory_breadcrumb">
	<xsl:param name="identifier">-1</xsl:param>
	<xsl:param name="uri"></xsl:param>
	<xsl:variable name="id"><xsl:choose>
		<xsl:when test="//module[@display='search_results']/list"><xsl:value-of select="//module[@display='search_results']/list/@identifier"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="//module[@display='INFORMATION']/list/@identifier"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name='category'><xsl:value-of select="//module[@name='information_presentation']/content/@category"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="boolean(//categorylist[@parent=$category])">
			<xsl:comment> found [<xsl:value-of select="$category"/>]</xsl:comment>
			<xsl:for-each select="//categorylist[@parent=$category]/bread/crumb">[[rightarrow]] <a class='breadcrumb'>
				<xsl:attribute name="href"><xsl:value-of select="substring-before(//module[@display='INFORMATION']/fake_uri,'index.php')"/><xsl:value-of select="path"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute>
				<xsl:value-of select="label"/></a>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			<xsl:comment> not found </xsl:comment>
			<xsl:if test="count(//module/categorylist)=1">
				<xsl:for-each select="//module/categorylist/bread/crumb"> [[rightarrow]] <a class='breadcrumb'>
					<xsl:attribute name="href"><xsl:value-of select="substring-before(//module[@display='INFORMATION']/fake_uri,'index.php')"/><xsl:value-of select="path"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute>
					<xsl:value-of select="label"/></a>
				</xsl:for-each>
			</xsl:if>
			[[rightarrow]] <a class='breadcrumb'>
			<xsl:attribute name="href"><xsl:value-of select="substring-before(//module[@display='INFORMATION']/fake_uri,'index.php')"/><xsl:value-of select="//module[@display='INFORMATION']/categorylist/category[@identifier=//module[@display='INFORMATION']/current_category]/uri"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="//module[@display='INFORMATION']/categorylist/category[@identifier=//module[@display='INFORMATION']/current_category]/label"/></xsl:attribute>
			<xsl:value-of select="//module[@display='INFORMATION']/categorylist/category[@identifier=//module[@display='INFORMATION']/current_category]/label"/></a>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_a2z_widget">
<xsl:variable name="uri"><xsl:value-of select="uri"/></xsl:variable>
<ul class='a2zwidget'>
	<li class='label'><xsl:value-of select="label"/></li>
<xsl:for-each select="letters/letter">
	<li class='letter'><xsl:choose>
		<xsl:when test="@count!='0'"><a><xsl:attribute name='href'><xsl:value-of select="$uri"/>_<xsl:value-of select="@lcase"/>.php</xsl:attribute>
		<xsl:attribute name='title'><xsl:value-of select="@lcase"/></xsl:attribute>
		<xsl:value-of select="."/></a></xsl:when>
		<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
	</xsl:choose></li>
</xsl:for-each>
</ul>
</xsl:template>

</xsl:stylesheet>
