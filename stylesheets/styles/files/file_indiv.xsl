<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.13 $
- Modified $Date: 2005/03/02 11:48:47 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_files">
	<xsl:param name="file_download_style"><xsl:value-of select="//setting[@name='file_list_format']"/></xsl:param>
	<xsl:param name="show_msg">1</xsl:param>
	<xsl:variable name="property_identifier"><xsl:value-of select="../property/@id"/></xsl:variable>
	<xsl:comment>display_files</xsl:comment>
<!--	[<xsl:value-of select="$file_download_style"/>] -->
	<xsl:if test=".//group">
		<script src="/libertas_images/javascripts/hide_grouped_downloads.js" type="text/javascript"><xsl:comment> load the download javascript </xsl:comment></script>
	</xsl:if>
<!--	[<xsl:value-of select="$file_download_style"/>] -->
	<xsl:if test="boolean(files/group)">
		<table class='filetable' cellspacing="0" cellpadding="0" summary="filter by year">
			<tr>
				<xsl:for-each select="files/group">
				<td ><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="position()=1">downloadlinkon</xsl:when>
					<xsl:otherwise>downloadlinkoff</xsl:otherwise>
				</xsl:choose></xsl:attribute>
					<xsl:if test="boolean(group)">
					<xsl:attribute name='id'>dl_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
					</xsl:if>
					<a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='real_url']"/>#dl_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute><xsl:value-of select="@label"/></a>
				</td>
				</xsl:for-each>
				<td style="width:100%">[[nbsp]]</td>
			</tr>
		</table>
			<xsl:if test="files/group/group">
				<xsl:for-each select="files/group">
			<table class='filetable' cellspacing="0" cellpadding="0" summary="filter by month of year">
				<tr class='downloadrow'>
					<xsl:attribute name='id'>li_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
					<xsl:for-each select="group">
					<td class='entry'>
						<xsl:attribute name='id'>li2_<xsl:value-of select="../@label"/>_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
						<a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='real_url']"/>#dl2_<xsl:value-of select="../@label"/>_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute><xsl:value-of select="@label"/></a></td>
					</xsl:for-each>
					<td style="width:100%">[[nbsp]]</td>
				</tr>
			</table>
				</xsl:for-each>
			</xsl:if>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="$file_download_style='LIST'">
			<xsl:choose>
				<xsl:when test="boolean(files/group)">
					<xsl:if test="count(file)!=1">
						<div class="filetitle"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></div>
					</xsl:if>
			    	<xsl:for-each select="files/group">
							<xsl:choose>
								<xsl:when test="group">
									<xsl:for-each select="group">
										<table summary="this table holds the title, summary, size of file and download time" class='filetable'>
											<xsl:attribute name='id'>dl2_<xsl:value-of select="../@label"/>_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
						    		<xsl:for-each select="file">
										<tr>
					    					<td class='fileicon'><img style="width:32px;height:32px"><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
											<td class='filedes'><a>
												<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
												<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute><xsl:value-of select="label"/></a>
												<br/>File size : <span class="filedescription"><xsl:value-of select="size"/></span><br/>Download time on (56k) : <span class="filedescription"><xsl:value-of select="download_time"/></span><br/>Date uploaded : <span class="filedescription"><xsl:value-of select="substring-before(date,' ')"/></span></td>
										</tr>
				   					</xsl:for-each>
									</table>
		    						</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<table summary="this table holds the title, summary, size of file and download time" width="100%" class='filetable'>
									<xsl:attribute name='id'>dl_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
						    		<xsl:for-each select="file">
										<tr>
					    					<td class='fileicon'><img style="width:32px;height:32px"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
											<td class='filedes'><a>
												<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
												<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute><xsl:value-of select="label"/></a>
												<br/>File size : <span class="filedescription"><xsl:value-of select="size"/></span><br/>Download time on (56k) : <span class="filedescription"><xsl:value-of select="download_time"/></span><br/>Date uploaded : <span class="filedescription"><xsl:value-of select="substring-before(date,' ')"/></span></td>
										</tr>
				   					</xsl:for-each>
									</table>
								</xsl:otherwise>
							</xsl:choose>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="count(files/file)!=1">
						<div class="filetitle"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></div>
					</xsl:if>
		    		<xsl:for-each select="files/file">
						<div>
	    					<div class='fileicon'><img style="width:32px;height:32px"><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></div>
							<div class='filedes'><a>
								<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute><xsl:value-of select="label"/></a>
								<br/>File size : <span class="filedescription"><xsl:value-of select="size"/></span><br/>Download time on (56k) : <span class="filedescription"><xsl:value-of select="download_time"/></span><br/>Date uploaded : <span class="filedescription"><xsl:value-of select="substring-before(date,' ')"/></span></div>
						</div>
   					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="$file_download_style='TABLE'">
			<xsl:choose>
				<xsl:when test="boolean(files/group)">
					<xsl:if test="count(file)!=1">
						<div class="filetitle"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></div>
					</xsl:if>
			    	<xsl:for-each select="files/group">
							<xsl:choose>
								<xsl:when test="group">
									<xsl:for-each select="group">
										<table summary="this table holds the title, summary, size of file and download time" width="100%" class='filetable'>
											<xsl:attribute name='id'>dl2_<xsl:value-of select="../@label"/>_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
											<tr><th scope="col"  class='alignleft' colspan="2">Filename</th><th  class='alignleft' scope="col" style='width:60'>Size</th><th  class='alignright' scope="col" style='width:100px'>Time</th></tr>
								    		<xsl:for-each select="file">
									    		<tr>
													<td valign="top" style="width:20px"><img style="width:16px;height:16px"><xsl:attribute name="width">16</xsl:attribute><xsl:attribute name="height">16</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute></img></td>
													<td><a>
													<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
													<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
													<xsl:value-of select="label"/></a></td>
													<td valign="top"><span class="filedescription"><xsl:value-of select="size"/></span></td>
													<td valign="top" class='alignright'><span class="filedescription"><xsl:value-of select="download_time"/></span></td>
												</tr>
						   					</xsl:for-each>
									</table>
		    						</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<table summary="this table holds the title, summary, size of file and download time" width="100%" class='filetable'>
									<xsl:attribute name='id'>dl_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
										<tr><th scope="col"  class='alignleft' colspan="2">Filename</th><th  class='alignleft' scope="col" style='width:60'>Size</th><th  class='alignright' scope="col" style='width:100'>Time</th></tr>
							    		<xsl:for-each select="file">
								    		<tr>
												<td valign="top" style="width:20px"><img style="width:16px;height:16px"><xsl:attribute name="width">16</xsl:attribute><xsl:attribute name="height">16</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute></img></td>
												<td><a>
													<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
													<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
													<xsl:value-of select="label"/></a></td>
												<td valign="top"><span class="filedescription"><xsl:value-of select="size"/></span></td>
												<td valign="top" class='alignright'><span class="filedescription"><xsl:value-of select="download_time"/></span></td>
											</tr>
					   					</xsl:for-each>
									</table>
								</xsl:otherwise>
							</xsl:choose>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
			<xsl:if test="count(files/file)!=1">
			<div class="filetitle"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></div>
			</xsl:if>
			<table summary="This table holds the title, summary, size of file and download time" class="filetable">
			<tr><th scope="col"  class='alignleft' colspan="2">Filename</th><th  class='alignleft' scope="col" style='width:60'>Size</th><th  class='alignright' scope="col" style='width:100'>Time</th></tr>
	    	<xsl:for-each select="files/file">
    		<tr>
				<td valign="top" style="width:20px"><img style="width:16px;height:16px"><xsl:attribute name="width">16</xsl:attribute><xsl:attribute name="height">16</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute></img></td>
				<td><a>
				<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:value-of select="label"/></a></td>
				<td valign="top"><span class="filedescription"><xsl:value-of select="size"/></span></td>
				<td valign="top" class='alignright'><span class="filedescription"><xsl:value-of select="download_time"/></span></td>
			</tr>
    		</xsl:for-each>
			</table>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="$file_download_style='TITLE AND SUMMARY'">
			<xsl:if test="count(files/file)!=1 and $show_msg=1">
				<div class="filetitle"><p><xsl:choose><xsl:when test="position()=1"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></xsl:when><xsl:otherwise>[[nbsp]]</xsl:otherwise></xsl:choose></p></div>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="boolean(files/group)">
			    	<xsl:for-each select="files/group">
						<xsl:choose>
							<xsl:when test="group">
								<xsl:for-each select="group">

								<table summary="this table holds the title, size of file and download time" width="96%" cellspacing="1" cellpadding="3" class='downloaddata'>
									<xsl:attribute name='id'>dl2_<xsl:value-of select="../@label"/>_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
									<xsl:for-each select="file">
										<xsl:if test="position()=1">
										<tr>
											<th colspan="2">Filename</th>
											<th class='alignright'>Download time</th>
										</tr>
										</xsl:if>
					    				<tr class="filecells">
											<td valign="top" rowspan="2" class="imgicon"><img style="width:32px;height:32px"><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
											<td><a>
												<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
												<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
												<xsl:value-of select="label"/>
											</a></td>
											<td valign="top"  class='alignright'><span class="filedescription"><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/></span></td>
										</tr>
										<tr class="filecells">
											<td valign="top" colspan="2"><span class="filedescription"><xsl:value-of select="description"/></span></td>
										</tr>
				    					</xsl:for-each>
								    </table>
				    					</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
									<table summary="this table holds the title, size of file and download time" width="96%" cellspacing="1" cellpadding="3" class='downloaddata'>
										<xsl:attribute name='id'>dl_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="../../../property/@id"/></xsl:attribute>
								<xsl:for-each select="file">
									<tr>
										<th colspan="2">Filename</th>
										<th class='alignright'>Download time</th>
									</tr>
				    				<tr class="filecells">
										<td valign="top" rowspan="2" class="imgicon"><img style="width:32px;height:32px"><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
										<td><a>
											<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
											<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
											<xsl:value-of select="label"/>
											</a></td>
										<td valign="top"  class='alignright'><span class="filedescription"><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/></span></td>
									</tr>
									<tr class="filecells">
										<td valign="top" colspan="2"><span class="filedescription"><xsl:value-of select="description"/></span></td>
									</tr>
				    			</xsl:for-each>
							    </table>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise><table summary="this table holds the title, size of file and download time" width="96%" cellspacing="1" cellpadding="3">
					<tr>
						<th colspan="2">Filename</th>
						<th class='alignright'>Download time</th>
					</tr>
		    	<xsl:for-each select="files/file">
					<tr class="filecells">
						<td valign="top" rowspan="2" class="imgicon"><img style="width:32px;height:32px"><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
						<td><a>
							<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
							<xsl:value-of select="label"/>
							</a></td>
						<td valign="top"  class='alignright'><span class="filedescription"><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/></span></td>
					</tr>
					<tr class="filecells">
						<td valign="top" colspan="2"><span class="filedescription"><xsl:value-of select="description"/></span></td>
					</tr>
    			</xsl:for-each>
			    </table></xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="$file_download_style='DATE, TITLE AND SIZE'">
			<div class="label"><span><xsl:value-of  select="label"/></span></div>
			<xsl:choose>
				<xsl:when test="boolean(files/group)">
					<xsl:for-each select="files/group">
						<xsl:choose>
							<xsl:when test="group">
								<xsl:for-each select="group">
								<ul class='filelist'>
									<xsl:for-each select="file">
					    				<li><!--
										<xsl:attribute name="style">list-style-image:/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute>
										--><a>
												<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
												<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
												<span class="newsdate"><xsl:call-template name="format_date">
											<xsl:with-param name="current_date"><xsl:value-of select="date"/></xsl:with-param>
											<xsl:with-param name="output_format">DD/MM/YYYY</xsl:with-param>
										</xsl:call-template> - </span> <xsl:value-of select="label"/>
										</a> <br/><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/></li>
				    					</xsl:for-each>
								    </ul>
		    					</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<ul class='filelist'>
									<xsl:for-each select="file">
					    				<li><!--
										<xsl:attribute name="style">list-style-image:/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute>
										-->
										<a>
											<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
											<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
										<span class="newsdate"><xsl:call-template name="format_date">
											<xsl:with-param name="current_date"><xsl:value-of select="date"/></xsl:with-param>
											<xsl:with-param name="output_format">DD/MM/YYYY</xsl:with-param>
										</xsl:call-template> - </span> <xsl:value-of select="label"/>
										</a> <br/><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/></li>
			    					</xsl:for-each>
							    </ul>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<ul class='filelist'>
				    	<xsl:for-each select="files/file">
		    				<li><!--
							<xsl:attribute name="style">list-style-image:url(/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif);list-style-position: inside;</xsl:attribute>
							--><a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
								<span class="newsdate"><xsl:call-template name="format_date">
											<xsl:with-param name="current_date"><xsl:value-of select="date"/></xsl:with-param>
											<xsl:with-param name="output_format">DD/MM/YYYY</xsl:with-param>
										</xsl:call-template> - </span> <xsl:value-of select="label"/></a> <br/><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/></li>
    					</xsl:for-each>
				    </ul>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="boolean(files/group)">
			    	<xsl:for-each select="files/group">
							<xsl:choose>
								<xsl:when test="group">
									<xsl:for-each select="group">
										<table summary="this table holds the title, summary, size of file and download time" class='filetable'>
											<xsl:attribute name='id'>dl2_<xsl:value-of select="../@label"/>_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
											<xsl:for-each select="file">
	    										<tr>
													<td valign="top" style="width:20px"><img style="width:16px;height:16px"><xsl:attribute name="width">16</xsl:attribute><xsl:attribute name="height">16</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
													<td><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
														<xsl:attribute name="title"><xsl:value-of select="url"/> (<xsl:value-of select="size"/>/<xsl:value-of select="download_time"/>)</xsl:attribute>
													<xsl:value-of select="label"/></a></td>
												</tr>
				    						</xsl:for-each>
										</table>
		    						</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<table summary="this table holds the title, summary, size of file and download time" class='filetable'>
									<xsl:attribute name='id'>dl_<xsl:value-of select="@label"/>_<xsl:value-of select="position()"/>_<xsl:value-of select="$property_identifier"/></xsl:attribute>
									<xsl:for-each select="file">
	    							<tr>
										<td valign="top" style="width:20px"><img style="width:16px;height:16px"><xsl:attribute name="width">16</xsl:attribute><xsl:attribute name="height">16</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
											<td><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
											<xsl:attribute name="title"><xsl:value-of select="url"/> (<xsl:value-of select="size"/>/<xsl:value-of select="download_time"/>)</xsl:attribute>
										<xsl:value-of select="label"/></a></td>
									</tr>
		    						</xsl:for-each>
									</table>
								</xsl:otherwise>
							</xsl:choose>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<table summary="this table holds the title, summary, size of file and download time" class='filetable'>
				    	<xsl:for-each select="files/file">
	    				<tr>
							<td valign="top" style="width:20px"><img style="width:16px;height:16px"><xsl:attribute name="width">16</xsl:attribute><xsl:attribute name="height">16</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
							<td><a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="url"/> (<xsl:value-of select="size"/>/<xsl:value-of select="download_time"/>)</xsl:attribute>
								<xsl:value-of select="label"/></a></td>
							</tr>
    					</xsl:for-each>
					</table>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test=".//group">
	
	<script type="text/javascript">
		downloadlist_init();
	</script>
	</xsl:if>
