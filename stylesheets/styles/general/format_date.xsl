<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.8 $
- Modified $Date: 2005/02/14 19:23:34 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	

<!--
This function will take reformat a default date taken in the format

YYYY-MM-DD
-->

<xsl:template name="format_date">
	<xsl:param name="current_date"></xsl:param>
	<xsl:param name="output_format">Dxx MMMM YYYY</xsl:param>
	
	<xsl:variable name="format_date"><xsl:value-of select="translate(normalize-space($current_date),',','')"/></xsl:variable>
<!--
	[<xsl:value-of select="$output_format"/>]
	[<xsl:value-of select="$current_date"/>]
	[d:<xsl:value-of select="$format_date"/>]
format of date recieved = "Thu,  6 Feb 2004 07:16:05 +0000" 
formatted into "Thu 6 Feb 2004 07:16:05 +0000" 

	output formatting options
	D 	= 5 or 10
	DD	= 05 or 10
	xx	= st, nd, rd or th
	
	M	= 1
	MM	= 01
	MMMM= January
	
	YY	= 03
	YYYY= 2003
-->	

<!-- 
	split the date into three variables day, month and year
-->
<xsl:variable name="txt"><xsl:value-of select="substring-before($current_date,',')"/></xsl:variable>
<xsl:variable name="current_dayname"><xsl:choose>
	<xsl:when test="$txt='Mon'">Monday</xsl:when>
	<xsl:when test="$txt='Tue'">Tuesday</xsl:when>
	<xsl:when test="$txt='Wed'">Wednesday</xsl:when>
	<xsl:when test="$txt='Thu'">Thursday</xsl:when>
	<xsl:when test="$txt='Fri'">Friday</xsl:when>
	<xsl:when test="$txt='Sat'">Saturday</xsl:when>
	<xsl:when test="$txt='Sun'">Sunday</xsl:when>
</xsl:choose></xsl:variable>
<xsl:variable name="current_left"><xsl:value-of select="normalize-space(substring-after($current_date,','))"/></xsl:variable>
<xsl:variable name="current_day"><xsl:value-of select="substring-before($current_left,' ')"/></xsl:variable>
<xsl:variable name="current_month"><xsl:value-of select="substring-before(substring-after($current_left,' '),' ')"/></xsl:variable>
<xsl:variable name="current_year"><xsl:value-of select="substring-before(substring-after(substring-after($current_left,' '),' '),' ')"/></xsl:variable>
<xsl:variable name="current_time"><xsl:value-of select="substring-after(substring-after(substring-after($current_left,' '),' '),' ')"/></xsl:variable>
<xsl:variable name="current_hours"><xsl:value-of select="substring-before($current_time,':')"/></xsl:variable>
<xsl:variable name="current_minutes"><xsl:value-of select="substring-before(substring-after($current_time,':'),':')"/></xsl:variable>
<xsl:variable name="current_seconds"><xsl:value-of select="substring-before(substring-after(substring-after($current_time,':'),':'),' ')"/></xsl:variable>
<!--
[<xsl:value-of select="$current_date"/>]
[day:<xsl:value-of select="$current_day"/>]
[<xsl:value-of select="$current_month"/>]
[yr::<xsl:value-of select="$current_year"/>]
[<xsl:value-of select="$current_hours"/>]
[<xsl:value-of select="$current_minutes"/>]
[<xsl:value-of select="$current_seconds"/>]
[dn:<xsl:value-of select="$current_dayname"/>]
-->
<!-- 
from the output format format the day part
-->
<xsl:variable name="day"><xsl:choose>
	<xsl:when test="contains($output_format,'DD')"><xsl:value-of select="$current_day"/></xsl:when>
	<xsl:when test="contains($output_format,'D')"><xsl:value-of select="number($current_day)"/></xsl:when>
	<xsl:otherwise></xsl:otherwise>
</xsl:choose></xsl:variable>

<!-- 
generate the extension for the day
-->
<xsl:variable name="ext"><xsl:choose>
	<xsl:when test="
		$day = '1' or
		$day = '21' or
		$day = '31'
	">&lt;sup&gt;st&lt;/sup&gt;</xsl:when>
	<xsl:when test="
		$day = '2' or
		$day = '22'
	">&lt;sup&gt;nd&lt;/sup&gt;</xsl:when>
	<xsl:when test="
		$day = '3' or
		$day = '23'
	">&lt;sup&gt;rd&lt;/sup&gt;</xsl:when>
	<xsl:otherwise>&lt;sup&gt;th&lt;/sup&gt;</xsl:otherwise>
</xsl:choose></xsl:variable>

<!-- 
format the months formatting option
-->
<xsl:variable name="month"><xsl:choose>
	<xsl:when test="contains($output_format,'MMMM')"><xsl:choose>
		<xsl:when test="$current_month='Jan'">January</xsl:when>
		<xsl:when test="$current_month='Feb'">February</xsl:when>
		<xsl:when test="$current_month='Mar'">March</xsl:when>
		<xsl:when test="$current_month='Apr'">April</xsl:when>
		<xsl:when test="$current_month='May'">May</xsl:when>
		<xsl:when test="$current_month='Jun'">June</xsl:when>
		<xsl:when test="$current_month='Jul'">July</xsl:when>
		<xsl:when test="$current_month='Aug'">August</xsl:when>
		<xsl:when test="$current_month='Sep'">September</xsl:when>
		<xsl:when test="$current_month='Oct'">October</xsl:when>
		<xsl:when test="$current_month='Nov'">November</xsl:when>
		<xsl:when test="$current_month='Dec'">December</xsl:when>
	</xsl:choose></xsl:when>
	<xsl:when test="contains($output_format,'MMM')"><xsl:value-of select="$current_month"/></xsl:when>
	<xsl:when test="contains($output_format,'MM')"><xsl:choose>
		<xsl:when test="$current_month='Jan'">01</xsl:when>
		<xsl:when test="$current_month='Feb'">02</xsl:when>
		<xsl:when test="$current_month='Mar'">03</xsl:when>
		<xsl:when test="$current_month='Apr'">04</xsl:when>
		<xsl:when test="$current_month='May'">05</xsl:when>
		<xsl:when test="$current_month='Jun'">06</xsl:when>
		<xsl:when test="$current_month='Jul'">07</xsl:when>
		<xsl:when test="$current_month='Aug'">08</xsl:when>
		<xsl:when test="$current_month='Sep'">09</xsl:when>
		<xsl:when test="$current_month='Oct'">10</xsl:when>
		<xsl:when test="$current_month='Nov'">11</xsl:when>
		<xsl:when test="$current_month='Dec'">12</xsl:when>
	</xsl:choose></xsl:when>
	<xsl:when test="contains($output_format,'M')"><xsl:choose>
		<xsl:when test="$current_month='Jan'">1</xsl:when>
		<xsl:when test="$current_month='Feb'">2</xsl:when>
		<xsl:when test="$current_month='Mar'">3</xsl:when>
		<xsl:when test="$current_month='Apr'">4</xsl:when>
		<xsl:when test="$current_month='May'">5</xsl:when>
		<xsl:when test="$current_month='Jun'">6</xsl:when>
		<xsl:when test="$current_month='Jul'">7</xsl:when>
		<xsl:when test="$current_month='Aug'">8</xsl:when>
		<xsl:when test="$current_month='Sep'">9</xsl:when>
		<xsl:when test="$current_month='Oct'">10</xsl:when>
		<xsl:when test="$current_month='Nov'">11</xsl:when>
		<xsl:when test="$current_month='Dec'">12</xsl:when>
	</xsl:choose></xsl:when>
	<xsl:otherwise></xsl:otherwise>
</xsl:choose></xsl:variable>

<!-- 
format the year format
-->
<xsl:variable name="year"><xsl:choose>
	<xsl:when test="contains($output_format,'YYYY')"><xsl:value-of select="$current_year"/></xsl:when>
	<xsl:when test="contains($output_format,'YY')"><xsl:value-of select="substring($current_year , 3,2)"/></xsl:when>
	<xsl:otherwise></xsl:otherwise>