</xsl:template>

<xsl:template name="display_files_comma">
    	<xsl:comment>display_files_comma</xsl:comment>
    		<xsl:for-each select="files/file">
    		<xsl:if test="position()>1">,</xsl:if>
    		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
			<img style="width:32px;height:32px" ><xsl:attribute name="width">32</xsl:attribute><xsl:attribute name="height">32</xsl:attribute><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/>.</xsl:attribute></img>
			&#32;<xsl:value-of select="label"/></a>
    	</xsl:for-each>
</xsl:template>



<xsl:template name="get_file_extension">
	<xsl:param name="file"/>
	<xsl:param name="has"><xsl:choose>
	<xsl:when test="contains($file,'.')">1</xsl:when>
	<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:param>
	
	<xsl:variable name="values">
	<xsl:choose>
		<xsl:when test="contains($file,'.')">
				<xsl:call-template name="get_file_extension">
					<xsl:with-param name="file" select="substring-after($file,'.')"/>
				<xsl:with-param name="has" select="$has"/>
			 </xsl:call-template>
	   	</xsl:when>
		<xsl:otherwise>
			<xsl:if test="$has=1">
				<xsl:value-of select="$file"/>
			</xsl:if>
		</xsl:otherwise>
 	</xsl:choose>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="$values!=''"><xsl:value-of select="$values"/></xsl:when>
		<xsl:otherwise>lsl</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>