</xsl:choose></xsl:variable>
<!-- 
output the formatted date.
-->
<xsl:if test="contains($output_format,'d')"><xsl:value-of select="$current_dayname"/><xsl:value-of select="' '"/></xsl:if>
<xsl:choose>
	<xsl:when test="
		contains($output_format, 'DxxRM YY') or 
		contains($output_format, 'DxxRMM YY') or 
		contains($output_format, 'DxxRMMM YY') or 
		contains($output_format, 'DxxRMMMM YY') or 
		contains($output_format, 'DxxRM YYYY') or 
		contains($output_format, 'DxxRMM YYYY') or 
		contains($output_format, 'DxxRMMM YYYY') or 
		contains($output_format, 'DxxRMMMM YYYY') or 
		contains($output_format, 'DDxxRM YY') or 
		contains($output_format, 'DDxxRMM YY') or 
		contains($output_format, 'DDxxRMMM YY') or 
		contains($output_format, 'DDxxRMMMM YY') or 
		contains($output_format, 'DDxxRM YYYY') or 
		contains($output_format, 'DDxxRMMM YYYY') or 
		contains($output_format, 'DDxxRMM YYYY') or 
		contains($output_format, 'DDxxRMMMM YYYY')
		">
		<xsl:value-of select="$day"/><xsl:value-of select="$ext"/><xsl:value-of select="' '"/><br /><xsl:value-of select="$month"/><xsl:value-of select="' '"/><xsl:value-of select="$year"/>
	</xsl:when>
	<xsl:when test="
		contains($output_format, 'Dxx M YY') or 
		contains($output_format, 'Dxx MM YY') or 
		contains($output_format, 'Dxx MMM YY') or 
		contains($output_format, 'Dxx MMMM YY') or 
		contains($output_format, 'Dxx M YYYY') or 
		contains($output_format, 'Dxx MM YYYY') or 
		contains($output_format, 'Dxx MMM YYYY') or 
		contains($output_format, 'Dxx MMMM YYYY') or 
		contains($output_format, 'DDxx M YY') or 
		contains($output_format, 'DDxx MM YY') or 
		contains($output_format, 'DDxx MMM YY') or 
		contains($output_format, 'DDxx MMMM YY') or 
		contains($output_format, 'DDxx M YYYY') or 
		contains($output_format, 'DDxx MMM YYYY') or 
		contains($output_format, 'DDxx MM YYYY') or 
		contains($output_format, 'DDxx MMMM YYYY')
		">
	<xsl:value-of select="$day"/><xsl:value-of select="$ext"/><xsl:value-of select="' '"/><xsl:value-of select="$month"/><xsl:value-of select="' '"/><xsl:value-of select="$year"/>
	</xsl:when>
	<xsl:when test="
		contains($output_format, 'D M YY') or 
		contains($output_format, 'D MM YY') or 
		contains($output_format, 'D MMM YY') or 
		contains($output_format, 'D MMMM YY') or 
		contains($output_format, 'D M YYYY') or 
		contains($output_format, 'D MM YYYY') or 
		contains($output_format, 'D MMM YYYY') or 
		contains($output_format, 'D MMMM YYYY') or 
		contains($output_format, 'DD M YY') or 
		contains($output_format, 'DD MM YY') or 
		contains($output_format, 'DD MMM YY') or 
		contains($output_format, 'DD MMMM YY') or 
		contains($output_format, 'DD M YYYY') or 
		contains($output_format, 'DD MMM YYYY') or 
		contains($output_format, 'DD MM YYYY') or 
		contains($output_format, 'DD MMMM YYYY')
		">
	<xsl:value-of select="$day"/><xsl:value-of select="' '"/><xsl:value-of select="$month"/><xsl:value-of select="' '"/><xsl:value-of select="$year"/>
	</xsl:when>
	
	<xsl:when test="
		contains($output_format, 'D/M/YY') or 
		contains($output_format, 'D/MM/YY') or 
		contains($output_format, 'D/MMM/YY') or 
		contains($output_format, 'D/MMMM/YY') or 
		contains($output_format, 'D/M/YYYY') or 
		contains($output_format, 'D/MM/YYYY') or 
		contains($output_format, 'D/MMM/YYYY') or 
		contains($output_format, 'D/MMMM/YYYY') or 
		contains($output_format, 'DD/M/YY') or 
		contains($output_format, 'DD/MM/YY') or 
		contains($output_format, 'DD/MMM/YY') or 
		contains($output_format, 'DD/MMMM/YY') or 
		contains($output_format, 'DD/M/YYYY') or 
		contains($output_format, 'DD/MM/YYYY') or 
		contains($output_format, 'DD/MMM/YYYY') or 
		contains($output_format, 'DD/MMMM/YYYY')
		">
	<xsl:value-of select="$day"/><xsl:value-of select="'/'"/><xsl:value-of select="$month"/><xsl:value-of select="'/'"/><xsl:value-of select="$year"/>
	</xsl:when>
	<xsl:when test="
		contains($output_format, 'D:M:YY') or 
		contains($output_format, 'D:MM:YY') or 
		contains($output_format, 'D:MMM:YY') or 
		contains($output_format, 'D:MMMM:YY') or 
		contains($output_format, 'D:M:YYYY') or 
		contains($output_format, 'D:MM:YYYY') or 
		contains($output_format, 'D:MMM:YYYY') or 
		contains($output_format, 'D:MMMM:YYYY') or 
		contains($output_format, 'DD:M:YY') or 
		contains($output_format, 'DD:MM:YY') or 
		contains($output_format, 'DD:MMM:YY') or 
		contains($output_format, 'DD:MMMM:YY') or 
		contains($output_format, 'DD:M:YYYY') or 
		contains($output_format, 'DD:MMM:YYYY') or 
		contains($output_format, 'DD:MM:YYYY') or 
		contains($output_format, 'DD:MMMM:YYYY')
		">
	<xsl:value-of select="$day"/><xsl:value-of select="':'"/><xsl:value-of select="$month"/><xsl:value-of select="':'"/><xsl:value-of select="$year"/>
	</xsl:when>
</xsl:choose>
<xsl:choose>
	<xsl:when test="contains($output_format,'R')"><br /></xsl:when>
	<xsl:otherwise><xsl:value-of select="' '"/></xsl:otherwise>
</xsl:choose>
<xsl:if test="contains($output_format,'hour:minutes')"><xsl:value-of select="$current_hours"/>:<xsl:value-of select="$current_minutes"/></xsl:if>


</xsl:template>


</xsl:stylesheet>