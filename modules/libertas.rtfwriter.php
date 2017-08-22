<?php
// +----------------------------------------------------------------------+
// | RTF Writer                                                           |
// +----------------------------------------------------------------------+
// | This source file is subject to GNU General Public Licence,                  |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.gnu.org/copyleft/gpl.html.                                |
// +----------------------------------------------------------------------+
// | Author: Muhammad Imran Mirza <imranmirza82@hotmail.com>                    |
// +----------------------------------------------------------------------+
// $Id: rtfwriter
// NOT IMPLEMENTED:
// Header:
//         Unicode
//         Stylesheets
//         Subdocuments
//         List tables
// Document:
//         User properties
//
// TODO:
// -colors => done 2004-03-24
// -spacing => done 2004-03-24
// -Numbered lists => done 2004-04-09
// -Bullet lists => done 2004-05-30
// -Tables => basics done 2004-11-22
// -Pictures => basics done 2004-11-22
// -Sections
define( 'NL', "\n" );
define( 'ESC', "\\'" );
define ( 'FATAL', E_USER_ERROR );
define ( 'ERROR', E_USER_WARNING );
define ( 'WARNING', E_USER_NOTICE );

$GLOBALS['_RTF_font_families'] = array( 'roman', 'swiss', 'modern', 'script', 'decor', 'tech', 'bidi' );
$GLOBALS['_RTF_font_charsets'] = array( 0, 1, 2, 3, 77, 128, 129, 130134, 136, 161, 162, 163, 177, 178, 179, 180, 181, 186, 204, 222, 238, 254, 255 );
$GLOBALS['_RTF_font_formats'] = array( 'b', 'i', 'ul', 'sub', 'sup', 'caps', 'strike', 'hide', '' );
$GLOBALS['_RTF_border_styles'] = array( 's', 'th', 'sh', 'db', 'dot', 'dash', 'hair', 'inset', 'dashsm', 'dashd', 'dashdd', 'outset', 'triple', 'tnthsg', 'thtnsg', 'tnthtnsg', 'tnthmg', 'thtnmg', 'tnthtnmg', 'tntglg', 'thtnlg', 'tnthtnlg', 'wavy', 'wavydb', 'dashdotstr', 'emboss', 'engrave', 'frame' );

/**
 * Base class for RTF Writer
 * Contains all general functions
 *
 * @since PHP 4.3.2
 * @author Johan Natt och Dag <johan@nattochdag.org>
 * @see http://rtfwriter.sourceforge.net
 */
class RTFWriter {

    // {{{ getCallSource()

    /**
     * This method find the position of the calling function
     * that was the reason for the triggered error
     *
     * @return int The id in the backtrace array
     * @access private
     */
    function getCallSource() {
        $btrace = debug_backtrace();
        $btid = 0;
        while ($btrace[$btid]['file'] == $_SERVER['SCRIPT_NAME']) {
            $btid++;
        }
        return $btid;
    }

    // }}}


    // {{{ error()

    /**
     * This method triggers an error
     * that was the reason for the triggered error
     *
     * @return int The id in the backtrace array
     * @access private
     */
    function error( $txt, $type ) {
        $btrace = debug_backtrace();
        $btid = $this->getCallSource();
        $errtxt = $txt . " in <b>" . $btrace[1]['file'] . "</b> on line <b>" . $btrace[1]['line'] . "</b>.<br>";
        $errtxt .= " Error encountered in <b>" . $btrace[$btid]['file'] . "</b> on line <b>" . $btrace[$btid]['line'] . "</b>.<br>";
        $errtxt .= " Error catched";
        trigger_error( $errtxt, $type );
    }

    // }}}


    // {{{ getTwips()

    /**
     * This method converts cm, inches, pixels and points to twips
     *
     * @param string $measure The measure in any format
     * @return void
     * @access private
     */
    function getTwips( $measure )
    {
        if ( ereg( '(\-?[0-9]+(\.[0-9]+)?)( )*(mm|cm|in|px|p|pt)?', strtolower( $measure ), $arg ) ) {
            switch ( $arg[4] ) {
                case 'mm':
                    return intval( floatval( $arg[1] ) * 5670 );
                case 'cm':
                    return intval( floatval( $arg[1] ) * 567 );
                case 'in':
                    return intval( floatval( $arg[1] ) * 1440 );
                case 'px':
                case 'p':
                case 'pt':
                    return intval( floatval( $arg[1] ) * 20 );
                default:
                    return intval( floatval( $arg[1] ) );
            }

        } else {
            $this->error("Illegal measure ('" . $measure . "')", ERROR);
            return 0;
        }
    }

    // }}}


    // {{{ parseFontStyle()

    /**
     * This method translates user specified font styles to RTF font format specifiers
     *
     * @param string $style Character styles
     * @return string RTF character formatting specifiers
     * @access private
     */
    function parseFontStyle( $fmt )
    {
        $font_style = '';
        if ( strpos( $fmt, 'b' ) !== false ) {
            $font_style .= '\b';
        }
        if ( strpos( $fmt, 'scaps' ) !== false ) {
            $font_style .= '\scaps';
        } elseif ( strpos( $fmt, 'caps' ) !== false ) {
            $font_style .= '\caps';
        }
        if ( strpos( $fmt, 'e' ) !== false ) {
            $font_style .= '\embo';
        }
        if ( strpos( $fmt, 'i' ) !== false ) {
            $font_style .= '\i';
        }
        if ( strpos( $fmt, 'sh' ) !== false ) {
            $font_style .= '\shad';
        }
        if ( strpos( $fmt, 'st' ) !== false ) {
            $font_style .= '\strike';
        }
        if ( strpos( $fmt, 'sub' ) !== false ) {
            $font_style .= '\sub';
        }
        if ( strpos( $fmt, 'sup' ) !== false ) {
            $font_style .= '\super';
        }
        if ( strpos( $fmt, 'ul' ) !== false ) {
            $font_style .= '\ul';
        }

        if ($font_style == '') {
            $this->error("Illegal font format specifier: '" . $fmt . "'", ERROR);
        }

        return $font_style;
    }

    // }}}


    // {{{ convertText()

    /**
     * This method converts a text to the rtf representation of the text
     * Conversions made:
     *     \t => \tab
     *     Characters above ASCII 161 are converted to hexdecimal representation
     *
     * @param string $tx The text to be converted
     * @return string The converted text
     * @access private
     */
    function convertText( $txt )
    {
        $rtftxt = '';

        $txt = str_replace( '\t ', '\tab ', $txt ); // If space after \t
        $txt = str_replace( "\\t\\", "\\tab\\", $txt ); // If another command after \t (e.g. another tab)
        $txt = str_replace( chr( 9 ), '\tab ', $txt ); // If an ASCII tab character
        $txt = str_replace( "\\\\", ESC . '5c', $txt ); // Backslash must be entered with double \
        $txt = str_replace( '{', ESC . '7b', $txt ); // RTF control character
        $txt = str_replace( '}', ESC . '7d', $txt ); // RTF control character
        for( $i = 0; $i < strlen( $txt ); $i++ ) {
            $c = substr( $txt, $i, 1 );
            if ( ord( $c ) >= 128 ) {
                $rtftxt .= ESC . dechex( ord( $c ) );
            } else {
                $rtftxt .= $c;
            }
        }
        return $rtftxt;
    }

    // }}}
}

/**
 * Paragraph formatting class for RTF Writer
 *
 * @since PHP 4.0.2
 * @author Johan Natt och Dag <johan@nattochdag.org>
 * @see http://rtfwriter.sourceforge.net
 */

class RTFParagraphFormat /* extends RTFWriter */ {

    // {{{ properties

    /**
     * RTF Document class to which this format is associated
     *
     * @var bool
     * @access private
     */
    var $rtf;

    /**
     * Format's unique id - also used for creating unique bullet/number list ids
     *
     * @var bool
     * @access private
     */
    var $id;

    /**
     * Break before this paragraph
     *
     * @var bool
     * @access private
     */
    var $pageBreak = false;

    /**
     * Keep this paragraph with next paragraph
     *
     * @var bool
     * @access private
     */
    var $keepWithNext = false;

    /**
     * Paragraph alignment
     *
     * @var string
     * @access private
     */
    var $align = '';

    /**
     * Paragraph first indent (twips)
     *
     * @var int
     * @access private
     */
    var $fi = 0;

    /**
     * Paragraph left indent (twips)
     *
     * @var int
     * @access private
     */
    var $li = 0;

    /**
     * Paragraph right indent (twips)
     *
     * @var int
     * @access private
     */
    var $ri = 0;

    /**
     * Space before paragraph (twips)
     *
     * @var int
     * @access private
     */
    var $sb = 0;

    /**
     * Space after paragraph (twips)
     *
     * @var int
     * @access private
     */
    var $sa = 0;

    /**
     * Paragraph line space (twips or multiple)
     *
     * @var int
     * @access private
     */
    var $sl = 0;

    /**
     * Paragraph line space is a multiple
     *
     * @var int
     * @access private
     */
    var $slmult = false;

    /**
     * Paragraph hyphenation
     *
     * @var bool
     * @access private
     */
    var $hyphenate = true;

    /**
     * Paragraph tabs
     *
     * @var string
     * @access private
     */
    var $tabs = '';

    /**
     * Paragraph list style (bulleted, numbered)
     *
     * @var array
     * @access private
     */
    var $plist = array();

    /**
     * Paragraph font
     *
     * @var int
     * @access private
     */
    var $font = 0;

    /**
     * Paragraph font size
     *
     * @var int
     * @access private
     */
    var $fontsize = 10;

    /**
     * Paragraph font style
     *
     * @var string
     * @access private
     */
    var $fontstyle = '';

    /**
     * Character scale
     *
     * @var string
     * @access private
     */
    var $scale = '';

    /**
     * Character spacing
     *
     * @var string
     * @access private
     */
    var $spacing = '';

    /**
     * Character position
     *
     * @var string
     * @access private
     */
    var $position = '';

    /**
     * Paragraph border
     *
     * @var string
     * @access private
     */
    var $border = '';

    /**
     * Paragraph foreground color (text color)
     *
     * @var string
     * @access private
     */
    var $fg = 0;

    /**
     * Paragraph background color
     *
     * @var string
     * @access private
     */
    var $bg = 0;

    // }}}

    // {{{ constructor

    /**
     * Constructor. Sets the paragraph id.
     *
     * @param int $id Paragraph id
     * @return void
     * @access public
     */
    function RTFParagraphFormat( &$rtf, $id )
    {
        $this->rtf = &$rtf;
        $this->id = $id;

        // Initialize variables
        $this->plist['recentlevel'] = -1;
    }

    // }}}


    // {{{ setFont()

    /**
     * This method assigns a font and font properties to a paragraph
     *
     * @param string $font A font number (already added to the document's font table)
     * @param string $fontsize Size of the font
     * @param string $fontstyle Font formatting
     * @return void
     * @access public
     */
    function setFont( $font, $fontsize = 12, $fontstyle = '' )
    {
        if ( ( $font <0 ) || ( $font > sizeof( $this->rtf->header['fonts'] )-1 ) ) {
            $this->error( "Illegal font: Font does not exist", ERROR);
        }
        $this->font = $font;
        $this->fontsize = $fontsize;
        if ( $fontstyle != '' ) {
            $this->fontstyle = $this->rtf->parseFontStyle( $fontstyle );
        }
    }

    // }}}

    // {{{ setCharSpacing()

    /**
     * This method sets character spacing for
     *
     * @param string $spacing Character spacing +xx or -xx for expanded or
     * condensed, respectively
     * @param string $position Character position +xx or -xx for raised or
     * lowered, respectively
     * @param string $scale Character scaling in percent
     * @return void
     * @access public
     */
    function setCharSpacing( $spacing, $position = 0, $scale = 100 )
    {
    	$this->spacing = '\expnd' . intval(-$this->rtf->getTwips(substr($spacing,1))/5) . /* quarter-points */
    					 '\expndtw' . -$this->rtf->getTwips(substr($spacing,1));
		if ($scale != 100)
    		$this->scale = '\charscalex' . $scale;
    	
    	if ($position !== 0) {
    		$pos = $this->rtf->getTwips($position);
    		if ($pos < 0)
    			$this->position = '\dn' . abs(intval($pos/10));
    		else
    			$this->position = '\up' . intval($pos/10);
    	}
    }

    // }}}



    // {{{ setBorder()

    /**
     * This method assigns a border and border properties to a paragraph
     *
     * @param string $position Border position ([t]op, [b]ottom, [l]eft,
     * [r]ight)
     * @param string $style Border style
     * @param string $width Border width
     * @param integer $color Border color id
     * @param string $space Space between paragraph and border
     * @return void
     * @access public
     */
    function setBorder( $position, $style = 's', $width = '0.5pt', $color = 0, $space = '2pt')
    {
    	// Border position
    	switch($position) {
    		case 't':
    		case 'top':
    			$this->border = '\brdrt';
    			break;
    		case 'b':
    		case 'bottom':
    			$this->border = '\brdrb';
    			break;
    		case 'l':
    		case 'left':
    			$this->border = '\brdrl';
    			break;
    		case 'r':
    		case 'right':
    			$this->border = '\brdrr';
    			break;
    		default:
				$this->error( "Illegal border position: Position '$position' unknown", ERROR);
				break;
    	}
    	
    	// Border style
    	if ( in_array( strtolower( $style ), $GLOBALS['_RTF_border_styles'] ) ) {
            // A proper border style was specified
            $this->border .= '\brdr' . strtolower( $style );
        } else {
            $this->error("Illegal parameter: Border style '$font_family' unknown", ERROR);
        }
        
    	// Border width
    	$this->border .= '\brdrw' . $this->rtf->getTwips($width);	
    	
    	// Border color
    	$this->border .= '\brdrcf' . $color;
    	
    	// Space between paragraph and border
    	$this->border .= '\brsp' . $this->rtf->getTwips($space);	
    }

    // }}}

    // {{{ setColor()

    /**
     * This method assigns colors to a paragraph
     *
     * @param string $fg Foreground color
     * @param string $bg Background color
     * @return void
     * @access public
     */
    function setColor( $fg, $bg = -1 )
    {
        if ( ( $fg < 0 ) || ( $fg > sizeof($this->rtf->header['colors']) ) ) {
            $this->error( "Illegal parameter: Foreground color does not exist (must use newColor())", ERROR );
        }
        if ( ( $bg < -1 ) || ( $bg > sizeof($this->rtf->header['colors']) ) ) {
            $this->error( "Illegal parameter: Background color does not exist (must use newColor())", ERROR);
        }
        $this->fg = $fg;
        $this->bg = $bg;
    }

    // }}}


    // {{{ setAlign()

    /**
     * This method sets paragraph alignment
     *
     * @param string $alignment Alignment (l[eft], r[ight], c[enter], j[ustified])
     * @return void
     * @access public
     */
    function setAlign( $alignment )
    {
        switch ( $alignment ) {
            case 'r':
            case 'right':
                $this->align = '\qr'; // Right alignment
                break;
            case 'c':
            case 'center':
                $this->align = '\qc'; // Center alignment
                break;
            case 'j':
            case 'justified':
                $this->align = '\qj'; // Justified
                break;
            case 'l':
            case 'left':
                $this->align = ''; // Default is left alignment
                break;
            default:
                $this->error("Potentially wrong parameter for setAlign: '" . $alignment . "'", WARNING);
        }
    }

    // }}}


    // {{{ setIndent()

    /**
     * This method sets paragraph indentation
     *
     * @param string $first_indent First indent in format accepted by getTwips
     * @param string $left_indent Left indent in format accepted by getTwips
     * @param string $right_indent Right indent in format accepted by getTwips
     * @return void
     * @access public
     */
    function setIndent( $first_indent, $left_indent = '', $right_indent = '' )
    {
        $this->fi = $this->rtf->getTwips( $first_indent );

        if ($left_indent != '') {
            $this->li = $this->rtf->getTwips( $left_indent );
        }

        if ($right_indent != '') {
            $this->ri = $this->rtf->getTwips( $right_indent );
        }
    }

    // }}}


    // {{{ setSpace()

    /**
     * This method sets paragraph line space
     *
     * @param string $sb Space before paragraph in format accepted by getTwips
     * @param string $sa Space after paragraph in format accepted by getTwips
     * @return void
     * @access public
     */
    function setSpace( $sb, $sa )
    {
        $this->sb = $this->rtf->getTwips( $sb );
        $this->sa = $this->rtf->getTwips( $sa );
    }

    // }}}


    // {{{ setLineSpace()

    /**
     * This method sets paragraph line space either exactly or as a line multiple
     *
     * @param string/integer $sl The line space in format accepted by getTwips or an integer
     * @return void
     * @access public
     */
    function setLineSpace( $sl )
    {
        if ( is_string( $sl ) ) {
            $this->sl = $this->rtf->getTwips( $sl );
        } else {
            $this->sl = $this->rtf->getTwips( ( 12 /*$this->fontsize*/ * $sl ) . 'pt' );
            $this->slmult = true;
        }
    }

    // }}}


    // {{{ setTab()

    /**
     * This method sets a new tab
     *
     * @param string $pos Tab position from the left margin in format accepted by getTwips
     * @param string $align Alignment (l[eft], r[ight], c[enter], d[ecimal])
     * @param string $lead Tab leader (.:-_+= see below)
     * @return void
     * @access public
     */
    function setTab( $pos, $align = 'l', $leader = '' )
    {
        // Tab kind (i.e. alignment)
        switch ( $align ) {
            case 'r':
            case 'right':
                $this->tabs .= '\tqr';
                break;
            case 'c':
            case 'center':
                $this->tabs .= '\tqc';
                break;
            case 'd':
            case 'decimal':
                $this->tabs .= '\tqdec';
                break;
            case 'l':
            case 'left':
                // Left alignment by default
                break;
            default:
                $this->error("Illegal parameter. Incorrect alignment: setTab(..., " . $align . ")", ERROR);
        }
        // Tab leader
        switch ( $leader ) {
            case '.':
                $this->tabs .= '\tldot';    // Leader dots
                break;
            case ':':
                $this->tabs .= '\tlmdot';   // Leader middle dots
                break;
            case '-':
                $this->tabs .= '\tlhyph';   // Leader hyphens
                break;
            case '_':
                $this->tabs .= '\tlul';     // Leader underline
                break;
            case '+':
                $this->tabs .= '\tlth';     // Leader thick line
                break;
            case '=':
                $this->tabs .= '\tleq';     // Leader equal sign
                break;
            case '':
                // No leader by default
                break;
            default:
                $this->error("Illegal parameter. Incorrect leader specifier: setTab(..., " . $leader . ")", ERROR);
        }

        // Assign tab position from left margin
        $this->tabs .= '\tx' . $this->rtf->getTwips( $pos );
    }

    // }}}


    // {{{ setNumbered()

    /**
     * This method sets the format to numbered list style
     *
     * @return void
     * @access public
     */
    function setNumbered()
    {
        if ( !isset( $this->plist['type'] ) ) {
            $this->plist['type'] = '#';
            $this->plist['currentlevel'] = 0;
            // Assign default numbering style and formatting
            if ( ( $fnt_times = $this->rtf->findFont( 'Times New Roman' ) ) == -1 )
                $fnt_times = $this->rtf->newFont( 'Times New Roman' );
            for ( $i = 1; $i <= 9; $i++ ) {
                $this->plist[$i]['font'] = $fnt_times; // Number font
                $this->plist[$i]['style'] = 0; // Numbering style (0=arabic)
                $this->plist[$i]['fi'] = '-283'; // First line indent in twips
                $this->plist[$i]['li'] = sprintf( '%d', floor( 283.5 * $i ) ); // Left indent
                $this->plist[$i]['ri'] = 0;
                $this->plist[$i]['char'] = 0; // We use a tab character by default
                $this->plist[$i]['align'] = 0;
            }
        } else {
                if ($this->plist['type'] == '#') {
                    $this->error("Numbered list already specified for paragraph. setNumbered() called second time", ERROR);
                } else {
                    $this->error("Bullet list already specified for paragraph. setNumbered() called", ERROR);
                }
        }
    }

    // }}}


    // {{{ setBulleted()

    /**
     * This method sets the format to bulleted list style
     *
     * @return void
     * @access public
     */
    function setBulleted()
    {
        if ( !isset( $this->plist['type'] ) ) {
            $this->plist['type'] = '*';
            $this->plist['currentlevel'] = 1;
            // Assign default bullet style and formatting
            if ( ( $fnt_times = $this->rtf->findFont( 'Times New Roman' ) ) == -1 )
                $fnt_times = $this->rtf->newFont( 'Times New Roman' );
            for ( $i = 1; $i <= 9; $i++ ) {
                $this->plist[$i]['font'] = $fnt_times; // Bullet font
                $this->plist[$i]['style'] = '\u9679'; // Bullet
                $this->plist[$i]['fi'] = '-283'; // First line indent
                $this->plist[$i]['li'] = sprintf( '%d', floor( 283.5 * $i ) ); // Left indent
                $this->plist[$i]['ri'] = 0;
                $this->plist[$i]['char'] = 0; // We use a tab character by default
                $this->plist[$i]['align'] = 0;
            }
        } else {
            if ($this->plist['type'] == '*') {
                $this->error("Bullet list already specified for paragraph. setBullet() called second time", ERROR);
            } else {
                $this->error("Numbered list already specified for paragraph. setBullet() called", ERROR);
            }
        }
    }

    // }}}

    // {{{ setLevelFormat()
    /**
     * This method sets the numbering style or the bullet
     * and the character following the number/bullet
     * for a numbered/bulleted list level
     *
     * @param int $level List level
     * @param string $style Numbering style/Bullet in RTF spec format (see end of file)
     * @param string $space Character following the number/bullet ('', ' ', 't')
     * @return void
     * @access public
     */
    function setLevelFormat( $level, $style, $space = 't' )
    {
        $this->plist[$level]['style'] = $style;
        switch ( $space ) {
            case '':
                $this->plist[$level]['char'] = 2;
                break;
            case ' ':
                $this->plist[$level]['char'] = 1;
                break;
            case 't':
                $this->plist[$level]['char'] = 0;
                break;
            default:
                if ($this->plist['type'] == '*') {
                    $this->error("Illegal character specified for setLevelFormat()", ERROR);
                }
        }
    }

    // }}}

    // {{{ setLevelFont()
    /**
     * This method assigns a font and font properties to a list level
     *
     * @param int $level List level
     * @param string $font A font number (already added to the document's font table)
     * @param string $fontsize Size of the font
     * @param string $fontstyle Font formatting
     * @return void
     * @access public
     */
    function setLevelFont( $level, $font, $fontsize = 10, $fontstyle = '' )
    {
        if ( ( $font <0 ) || ( $font > sizeof( $this->rtf->header['fonts'] )-1 ) ) {
            $this->error("Illegal font: Font does not exist", ERROR);
        }
        $this->plist[$level]['font'] = $font;
        $this->plist[$level]['fontsize'] = $fontsize;
        if ($fontstyle != '') {
            $this->plist[$level]['fontstyle'] = $this->rtf->parseFontStyle( $fontstyle );
        }
    }

    // }}}

    // {{{ setLevelColor()
    /**
     * This method assigns a colors to a paragraph
     *
     * @param int $level The list level
     * @param string $fg Foreground color
     * @param string $bg Background color
     * @return void
     * @access public
     */
    function setLevelColor( $level, $fg, $bg = -1 )
    {
        if ( ( $fg < 0 ) || ( $fg > sizeof($this->rtf->header['colors']) ) ) {
            $this->error("Illegal parameter: Foreground color does not exist (must use newColor())", ERROR);
        }
        if ( ( $bg < -1 ) || ( $bg > sizeof($this->rtf->header['colors']) ) ) {
            $this->error("Illegal parameter: Background color does not exist (must use newColor())", ERROR);
        }
        $this->plist[$level]['fg'] = $fg;
        $this->plist[$level]['bg'] = $bg;
    }

    // }}}


    // {{{ setLevelAlign()

    /**
     * This method sets alignment for a list level
     *
     * @param int $level The list level
     * @param string $alignment Alignment (l[eft], r[ight], c[enter])
     * @return void
     * @access public
     */
    function setLevelAlign( $level, $alignment )
    {
        switch ( $alignment ) {
            case 'r':
            case 'right':
                $this->plist[$level]['align'] = 2; // Right alignment
                break;
            case 'c':
            case 'center':
                $this->plist[$level]['align'] = 1; // Center alignment
                break;
            case 'l':
            case 'left':
                $this->plist[$level]['align'] = 0; // Default is left alignment
            default:
                $this->error("Potentially wrong parameter for setAlign: '" . $alignment . "'", WARNING);
        }
    }

    // }}}


    // {{{ setLevelIndent()

    /**
     * This method sets indentation for a list level
     *
     * @param int $level The list level
     * @param string $left_indent Left indent
     * @param string $first_indent First indent
     * @param string $right_indent Right indent
     * @return void
     * @access public
     */
    function setLevelIndent( $level, $left_indent, $first_indent = '', $right_indent = '' )
        {
        if ( $first_indent != '' )
            $this->plist[$level]['fi'] = $this->rtf->getTwips( $first_indent );
        if ( $left_indent != '' )
            $this->plist[$level]['li'] = $this->rtf->getTwips( $left_indent );
        if ( $left_indent != '' )
            $this->plist[$level]['ri'] = $this->rtf->getTwips( $right_indent );
    }

    // }}}
}


/**
 * Main class for the RTF Writer
 *
 * @since PHP 4.0.2
 * @author Johan Natt och Dag <johan@nattochdag.org>
 * @see http://rtfwriter.sourceforge.net
 */
class RTFWriterDoc extends RTFWriter {

    // {{{ properties

    /**
     * Header contents
     *
     * @var array
     * @access private
     */
    var $header = array();

    /**
     * Custom RTF headers for flexibility
     * These will be added after the supported parameters
     * defined above, and in the other they are added to the array
     *
     * @var string
     * @access private
     */
    var $custom_header = '';

    /**
     * Document info contents
     *
     * @var array
     * @access private
     */
    var $doc_info = array();

    /**
     * Custom document info for flexibility
     * These will be added after the supported parameters
     * defined above, and in the order they are added to the array
     *
     * @var string
     * @access private
     */
    var $custom_doc_info = '';

    /**
     * Paragraph formats in document
     * References to objects
     *
     * @var array
     * @access private
     */
    var $formats = array();

    /**
     * Document's formatting properties
     *
     * @var array
     * @access private
     */
    var $doc_fmt = array();

    /**
     * Custom document formatting properties for flexibility
     * These will be added after the supported parameters
     * defined above, and in the order they are added to the array
     *
     * @var string
     * @access private
     */
    var $custom_doc_fmt = '';

    /**
     * Section formatting (current/default: index 0, user defined: index >= 1);
     *
     * @var bool
     * @access private
     */
    var $section_fmt = array();

    /**
     * Custom section formatting properties for flexibility
     * These will be added after the supported parameters
     * defined above, and in the order they are added to the array
     *
     * @var string
     * @access private
     */
    var $custom_section_fmt = '';

    /**
     * Custom section header formatting properties for flexibility
     * These will be added after the supported parameters
     * defined above, and in the order they are added to the array
     *
     * @var string
     * @access private
     */
    var $custom_header_fmt = '';

    /**
     * Custom section footer formatting properties for flexibility
     * These will be added after the supported parameters
     * defined above, and in the order they are added to the array
     *
     * @var string
     * @access private
     */
    var $custom_footer_fmt = '';

    /**
     * Current paragraph format
     *
     * @var RTFParagraphFormat
     * @access private
     */
    var $paragraph_fmt = null;

    /**
     * Current paragraph's content (including any character formatting)
     *
     * @var string
     * @access private
     */
    var $paragraph_text = '';

    /**
     * Remebers the previous paragraphs format
     * Used to increment the number in numbered lists.
     *
     * @var RTFParagraphFormat
     * @access private
     */
    var $prevParagraphFormat = null;

    /**
     * Specifies whether we are inside the header section
     *
     * @var bool
     * @access private
     */
    var $in_header = false;

    /**
     * Specifies whether we are inside the footer section
     *
     * @var bool
     * @access private
     */
    var $in_footer = false;

    /**
     * Specifies whether we are inside the regular paragraph section
     *
     * @var bool
     * @access private
     */
    var $in_paragraph = false;

    /**
     * Specifies whether text has been output (in header, footer, paragraph, etc)
     *
     * @var bool
     * @access private
     */
    var $in_text = false;


    /**
     * Indiciates if the default paragraph font currently is in use
         * (or another temporarily switched to)
     *
     * @var bool
     * @access private
     */
    var $usingDefaultFont = true;


    /**
     * The RTF document content
     *
     * @var string
     * @access private
     */
    var $buffer;


    /**
     * Table nesting level
     *
     * @var int
     * @access private
     */
    var $numTables;


    /**
     * Tables
     *
     * @var array
     * @access private
     */
    var $tables = array();


    /**
     * Index of last row in array
     *
     * @var int
     * @access private
     */
    var $lastRowIdx;


    /**
     * Table rows
     *
     * @var array
     * @access private
     */
    var $tableRows = array();


    /**
     * Index of last column in array
     *
     * @var int
     * @access private
     */
    var $lastColIdx;


    /**
     * Table columns
     *
     * @var array
     * @access private
     */
    var $tableCols = array();


    /**
     * Current row level
     *
     * @var int
     * @access private
     */
    var $rowLevel;


    /**
     * Current cell color
     *
     * @var int
     * @access private
     */
    var $currentCellColor;

    /**
     * Default table border row settings
     *
     * @var int
     * @access private
     */
    var $defaultTableRowBorders = array();

    /**
     * Default table column border settings
     *
     * @var int
     * @access private
     */
    var $defaultTableColBorders = array();


    // }}}


    // {{{ constructor

    /**
     * Constructor. Sets the title, author and company document parameters.
     * Initializes table variables.
     *
     * @param string $title Document title
     * @param string $author Document author
     * @param string $company Document company
     * @return void
     * @access public
     */
    function RTFWriterDoc( $title = 'RTF', $author = 'RTFW v0.1', $company = 'Jonod' )
    {
        // Initialize header
        $this->header['version'] = 1; // '\rtf1';
        $this->header['charset'] = '\ansi'; // Document character set
        $this->header['default_font'] = 0; // Default font
        $this->header['fonts'] = array(); // Fonts table
        $this->header['colors'] = array(); // Color table
        $this->header['lists'] = array(); // List table

        // Initialize document info
        $this->doc_info['title'] = $title; // Document title
        $this->doc_info['subject'] = ''; // Document subject
        $this->doc_info['author'] = $author; // Document author
        $this->doc_info['manager'] = ''; // Author's manager
        $this->doc_info['company'] = $company; // Author's company
        $this->doc_info['category'] = ''; // Document category
        $this->doc_info['keywords'] = ''; // Document keywords
        $this->doc_info['version'] = 1; // Document version
        $this->doc_info['doccomm'] = 'RTF Writer 0.8'; // Document comments
        $this->doc_info['creatime'] = '\yr' . date( 'Y' ) . '\mo' . date( 'n' ) . '\dy' . date( 'j' );

        // Initialize document format
        $this->doc_fmt['landscape'] = false; // Document orientation
        $this->doc_fmt['width'] = 12240; // Document width
        $this->doc_fmt['height'] = 15840; // Document height
        $this->doc_fmt['margin_left'] = 1800; // Left margin
        $this->doc_fmt['margin_right'] = 1800; // Right margin
        $this->doc_fmt['margin_top'] = 1440; // Top margin
        $this->doc_fmt['margin_bottom'] = 1440; // Bottom margin
        $this->doc_fmt['deftab'] = 720; // Default tab
        $this->doc_fmt['deflang'] = 1033; // Default language
        $this->doc_fmt['widowctrl'] = true; // Widow and orphan control

        // Initialize section format
        $this->section_fmt['break'] = 'none'; // Type of section break preceding this section
        $this->section_fmt['cols'] = 1; // Number of columns
        $this->section_fmt['colsx'] = 720; // Space between columns
        $this->section_fmt['colline'] = false; // Line between columns
        $this->section_fmt['headery'] = 720; // Header's distance from top of page
        $this->section_fmt['footery'] = 720; // Footer's distance from bottom of page
        $this->section_fmt['pagenum'] = 'dec'; // Page number format
        $this->section_fmt['vertal'] = 't'; // Vertical alignment

        // Reset table counters
        $this->lastRowIdx = 0;
        $this->lastColIdx = 0;
        $this->numTables = 0;

        // Set default cell color
        $this->currentCellColor = 0;

        // Set default borders
        $locations = array('top', 'bottom', 'left', 'right', 'inside');
        foreach ( $locations as $loc ) {
            $this->defaultTableRowBorder['b_' . $loc] = 10;
            $this->defaultTableRowBorder['b_' . $loc . '_style'] = 's';
            $this->defaultTableRowBorder['b_' . $loc . '_color'] = 0;

            $this->defaultTableColBorder['b_' . $loc] = 10;
            $this->defaultTableColBorder['b_' . $loc . '_style'] = 's';
            $this->defaultTableColBorder['b_' . $loc . '_color'] = 0;
        }
        
        // Default picture formatting
        $this->picture['wrap'] = 0;
        $this->picture['wrapside'] = 0;
        $this->picture['abovetxt'] = 0;
        $this->picture['zorder'] = 0;
    }

    // }}}


    // DOCUMENT HEADER

    // {{{ addCustomHeader()

    /**
     * This method adds a custom RTF header.
     * The standard RTF formatting rules must be followed:
     * http://msdn.microsoft.com/library/en-us/dnrtfspec/html/rtfspec.asp
     *
     * @param string $header The header string
     * @return void
     * @access public
     */
    function addCustomHeader( $header )
    {
        $this->custom_headers .= $header;
    }

    // }}}


    // {{{ newFont()

    /**
     * This method adds a font to the list of available document fonts
     *
     * @param string $font_name The name of the font (e.g. Times New Roman)
     * @param string $font_family The font family (e.g. roman)
     * @param int $font_pitch The pitch of the font (0=default,1=fixed,2=variable)
     * @param int $font_charset The character set to be used (e.g. 0 for ANSI)
     * @return int The font id
     * @access public
     */
    function newFont( $font_name, $font_family = 'roman', $font_pitch = 0, $font_charset = 0 )
    {
        $font = '';
        if ( in_array( strtolower( $font_family ), $GLOBALS['_RTF_font_families'] ) ) {
            // A proper font family was specified
            $font .= '\f' . strtolower( $font_family );
        } else {
            $this->error("Illegal parameter: Font family '" . $font_family . "' unknown", ERROR);
        }

        if ( ( $font_pitch >= 0 ) && ( $font_pitch <= 2 ) ) {
            // A proper font pitch was specified
            $font .= '\fprq' . $font_pitch;
        } else {
            $this->error("Illegal parameter: Font pitch '" . $font_pitch . "' not allowed", ERROR);
        }

        if ( in_array( $font_charset, $GLOBALS['_RTF_font_charsets'] ) ) {
            // A proper font charset was specified
            $font .= '\fcharset' . $font_charset;
        } else {
            $this->error("Illegal parameter: Font charset '" . $font_charset . "' unknown", ERROR);
        }

        // Add the font
        $this->header['fonts'][] = $font . ' ' . $font_name;

        // Return font number
        return sizeof( $this->header['fonts'] )-1;
    }

    // }}}


    // {{{ setDefaultFont()

    /**
     * This method sets the default document font
     *
     * @param int $font_number The number of the font
     * @return void
     * @access public
     */
    function setDefaultFont( $font_number )
    {
        // Check that font number exists
        if ( ( $font_number >= 0 ) && ( $font_number <= sizeof( $this->header['fonts'] ) ) ) {
            $this->header['default_font'] = $font_number;
        } else {
            $this->error("Illegal parameter: Font does not exist (must use newFont())", ERROR);
        }
    }

    // }}}


    // {{{ findFont()

    /**
     * This method finds a font number by its name
     *
     * @param string $font The font name
     * @return int The number of the font in the font list, false otherwise
     * @access public
     */
    function findFont( $font )
    {
        foreach( $this->header['fonts'] as $font_number => $font_spec ) {
            if ( substr( $font_spec, - strlen( $font ) ) == $font ) {
                return $font_number;
            }
        }
        return false;
    }

    // }}}


    // {{{ newColor()

    /**
     * This method adds a color to the list of available document colors
     *
     * @param int $red The amount of red in the RGB scheme
     * @param int $green The amount of green in the RGB scheme
     * @param int $blue The amount of blue in the RGB scheme
     * @return int The number of the color
     * @access public
     */
    function newColor( $red, $green = -1, $blue = -1 )
    {
        if ( $green == -1 ) {
            // If only one parameter, then treat that parameter
            // as the hexadecimal representation for RGB
            $RGB = $red;
            $red = ( $RGB &0xFF0000 ) >> 16;
            $green = ( $RGB &0x00FF00 ) >> 8;
            $blue = $RGB &0x0000FF;
        }
        $this->header['colors'][] = '\red' . $red . '\green' . $green . '\blue' . $blue;
        return sizeof( $this->header['colors'] ); // 1-based. 0 is the default which is auto coloring
    }

    // }}}


    // {{{ newFormat()

    /**
     * This method adds a paragraph format
     *
     * @param array $fmt Paragraph formatting properties
     * @return RTFParagraphFormat $fmt        Paragraph format
     * @access public
     */
    function &newFormat()
    {
        return $this->formats[] = &new RTFParagraphFormat( $this, sizeof( $this->formats ) );
    }

    // }}}


    // DOCUMENT INFO


    // {{{ setTitle()

    /**
     * This method sets the document title
     *
     * @param string $title The title
     * @return void
     * @access public
     */
    function setDocTitle( $title )
    {
        $this->doc_info['title'] = $title;
    }

    // }}}


    // {{{ setDocSubject()

    /**
     * This method sets the document subject
     *
     * @param string $subject The subject
     * @return void
     * @access public
     */
    function setDocSubject( $subject )
    {
        $this->doc_info['subject'] = $subject;
    }

    // }}}


    // {{{ setAuthor()

    /**
     * This method sets the document author
     *
     * @param string $author The author
     * @return void
     * @access public
     */
    function setDocAuthor( $author )
    {
        $this->doc_info['author'] = $author;
    }

    // }}}


    // {{{ setDocManager()

    /**
     * This method sets the document's author's manager
     *
     * @param string $manager The manager
     * @return void
     * @access public
     */
    function setDocManager( $manager )
    {
        $this->doc_info['manager'] = $manager;
    }

    // }}}


    // {{{ setDocCompany()

    /**
     * This method sets the document company
     *
     * @param string $company The companyu
     * @return void
     * @access public
     */
    function setDocCompany( $company )
    {
        $this->doc_info['company'] = $company;
    }

    // }}}


    // {{{ setDocCategory()

    /**
     * This method sets the document category
     *
     * @param string $title The category
     * @return void
     * @access public
     */
    function setDocCategory( $category )
    {
        $this->doc_info['category'] = $category;
    }

    // }}}


    // {{{ setDocKeywords()

    /**
     * This method sets the document keywords
     *
     * @param string $keywords The keywords
     * @return void
     * @access public
     */
    function setDocKeywords( $keywords )
    {
        $this->doc_info['keywords'] = $keywords;
    }

    // }}}


    // {{{ setDocVersion()

    /**
     * This method sets the document version
     *
     * @param int $version The version
     * @return void
     * @access public
     */
    function setDocVersion( $version )
    {
        $this->doc_info['version'] = $version;
    }

    // }}}


    // {{{ setDocComment()

    /**
     * This method sets the document comment
     *
     * @param string $comment The comment
     * @return void
     * @access public
     */
    function setDocComment( $comment )
    {
        $this->doc_info['doccomm'] = $comment;
    }

    // }}}


    // {{{ setDocCreatTime()

    /**
     * This method sets the document creation time
     *
     * @param string $creatim The creation time
     * @return void
     * @access public
     */
    function setDocCreatTime( $creattim )
    {
        $this->doc_info['creatim'] = $creattim;
    }

    // }}}


    // {{{ addCustomDocInfo()

    /**
     * This method adds a custom document info property.
     * The standard RTF formatting rules must be followed.
     * http://msdn.microsoft.com/library/en-us/dnrtfspec/html/rtfspec.asp
     *
     * @param string $doc_info The doc_info property
     * @return void
     * @access public
     */
    function addCustomDocInfo( $doc_info )
    {
        $this->custom_doc_info .= $doc_info;
    }

    // }}}


    // DOCUMENT FORMATTING


    // {{{ setOrientation()

    /**
     * This method sets the document orientation
     *
     * @param string $orientation The document orientation
     * @return false True is successful, false otherwise
     * @access public
     */
    function setOrientation( $orientation )
    {
        if ( $orientation == 'portrait' ) {
            $this->doc_fmt['landscape'] = false;
        } elseif ( $orientation == 'landscape' ) {
            $this->doc_fmt['landscape'] = true;
        } else {
            $this->error("Illegal parameter: Document orientation '" . $orientation . "' unknown (must be portrait or landscape)", ERROR);
        }
    }

    // }}}



    // {{{ setPageSize()

    /**
     * This method sets the document size
     *
     * @param string $width The document width | a4 | letter
     * @param string $height The document height
     * @return void
     * @access public
     */
    function setPageSize( $width, $height = 0 )
    {
        // width          height
        // 10 x 14        14400         20160
        // A3             16840         23800
        // A5              8400         11900
        // B4             14580         20640
        // B5             10320         14580
        // DL              6236         12472
        // Executive      10440         15120
        // Folio          12240         18720
        // Ledger         24480         15840
        // Legal          12240         20160
        // Monarch         5580         10800
        // Quarto         12200         15600
        // Statement       7920         12240
        // Tabloid        15840         24480
        switch ( $width ) {
            case 'a4':
                $this->doc_fmt['width'] = 11906;
                $this->doc_fmt['height'] = 16838;
                break;
            case 'letter':
                $this->doc_fmt['width'] = 12240;
                $this->doc_fmt['height'] = 15840;
                break;
            default:
                $this->doc_fmt['width'] = $this->getTwips( $width );
                $this->doc_fmt['height'] = $this->getTwips( $height );
        }
    }

    // }}}


    // {{{ setPageMargins()

    /**
     * This method sets the document margins
     *
     * @param string $left The left margin
     * @param string $right The right margin
     * @param string $top The top margin
     * @param string $bottom The bottom margin
     * @return void
     * @access public
     */
    function setPageMargins( $left, $right, $top, $bottom )
    {
        $this->setLeftMargin( $left );
        $this->setRightMargin( $right );
        $this->setTopMargin( $top );
        $this->setBottomMargin( $bottom );
    }

    // }}}


    // {{{ setLeftMargin()

    /**
     * This method sets the document left margin
     *
     * @param string $left The left margin
     * @return void
     * @access public
     */
    function setLeftMargin( $left )
    {
        $this->doc_fmt['margin_left'] = $this->getTwips( $left );
    }

    // }}}


    // {{{ setRightMargin()

    /**
     * This method sets the document right margin
     *
     * @param string $right The right margin
     * @return void
     * @access public
     */
    function setRightMargin( $right )
    {
        $this->doc_fmt['margin_right'] = $this->getTwips( $right );
    }

    // }}}


    // {{{ setTopMargin()

    /**
     * This method sets the document top margin
     *
     * @param string $top The top margin
     * @return void
     * @access public
     */
    function setTopMargin( $top )
    {
        $this->doc_fmt['margin_top'] = $this->getTwips( $top );
    }

    // }}}


    // {{{ setLeftMargin()

    /**
     * This method sets the document bottom margin
     *
     * @param string $bottom The bottom margin
     * @return void
     * @access public
     */
    function setBottomMargin( $bottom )
    {
        $this->doc_fmt['margin_bottom'] = $this->getTwips( $bottom );
    }

    // }}}


    // {{{ setDefTab()

    /**
     * This method sets the default tab space
     *
     * @param string $tab The tab space
     * @return void
     * @access public
     */
    function setDefTab( $tab )
    {
        $this->doc_fmt['deftab'] = $this->getTwips( $tab );
    }

    // }}}


    // {{{ setDefLang()

    /**
     * This method sets the default document language
     *
     * @param int $lang The document language (default = 1033)
     * @return void
     * @access public
     */
    function setDefLang( $lang )
    {
        $this->doc_fmt['deflang'] = $lang;
    }

    // }}}


    // {{{ setDocWidowCtrl()

    /**
     * This method sets the default document language
     *
     * @param bool $widowctrl True if Widow and orphan control is on
     * @return void
     * @access public
     */
    function setDocWidowCtrl( $widowctrl )
    {
        $this->doc_fmt['widowctrl'] = $widowctrl;
    }

    // }}}


    // {{{ addCustomDocFormat()

    /**
     * This method adds a custom document formatting property.
     * The standard RTF formatting rules must be followed.
     * http://msdn.microsoft.com/library/en-us/dnrtfspec/html/rtfspec.asp
     *
     * @param string $doc_fmt The formatting property
     * @return void
     * @access public
     */
    function addCustomDocFormat( $doc_fmt )
    {
        $this->custom_doc_info .= $doc_fmt;
    }

    // }}}



    // SECTION



    // {{{ endBlock()

    /**
     * This method ends an RTF block
     *
     * @return void
     * @access public
     */
    function endBlock( $nl = true )
    {
        if ( ( $this->in_header ) || ( $this->in_footer ) || ( $this->in_paragraph ) ) {
            $this->addToDoc( $this->paragraph_text );

            if ( isset( $this->paragraph_fmt->plist[$this->paragraph_fmt->plist['recentlevel'] + 1]['bg'] ) )
                if ( $this->paragraph_fmt->plist[$this->paragraph_fmt->plist['recentlevel'] + 1] > 0 ) {
                    // Word trick to set background color for the bullet/number in a list
                    $this->addToDoc( '\highlight' . $this->paragraph_fmt->plist[$this->paragraph_fmt->plist['recentlevel'] + 1]['bg'] );
                    $this->paragraph_fmt->plist['recentlevel'] = -1;
                }

            // {  <-- Just an extra left brace to compensate for some PHP editors faulty class browsers
            // The next line contains the right brace

            $this->addToDoc( NL );
            if ( $nl )
                $this->addToDoc( '\par' );
            $this->addToDoc( '}' . NL );

            $this->in_header = false;
            $this->in_footer = false;
            $this->in_paragraph = false;

            $this->paragraph_text = '';
        } else {
            // ERROR;
        }
    }

    // }}}

    // {{{ newHeader()

    /**
     * This method starts a header section
     *
     * @param RTFParagraphFormat $fmt Paragraph format specifier
     * @param string $pos Header position from top of page
     * @return bool True if not already within a header, false otherwise
     * @access public
     */
    function newHeader( $fmt, $pos = '720' )
    {
        if ( $this->in_header ) {
            $this->error("Header already started. Error", ERROR);
        } else {
            $this->endBlock();
            $this->addToDoc( '{\headery' . $this->getTwips( $pos ) . '\header\pard' );
            $this->writeFormat( $fmt ); // Add paragraph formatting
            $this->addToDoc( NL );
            $this->in_header = true;
            $this->in_footer = false;
            $this->in_paragraph = false;
        }
    }

    // }}}

    // {{{ newFooter()

    /**
     * This method starts a footer section
     *
     * @param RTFParagraphFormat $fmt Paragraph format specifier
     * @param string $pos Footer position from bottom of page
     * @return bool True if not already within a footer, false otherwise
     * @access public
     */
    function newFooter( $fmt, $pos = '720' )
    {
        if ( ( $this->in_footer ) ) {
            $this->error("Footer already started. Error", ERROR);
        } else {
            $this->endBlock();
            $this->addToDoc( '{\footery' . $this->getTwips( $pos ) . '\footer\pard' );
            $this->writeFormat( $fmt ); // Add paragraph formatting
            $this->addToDoc( NL );
            $this->in_header = false;
            $this->in_footer = true;
            $this->in_paragraph = false;
        }
    }

    // }}}


    // {{{ newSection()

    /**
     * This method starts a new section, resetting header, footer and columnation
     *
     * @return void
     * @access public
     */
    function newSection()
    {
        $this->endBlock();
        $this->addToDoc( '\sect\sectd' );
        $this->addToDoc( NL );
    }

    // }}}


    // {{{ newParagraph()

    /**
     * This method starts a new paragraph, resetting paragraph formatting attributes
     *
     * @param RTFParagraphFormat $fmt The format specifier
     * @param boolean $pageBreak Break page before this paragraph
     * @param boolean $keepWithNext Keep this paragraph with next
     * @return void
     * @access public
     */
    function newParagraph( &$fmt, $pageBreak = false, $keepWithNext = false )
    {
        // End any previous block
        $this->endBlock();

        if ( $fmt == null ) {
            $fmt =& $this->paragraph_fmt;
            if ( $fmt == null )
                $this->error("New paragraph requested without specifying format and with no prior used format available", ERROR);
        }

        if ( $this->paragraph_fmt->id != $fmt->id ) {
            // New paragraph format specified
            if (isset($fmt->plist['type']))
            {
                    // The new format is a list.
                    // Reset the list level.
                    $fmt->plist['currentlevel'] = 0;
            }
        }


        $this->addToDoc( '{\pard ' );
        if ( $this->numTables > 0 ) {
            $this->addToDoc( '\intbl ' );
        }

        $fmt->pageBreak = $pageBreak;
        $fmt->keepWithNext = $keepWithNext;
        $this->writeFormat( $fmt );

        // Save current paragraph format
        $this->paragraph_fmt =& $fmt;
        // We are now inside a paragraph
        $this->in_paragraph = true;
    }

    // }}}


    // {{{ newPar()

    /**
     * This method starts a new paragraph, resetting paragraph formatting attributes
     *
     * @return void
     * @access public
     */
    function newPar() {
            $noref = null;
            $this->newParagraph($noref);
    }

    // }}}


    // {{{ setListLevel()

    /**
     * This method sets the current list level.
     *
     * @param int $level List level (1-9)
     * @return bool True if new level is set, false otherwise
     * @access public
     */
    function setListLevel( $level )
    {
        // Check that current paragraph is a list
        if ( isset( $this->paragraph_fmt->plist['type'] ) ) {
            if ( $this->paragraph_fmt->plist['currentlevel'] != ( $level-1 ) ) {
                // Save previous level for endBlock()
                $this->paragraph_fmt->plist['recentlevel'] = $this->paragraph_fmt->plist['currentlevel'];
                // Set new level
                $this->paragraph_fmt->plist['currentlevel'] = $level-1;
                // Start new paragraph
                $this->newPar();
            }
        } else {
                $this->error("Cannot set list level if list format is not in use", ERROR);
        }
    }

    // }}}


    // {{{ useFont()

    /**
     * This method outputs the paragraph format
     *
     * @param int $fmt_idx The 1-based format index (refers to an added format)
     * @return void
     * @access public
     */
    function useFont($font, $fontsize = 12, $fontstyle = '')
    {
            if ($this->usingDefaultFont) {
                    $this->paragraph_text .= '{';
            } else {
                    $this->paragraph_text .= '}{';
            }
            if ( ( $font < 0 ) || ( $font >= sizeof( $this->header['fonts'] ) ) ) {
                $this->error("Illegal parameter. Font unknown", ERROR);
            }
            $this->paragraph_text .= '\f' . $font;
            $this->paragraph_text .= '\fs' . 2*$fontsize;
            if ($fontstyle != '') {
                $this->paragraph_text .= $this->parseFontStyle($fontstyle);
            }
            $this->paragraph_text .= ' ';

            $this->usingDefaultFont = false;
    }
    // }}}


    // {{{ useDefaultFont()

    /*
     * This method switches back to the default paragraph font
     *
     * @return         void
     * @access         public
     */
    function useDefaultFont()
    {
        if (!$this->usingDefaultFont) {
                $this->paragraph_text .= '}';
                $this->usingDefaultFont = true;
        }
    }

    // }}}


    // {{{ writeFormat()

    /**
     * This method outputs the paragraph format
     *
     * @param RTFParagraphFormat $fmt The format
     * @return string
     * @access public
     */
    function writeFormat( $fmt )
    {
        // If in table, ignore => The format will be output separately by tableRowEnd()
        //if ($this->numTables == 0) {
            $this->addToDoc( $this->getFormat($fmt) );
        //}
    }

    // }}}


    // {{{ getFormat()

    /**
     * This method returns the paragraph format
     *
     * @param RTFParagraphFormat $fmt The format
     * @return string
     * @access public
     */
    function getFormat( $fmt )
    {
        $fstr = '';

        // Add paragraph border
        $fstr .= $fmt->border;
                
        // Page break before this paragraph
        if ( $fmt->pageBreak ) {
            $fstr .= '\pagebb';
        }
        // Keep this paragraph with the next
        if ( $fmt->keep ) {
            $fstr .= '\keepn';
        }
        // List
        if ( isset( $fmt->plist['type'] ) ) {
            $fstr .= '\ls' . $fmt->id;
            $fstr .= '\ilvl' . $fmt->plist['currentlevel'];
        }
        // Paragraph alignment
        $fstr .= $fmt->align;
        // First indent
        if ( isset( $fmt->plist['type'] ) ) {
            // Override paragraph settings if it is a list
            $fstr .= '\fi' . $fmt->plist[$fmt->plist['currentlevel'] + 1]['fi'];
        } elseif ( $fmt->fi > 0 ) {
            $fstr .= '\fi' . $fmt->fi;
        }
        // Left indent
        if ( isset( $fmt->plist['type'] ) ) {
            // Override paragraph settings if it is a list
            $fstr .= '\li' . $fmt->plist[$fmt->plist['currentlevel'] + 1]['li'];
        } elseif ( $fmt->li > 0 ) {
            $fstr .= '\li' . $fmt->li;
        }
        // Right indent
        if ( isset( $fmt->plist['type'] ) ) {
            // Override paragraph settings if it is a list
            $fstr .= '\ri' . $fmt->plist[$fmt->plist['currentlevel'] + 1]['ri'];
        } elseif ( $fmt->ri > 0 ) {
            $fstr .= '\ri' . $fmt->ri;
        }

        if ( $fmt->sb > 0 ) {
            $fstr .= '\sb' . $fmt->sb; // Space before
        }
        if ( $fmt->sa > 0 ) {
            $fstr .= '\sa' . $fmt->sa; // Space after
        }
        if ( $fmt->slmult ) {
            $fstr .= '\sl' . $fmt->sl; // Line space
            $fstr .= '\slmult1'; // Line space is a multiple
        } else {
            if ( $fmt->sl > 0 ) {
                $fstr .= '\sl-' . $fmt->sl; // Line space - use exact value
            }
        }
        if ( ! ( $fmt->hyphenate ) ) {
            $fstr .= '\hyphpar0'; // Hyphenation off
        }
        // Add paragraph tabs
        $fstr .= $fmt->tabs;

        // Add paragraph shading
        //  $fstr .= $fmt->shade;

        // Add paragraph font
        if ( $fmt->font != $this->header['default_font'] ) {
            $fstr .= '\f' . $fmt->font;
        }
        if ( $fmt->fontsize != 12 ) {
            $fstr .= '\fs' . 2 * $fmt->fontsize;
        }
        $fstr .= $fmt->fontstyle;

		// Character spacing
		$fstr .= $fmt->scale;
		$fstr .= $fmt->spacing;
		$fstr .= $fmt->position;
		

        // Add paragraph coloring
        if ( $fmt->fg > 0 ) {
            $fstr .= '\cf' . $fmt->fg;
        }
        if ( $fmt->bg > 0 ) {
            // According to RTF Spec 1.6, \cb should be enough and also is
            // for OpenOffice. However, Word2003 seems to prefer \highlight.
            $fstr .= '\cb' . $fmt->bg . '\highlight' . $fmt->bg;
        }

        // Emit delimiter between command and other text
        $fstr .= ' ';
        return $fstr;
    }

    // }}}


    // {{{ write()

    /**
     * This method outputs text
     *
     * @param string $txt The text to display
     * @param bool $convert Indicates if text should be converted
     * @return void
     * @access public
     */
    function write( $txt, $convert = true )
    {
        if ( $this->numTables > 0 ) {
            if (! ( $this->in_paragraph ) ) {
                $this->error("Must start paragraph before emitting text", ERROR);
            }
        } else {
            if (! ( ( $this->in_header ) || ( $this->in_footer ) || ( $this->in_paragraph ) ) ) {
                $this->error("Must start paragraph, header or footer before emitting text", ERROR);
            }
        }

        $pos = strpos( $txt, '\np' );
        if ( $pos !== false ) {
            $head = substr( $txt, 0, $pos );
            $tail = substr( $txt, $pos + 3 );
        } else {
            $head = $txt;
            $tail = '';
        }
        if ($convert) {
            $txt = $this->convertText( $head );
        } else {
            $txt = $head;
        }

        if ( $this->numTables == 0 ) {
            // Not in table
            if ($this->usingDefaultFont) {
                $this->paragraph_text .= '{' . $txt . '}'; // Put within a group to handle
            } else {
                $this->paragraph_text .= $txt; // Other font usage handles group
            }
            // character formatting commands
        } elseif ( $this->tableRows[$this->lastRowIdx]['firstcol'] > $this->lastColIdx ) {
            // Ignore text to put in row
        } else {
            // Remember text to put in column
            $this->tableCols[$this->lastColIdx]['text'] .= $txt;
        }
        // If /np was specified, start a new paragraph
        if ( $pos !== false ) {
            $this->newPar();
        }
        // Process text after first occurence /np (of there is any)
        if ( $tail != '' ) {
            $this->write( $tail );
        }
    }

    // }}}


    // {{{ writeln()

    /**
     * This method outputs text and ends it with a newline
     *
     * @param string $txt The text to output
     * @return void
     * @access public
     */
    function writeln( $txt = '' )
    {
            $this->write( $txt );
            $this->write( '\line ' . NL, false);
    }

    // }}}


    // {{{ writeField()

    /**
     * This method outputs a field
     *
     * @param string $field The field specifier
     * @return void
     * @access public
     */
    function writeField( $field )
    {
        // Write unconverted text string
        $this->write('{\field{\*\fldinst{ ' . $this->convertText($field) . ' }}{\fldrslt {\noproof }}}', false);
    }

    /// }}}

	// {{{ setPictureWrapping()
    /**
     * This method sets picture wrapping.
     *
     * @param String $type Wrapping type:
     * 						[o]ver
     * 						[u]nder
     * 						[t]op[b]otton
     * 						[ar]around
     * 						[ti]ght
     * 						[th]rough
     * @param String $side Sides for around and tight wrapping
     * 						[b]oth
     * 						[l]eft
     * 						[r]ight
     * 						[w]idest
     * @return void
     * @access public
     */
    function setPictureWrapping($type, $side = '')
    {
    	switch ($type) {
    		case 'o':
    		case 'over':
    			$this->picture['wrap'] = 3;
    			$this->picture['abovetxt'] = 0; 
    			break;
    		case 'u':
    		case 'under':
    			$this->picture['wrap'] = 3;
    			$this->picture['abovetxt'] = 1; 
    			break;
    		case 'tb':
    		case 'topbottom':
    			$this->picture['wrap'] = 1;
    			break;
    		case 'ar':
    		case 'around':
    			$this->picture['wrap'] = 2;
    			break;
    		case 'ti':
    		case 'tight':
    			$this->picture['wrap'] = 4;
    			break;
    		case 'th':
    		case 'through':
    			$this->picture['wrap'] = 5;
    			break;
    		default:
                $this->error("Unknown picture wrapping style '" . $style, ERROR);
    	}

    	if (($type == 'ar') || ($type == 'ti'))
	    	switch ($side) {
	    		case 'b':
	    		case 'both':
	    			$this->picture['wrapside'] = 0;
	    			break;
	    		case 'l':
	    		case 'left':
	    			$this->picture['wrapside'] = 1;
	    			break;
	    		case 'r':
	    		case 'right':
	    			$this->picture['wrapside'] = 2;
	    			break;
	    		case 'w':
	    		case 'widest':
	    			$this->picture['wrapside'] = 3;
	    			break;
	    		default:
	                $this->error("Unknown wrapping picture side '" . $side, ERROR);
	    	}
    	
    }
	

    // {{{ addPicture()

    /**
     * This method adds a picture.
     *
     * @return void
     * @access public
     */
    function addPicture($filename, $width = '', $height = '',  
    					$anchorx ='n', $anchory = '', $top = '', $left = '' )
    {
        $this->endBlock();
        
		if ($anchorx != 'n') {		
		
	       	$this->addToDoc('{\shp' . NL);
	       	$this->addToDoc('{\*\shpinst' . NL);
	       	$this->addToDoc('\shptop' . $this->getTwips($top));
	       	$this->addToDoc('\shpleft' . $this->getTwips($left));
	       	$this->addToDoc('\shpright' . ($this->getTwips($left) + $this->getTwips($width)));
	       	$this->addToDoc('\shpbottom' . ($this->getTwips($top) + $this->getTwips($height)));
	
	       	$this->addToDoc('\shpfhdr0' . NL);
	
			switch ($anchorx) {
				case 'p':
				case 'page':
			       	$this->addToDoc('\shpbxpage' . NL);
			       	break;
				case 'm':
				case 'margin':
			       	$this->addToDoc('\shpbxmagin' . NL);
			       	break;
				case 'c':
				case 'column':
			       	$this->addToDoc('\shpbxcolumn' . NL);
			       	break;
			}        
			switch ($anchory) {
				case 'p':
				case 'page':
			       	$this->addToDoc('\shpbypage' . NL);
			       	break;
				case 'm':
				case 'margin':
			       	$this->addToDoc('\shpbymagin' . NL);
			       	break;
				case 'c':
				case 'column':
			       	$this->addToDoc('\shpbycolumn' . NL);
			       	break;
			}     
			$this->addToDoc('\shpwr' .  $this->picture['wrap']);
			$this->addToDoc('\shpwrk' .  $this->picture['wrapside']);
			$this->addToDoc('\shpfblwtxt' . $this->picture['abovetxt']);
			$this->addToDoc('\shpz' . $this->picture['zorder']);
		} else {
			// Picture in line with text
			$this->addToDoc('{\*\shppict');
			$this->addToDoc('{\pict');
			$this->addToDoc('{\*\picprop');
		}

		$this->addToDoc('{\sp{\sn shapeType}{\sv 75}}');		   
		$this->addToDoc('{\sp{\sn fFlipH}{\sv 0}}');		   
		$this->addToDoc('{\sp{\sn fFlipV}{\sv 0}}');
		$this->addToDoc('{\sp{\sn pibFlags}{\sv 2}}');
		$this->addToDoc('{\sp{\sn fRecolorFillAsPicture}{\sv 0}}');
		$this->addToDoc('{\sp{\sn fUseShapeAnchor}{\sv 0}}');
		$this->addToDoc('{\sp{\sn fLine}{\sv 0}}');
		$this->addToDoc('{\sp{\sn fLayoutInCell}{\sv 1}}');
		
		if ($anchorx == 'n') {
			$this->addToDoc('}');
		} else {
			$this->addToDoc('{\sp{\sn pib}{\sv');
			$this->addToDoc('{\pict');
		}
		
		$this->addToDoc('\picscalex100');
		$this->addToDoc('\picscaley100');
		$this->addToDoc('\piccropl0');
		$this->addToDoc('\piccropr0');
		$this->addToDoc('\piccropt0');
		$this->addToDoc('\piccropb0');

		$this->addToDoc('\picw' . $this->getTwips($width));
		$this->addToDoc('\pich' . $this->getTwips($height));
		$this->addToDoc('\picwgoal' . $this->getTwips($width));
		$this->addToDoc('\pichgoal' . $this->getTwips($height));
		$this->addToDoc('\jpegblip' . NL);
		
		$handle = fopen ($filename, "rb");
		if ($handle)
	  		while (!feof($handle))
				$this->addToDoc(bin2hex(fread($handle, 64)) . NL);
		fclose($handle);
		
		if ($anchorx != 'n')
			$this->addToDoc('}}}');
		$this->addToDoc('}}');
		//$this->addToDoc('}');
	}

    // }}}



    // {{{ shapeStart()

    /**
     * This method ends a shape definition and outputs the shape.
     *
     * @return void
     * @access public
     */
    function shapeStart()
    {
        $this->endBlock();

		        

        $this->numTables++;     // Increase table nesting level
        if ( $this->numTables > 1 ) {
            // Set the new tables position in the parent table
            $this->tables[$this->numTables]['parent_col'] = $this->lastColIdx;
            $this->tables[$this->numTables]['parent_row'] = $this->lastRowIdx;
        }
        // Initialize row and content
        $this->tables[$this->numTables]['firstrow'] = $this->lastRowIdx + 1;
        $this->tables[$this->numTables]['rows'] = 0;
        $this->tables[$this->numTables]['content'] = '';
        
        // Default padding (margins)
        $this->tables[$this->numTables]['cellmargint'] = 108; 
        $this->tables[$this->numTables]['cellmarginb'] = 108;
        $this->tables[$this->numTables]['cellmarginl'] = 108;
        $this->tables[$this->numTables]['cellmarginr'] = 108;
        
    }

    // }}}


    // {{{ writeHyperLink()

    /**
     * This method outputs a hyperlink
     *
     * @param string $text The text to display
     * @param string $url The url
     * @return void
     * @access public
     */
    function writeHyperLink( $text, $url )
    {
		echo $text;
		echo $url;
    }

    // }}}


    // TABLE


    // {{{ tableStart()

    /**
     * This method starts a new table.
     * A new table can be started before an already started table is finalized
     * in order to create nested tables.
     *
     * @return void
     * @access public
     */
    function tableStart()
    {
        $this->endBlock();

        $this->numTables++;     // Increase table nesting level
        if ( $this->numTables > 1 ) {
            // Set the new tables position in the parent table
            $this->tables[$this->numTables]['parent_col'] = $this->lastColIdx;
            $this->tables[$this->numTables]['parent_row'] = $this->lastRowIdx;
        }
        // Initialize row and content
        $this->tables[$this->numTables]['firstrow'] = $this->lastRowIdx + 1;
        $this->tables[$this->numTables]['rows'] = 0;
        $this->tables[$this->numTables]['content'] = '';
        
        // Default padding (margins)
        $this->tables[$this->numTables]['cellmargint'] = 108; 
        $this->tables[$this->numTables]['cellmarginb'] = 108;
        $this->tables[$this->numTables]['cellmarginl'] = 108;
        $this->tables[$this->numTables]['cellmarginr'] = 108;
        
    }

    // }}}


    // {{{ tableEnd()

    /**
     * This method finalizes the last started table
     *
     * @return void
     * @access public
     */
    function tableEnd()
    {
        if ( $this->numTables == 1 ) {
            // Outmost table. Just emit its content.
            // Must decrease table count before adding to document
            // or the content will be added to the table content variable
            $this->numTables--;
            $this->addToDoc( $this->tables[1]['content'] );
        } else {
            // This is a nested table
            // Add it to the parent cell
            // Go through rows in this table
            $subt = '';
            for( $i = $this->tables[$this->numTables]['firstrow'];
                $i < $this->tables[$this->numTables]['firstrow'] + $this->tables[$this->numTables]['rows']; $i++ ) {
                // Begin definition of nested table
                $subt .= '\pard \ql \li0\ri0\widctlpar\intbl\aspalpha\aspnum\faauto\adjustright\rin0\lin0';
                // Set nesting level
                $subt .= '\itap' . $this->numTables;
                // Set
                $subt .= $this->tables[$this->numTables]['format'];
                $subt .= '{' . $this->tableRows[$i]['format'] . NL;
                // Go through cols for this row
                for( $j = $this->tableRows[$i]['firstcol'];
                    $j < $this->tableRows[$i]['firstcol'] + $this->tableRows[$i]['cols']; $j++ ) {
                    $subt .= '{' . $this->tableCols[$j]['format'];
                    $subt .= $this->convertText($this->tableCols[$j]['text']) . '\nestcell{\nonesttables\par }';
                    $subt .= '}';
                }
                $subt .= '}' . NL;
                $subt .= '\pard \ql \li0\ri0\widctlpar\intbl\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap' . $this->numTables;
                // Nested table properties
                $subt .= '{{\*\nesttableprops';
                $subt .= $this->getTableRowDef( $i );
                $subt .= $this->getTableCellDef( $i );
                $subt .= '\nestrow}';
                $subt .= '{\nonesttables \par }';
                $subt .= '}' . NL;
            }
            $subt .= $this->getTableRowDef( $this->lastRowIdx );
            $subt .= $this->getTableCellDef( $this->tables[$this->numTables]['parent_row'] );

            for( $i = $this->tables[$this->numTables]['firstrow'];
                $i < $this->tables[$this->numTables]['firstrow'] + $this->tables[$this->numTables]['rows']; $i++ ) {
                for( $j = $this->tableRows[$i]['firstcol'];
                    $j < $this->tableRows[$i]['firstcol'] + $this->tableRows[$i]['cols']; $j++ )
                unset ( $this->tableCols[$j] );
                unset ( $this->tableRows[$i] );
            }

            $p = $this->tables[$this->numTables]['parent_col'];
            // echo 'parent_col: '.$p.'<br>';
            $this->tableCols[$p]['child'] = $subt;
            $this->lastRowIdx -= $this->tables[$this->numTables]['rows'];

            $this->numTables--;
        }
        // unset ( $tables[$this->numTables] );
    }

    // }}}

    // {{{ tableRowStart()

    /**
     * This method starts a new table row.
     *
     * @param int $numcols Number of columns in this row
     * @param string/int $pos Position of leftmost edge in format accepted by getTwips()
     * @param bool $nobreak Do not break row across pages
     * @param bool $keepwnext Keep this row in the same paga as the next row
     * @return void
     * @access public
     */
    function tableRowStart( $numcols, $pos = 0, $nobreak = true, $keepwnext = false )
    {
        $this->lastRowIdx++;
        $this->tables[$this->numTables]['rows']++;

        // Number of columns in this row
        $this->tableRows[$this->lastRowIdx]['cols'] = $numcols;
        $this->tableRows[$this->lastRowIdx]['firstcol'] = $this->lastColIdx + 1;

        // Default
        $this->tableRows[$this->lastRowIdx]['gap'] = 108;

        // Position of leftmost edge
        $this->tableRows[$this->lastRowIdx]['pos'] = $this->getTwips($pos);
	
		// Row height
		$this->tableRows[$this->lastRowIdx]['height'] = 0;
		
        // Break row across pages (or not)
        if ( $nobreak ) {
            $this->tableRows[$this->lastRowIdx]['keeptogether'] = '\trkeep';
        }

        // Keep row in same page as next row (or not)
        if ( $keepwnext ) {
            $this->tableRows[$this->lastRowIdx]['keeptogether'] = '\trkeepfollow';
        }

        // Set default row borders
        $this->tableRows[$this->lastRowIdx]['b_top'] = $this->defaultTableRowBorder['b_top'];
        $this->tableRows[$this->lastRowIdx]['b_top_style'] = $this->defaultTableRowBorder['b_top_style'];
        $this->tableRows[$this->lastRowIdx]['b_top_color'] = $this->defaultTableRowBorder['b_top_color'];
        $this->tableRows[$this->lastRowIdx]['b_bottom'] = $this->defaultTableRowBorder['b_bottom'];
        $this->tableRows[$this->lastRowIdx]['b_bottom_style'] = $this->defaultTableRowBorder['b_bottom_style'];
        $this->tableRows[$this->lastRowIdx]['b_bottom_color'] = $this->defaultTableRowBorder['b_bottom_color'];
        $this->tableRows[$this->lastRowIdx]['b_left'] = $this->defaultTableRowBorder['b_left'];
        $this->tableRows[$this->lastRowIdx]['b_left_style'] = $this->defaultTableRowBorder['b_left_style'];
        $this->tableRows[$this->lastRowIdx]['b_left_color'] = $this->defaultTableRowBorder['b_left_color'];
        $this->tableRows[$this->lastRowIdx]['b_right'] = $this->defaultTableRowBorder['b_right'];
        $this->tableRows[$this->lastRowIdx]['b_right_style'] = $this->defaultTableRowBorder['b_right_style'];
        $this->tableRows[$this->lastRowIdx]['b_right_color'] = $this->defaultTableRowBorder['b_right_color'];
        $this->tableRows[$this->lastRowIdx]['b_inside'] = $this->defaultTableRowBorder['b_inside'];
        $this->tableRows[$this->lastRowIdx]['b_inside_style'] = $this->defaultTableRowBorder['b_inside_style'];
        $this->tableRows[$this->lastRowIdx]['b_inside_color'] = $this->defaultTableRowBorder['b_inside_color'];

        // Set automatic coloring
        $this->currentCellColor = 0;
    }

    // }}}

	// {{{ setTablePadding()

    /**
     * Sets defauklt cell margins or padding.
     *
     * @param String/int $top Top padding in format accepted by getTwips()
     * @param String/int $bottom Bottom padding in format accepted by getTwips()
     * @param String/int $left Left padding in format accepted by getTwips()
     * @param String/int $right Right padding in format accepted by getTwips()
     * @return void
     * @access public
     */
    function setTablePadding( $top, $bottom, $left, $right )
    {
        // Default padding (margins)
        $this->tables[$this->numTables]['cellmargint'] = $this->getTwips($top); 
        $this->tables[$this->numTables]['cellmarginb'] = $this->getTwips($bottom);
        $this->tables[$this->numTables]['cellmarginl'] = $this->getTwips($left);
        $this->tables[$this->numTables]['cellmarginr'] = $this->getTwips($right);
    }
	
    // }}}


	// {{{ setTableRowPadding()

    /**
     * Sets the cell margins or padding for the current row.
     *
     * @param String/int $top Top padding in format accepted by getTwips()
     * @param String/int $bottom Bottom padding in format accepted by getTwips()
     * @param String/int $left Left padding in format accepted by getTwips()
     * @param String/int $right Right padding in format accepted by getTwips()
     * @return void
     * @access public
     */
    function setTableRowPadding( $top, $bottom, $left, $right )
    {
        $this->tableRows[$this->lastRowIdx]['cellmargint'] = $this->getTwips($top); 
        $this->tableRows[$this->lastRowIdx]['cellmarginb'] = $this->getTwips($bottom);
        $this->tableRows[$this->lastRowIdx]['cellmarginl'] = $this->getTwips($left);
        $this->tableRows[$this->lastRowIdx]['cellmarginr'] = $this->getTwips($right);
    }
	
    // }}}


	// {{{ setTableRowHeight()

    /**
     * Sets the row height the current table row.
     *
     * @param String/int $height Height in format accepted by getTwips()
     * @return void
     * @access public
     */
    function setTableRowHeight( $height )
    {
        $this->tableRows[$this->lastRowIdx]['height'] = $this->getTwips($height); 
    }
	
    // }}}




    // {{{ tableEndRow()

    /**
     * This method ends the current table row.
     *
     * @return void
     * @access public
     */
    function tableRowEnd()
    {
        if ( $this->lastRowIdx == 0 ) {
            // No row to end!
            $this->error("No row to end");
        }

        if ( $this->lastRowIdx == 1 ) {
            // Top level, write row immediately

            $this->tables[$this->numTables]['content'] .= $this->getTableRowDef( $this->lastRowIdx );
            $this->tables[$this->numTables]['content'] .= $this->getTableCellDef( $this->lastRowIdx );

            // DEBUG:
            // echo 'Firstcol: '. $this->tableRows[$this->lastRowIdx]['firstcol'].'<br>';
            // echo 'Cols: '.$this->tableRows[$this->lastRowIdx]['cols'].'<br>';

            // Write cell content
            for( $i = $this->tableRows[$this->lastRowIdx]['firstcol'];
                $i < $this->tableRows[$this->lastRowIdx]['firstcol'] + $this->tableRows[$this->lastRowIdx]['cols']; $i++ ) {
                if ( isset( $this->tableCols[$i]['child'] ) ) {
                    $this->tables[$this->numTables]['content'] .= $this->tableCols[$i]['child'];
                    // unset ($this->tableCols[$i]['child']);
                }
                //$this->tables[$this->numTables]['content'] .= $this->getFormat($this->paragraph_fmt) . NL;
//                $this->tables[$this->numTables]['content'] .= $tables[$this->numTables]['format'] . '\lang1053\langfe1053\cgrid\langnp1053\langfenp1053' . NL;

                // The text
                $this->tables[$this->numTables]['content'] .= '{' . $this->getFormat($this->paragraph_fmt) . $this->tableCols[$i]['text'] . '\cell}' . NL;
            }
            // $this->tables[$this->numTables]['content'] .= $this->getFormat($this->paragraph_fmt) . NL;

            $this->tables[$this->numTables]['content'] .= '{';
            $this->tables[$this->numTables]['content'] .= $this->getTableRowDef( $this->lastRowIdx );
            $this->tables[$this->numTables]['content'] .= $this->getTableCellDef( $this->lastRowIdx );
            $this->tables[$this->numTables]['content'] .= '\row }';

            // Clean up row and its columns
            for( $i = $this->tableRows[$this->lastRowIdx]['firstcol'];
                $i < $this->tableRows[$this->lastRowIdx]['firstcol'] + $this->tableRows[$this->lastRowIdx]['cols']; $i++ )
            unset ( $this->tableCols[$i] );
            $this->lastColIdx -= $this->tableRows[$this->lastRowIdx]['cols'];

            unset ( $this->tableRows[$this->lastRowIdx] );
            $this->lastRowIdx--;
        } else {
            // If it is a nested row/table then we add the whole tabledefinition to
            // the parent cell when we reach end_table
        }
    }

    // {{{ getTableRowDef()

    /**
     * This method returns the row definition for a specific row
     *
     * @param int $row The row number
     * @return string The row definition
     * @access public
     */
    function getTableRowDef( $row )
    {
        // Start of row
        $tdef = '\trowd \trgaph' . $this->tableRows[$row]['gap'];

        // Position of left edge relative to column start
        $tdef .= '\trleft' . $this->tableRows[$row]['pos'];

        // Whether to keep the row together
        $tdef .= $this->tableRows[$row]['keeptogether'];

        // Whether to keep this row in the same page as the following row
        $tdef .= $this->tableRows[$row]['keepwithnext'] . NL;

        // Table borders
        if ( $this->tableRows[$row]['b_top'] > 0 )
            $tdef .= '\trbrdrt\brdr' . $this->tableRows[$row]['b_top_style'] .
                     '\brdrw' . $this->tableRows[$row]['b_top'] .
                     '\brdrcf' . $this->tableRows[$row]['b_top_color'] .
                     ' ';
        else
            $tdef .= '\trbrdrt\brdrnone ';

        if ( $this->tableRows[$row]['b_bottom'] > 0 )
            $tdef .= '\trbrdrb\brdr' . $this->tableRows[$row]['b_bottom_style'] .
                     '\brdrw' . $this->tableRows[$row]['b_bottom'] .
                     '\brdrcf' . $this->tableRows[$row]['b_bottom_color'] .
                     ' ';
        else
            $tdef .= '\trbrdrb\brdrnone ';

        if ( $this->tableRows[$row]['b_left'] > 0 )
            $tdef .= '\trbrdrl\brdr' . $this->tableRows[$row]['b_left_style'] .
                     '\brdrw' . $this->tableRows[$row]['b_left'] .
                     '\brdrcf' . $this->tableRows[$row]['b_left_color'] .
                     ' ';
        else
            $tdef .= '\trbrdrl\brdrnone ';

        if ( $this->tableRows[$row]['b_right'] > 0 )
            $tdef .= '\trbrdrr\brdr' . $this->tableRows[$row]['b_right_style'] .
                     '\brdrw' . $this->tableRows[$row]['b_right'] .
                     '\brdrcf' . $this->tableRows[$row]['b_right_color'] .
                     ' ';
        else
            $tdef .= '\trbrdrr\brdrnone ';

        if ( $this->tableRows[$row]['b_inside'] > 0 )
            $tdef .= '\trbrdrh\brdr' . $this->tableRows[$row]['b_inside_style'] .
                     '\brdrw' . $this->tableRows[$row]['b_inside'] .
                     '\brdrcf' . $this->tableRows[$row]['b_inside_color'] .
                     ' ';
        else
            $tdef .= '\trbrdrh\brdrnone ';

		// Row height
		if ($this->tableRows[$row]['height'] != 0)
			$tdef .= '\trrh' . $this->tableRows[$row]['height'];

        // Ignore row widths and autofit row
        // $tdef .= '\trftsWidth1\trautofit1';
        // Paddings (margins)
        if (isset($this->tableRows[$row]['cellmargint']) ) {
        	$tdef .= '\trpaddt' . $this->tableRows[$row]['cellmargint'];
	        $tdef .= '\trpaddb' . $this->tableRows[$row]['cellmarginb'];
    	    $tdef .= '\trpaddl' . $this->tableRows[$row]['cellmarginl'];
        	$tdef .= '\trpaddr' . $this->tableRows[$row]['cellmarginr'];
        } else {
			$tdef .= '\trpaddt' . $this->tables[$this->numTables]['cellmargint'];
	        $tdef .= '\trpaddb' . $this->tables[$this->numTables]['cellmarginb'];
    	    $tdef .= '\trpaddl' . $this->tables[$this->numTables]['cellmarginl'];
        	$tdef .= '\trpaddr' . $this->tables[$this->numTables]['cellmarginr'];
        }
        
        // Use twips for all padding measures
        $tdef .= '\trpaddft3\trpaddfb3\trpaddfl3\trpaddfr3 ';

        return $tdef;
    }

    // }}}



    // {{{ getTableCellDef()

    /**
     * This method returns the cell definitions for a specific row
     *
     * @param int $row The row number
     * @return string The row definition
     * @access public
     */
    function getTableCellDef( $row )
    {
        $cdef = '';
        for( $i = $this->tableRows[$row]['firstcol'];
            $i < $this->tableRows[$row]['firstcol'] + $this->tableRows[$row]['cols']; $i++ ) {

            // Vertical alignment
            $cdef .= '\clvertal' . $this->tableCols[$i]['valign'] . NL;

			if (isset($this->tableCols[$i]['b_top'])) 
	            if ( $this->tableCols[$i]['b_top'] > 0 )
	                $cdef .= '\clbrdrt\brdr' . $this->tableCols[$i]['b_top_style'] .
	                         '\brdrw' . $this->tableCols[$i]['b_top'] .
	                         '\brdrcf' . $this->tableCols[$i]['b_top_color'] .
	                         ' ';
	            else
	                $cdef .= '\clbrdrt\brdrnone ';
	        else
	            if ( $this->tableRows[$row]['b_top'] > 0 )	        
		           	$cdef .= '\clbrdrt\brdr' . $this->tableRows[$row]['b_top_style'] .
		                     '\brdrw' . $this->tableRows[$row]['b_top'] .
		                     '\brdrcf' . $this->tableRows[$row]['b_top_color'] .
		                     ' ';
	            else
	                $cdef .= '\clbrdrt\brdrnone ';

			if (isset($this->tableCols[$i]['b_bottom'])) 
	            if ( $this->tableCols[$i]['b_bottom'] > 0 )
	                $cdef .= '\clbrdrb\brdr' . $this->tableCols[$i]['b_bottom_style'] .
	                         '\brdrw' . $this->tableCols[$i]['b_bottom'] .
	                         '\brdrcf' . $this->tableCols[$i]['b_bottom_color'] .
	                         ' ';
	            else
	                $cdef .= '\clbrdrb\brdrnone ';
	        else
	            if ( $this->tableRows[$row]['b_bottom'] > 0 )	        
		           	$cdef .= '\clbrdrb\brdr' . $this->tableRows[$row]['b_bottom_style'] .
		                     '\brdrw' . $this->tableRows[$row]['b_bottom'] .
		                     '\brdrcf' . $this->tableRows[$row]['b_bottom_color'] .
		                     ' ';
	            else
	                $cdef .= '\clbrdrb\brdrnone ';
		                     

			if (isset($this->tableCols[$i]['b_left'])) 
	            if ( $this->tableCols[$i]['b_left'] > 0 )
	                $cdef .= '\clbrdrl\brdr' . $this->tableCols[$i]['b_left_style'] .
	                         '\brdrw' . $this->tableCols[$i]['b_left'] .
	                         '\brdrcf' . $this->tableCols[$i]['b_left_color'] .
	                         ' ';
	            else
	                $cdef .= '\clbrdrl\brdrnone ';
	        else
	            if ( $this->tableRows[$row]['b_left'] > 0 )	        
		           	$cdef .= '\clbrdrl\brdr' . $this->tableRows[$row]['b_left_style'] .
		                     '\brdrw' . $this->tableRows[$row]['b_left'] .
		                     '\brdrcf' . $this->tableRows[$row]['b_left_color'] .
		                     ' ';
	            else
	                $cdef .= '\clbrdrl\brdrnone ';


			if (isset($this->tableCols[$i]['b_right'])) 
	            if ( $this->tableCols[$i]['b_right'] > 0 )
	                $cdef .= '\clbrdrr\brdr' . $this->tableCols[$i]['b_right_style'] .
	                         '\brdrw' . $this->tableCols[$i]['b_right'] .
	                         '\brdrcf' . $this->tableCols[$i]['b_right_color'] .
	                         ' ';
	            else
	                $cdef .= '\clbrdrr\brdrnone ';
	        else
	            if ( $this->tableRows[$row]['b_right'] > 0 )	        
		           	$cdef .= '\clbrdrr\brdr' . $this->tableRows[$row]['b_right_style'] .
		                     '\brdrw' . $this->tableRows[$row]['b_right'] .
		                     '\brdrcf' . $this->tableRows[$row]['b_right_color'] .
		                     ' ';
	            else
	                $cdef .= '\clbrdrr\brdrnone ';

            // Cell color
            if ( $this->tableCols[$i]['color'] > 0 )
                $cdef .= '\clcbpat' . $this->tableCols[$i]['color'];

            // Cell text flow
            $cdef .= '\cltxlrtb';

            // Cell width (old and new properties)
            $cdef .= '\clftsWidth3\clwWidth' . $this->tableCols[$i]['width'] . NL;
            $cdef .= '\cellx' . $this->tableCols[$i]['rightbound'] . NL;
        }
        return $cdef;
    }

    // {{{ tableStartCol()

    /**
     * This method starts a new column within a row
     *
     * @param int $colWidth Column width in format accepted by getTwips()
     * @param string $align Vertical alignment t[op], b[ottom], c[enter]
     * @param RFTParagraphFormat The paragraph format to be used initally
     * @return void
     * @access public
     */
    function tableColStart( $colWidth, $align , $fmt = null )
    {
        $this->lastColIdx++;

		$colWidth = $this->getTwips($colWidth);

        // Column width
        $this->tableCols[$this->lastColIdx]['width'] = $colWidth;

        // Vertical cell alignment
        switch ($align) {
            case 't':
            case 'top':
                $this->tableCols[$this->lastColIdx]['valign'] = 't';
                break;
            case 'b':
            case 'bottom':
                $this->tableCols[$this->lastColIdx]['valign'] = 'b';
                break;
            case 'c':
            case 'center':
                $this->tableCols[$this->lastColIdx]['valign'] = 'c';
                break;
            default:
                $this->error( "Unknown vertical alignment (use t[op], b[ottom] or c[enter])", ERROR );
        }

        // Set default cell borders
//        $this->tableCols[$this->lastColIdx]['b_top'] = $this->defaultTableColBorder['b_top'];
//        $this->tableCols[$this->lastColIdx]['b_top_style'] = $this->defaultTableColBorder['b_top_style'];
//        $this->tableCols[$this->lastColIdx]['b_top_color'] = $this->defaultTableColBorder['b_top_color'];
//        $this->tableCols[$this->lastColIdx]['b_bottom'] = $this->defaultTableColBorder['b_bottom'];
//        $this->tableCols[$this->lastColIdx]['b_bottom_style'] = $this->defaultTableColBorder['b_bottom_style'];
//        $this->tableCols[$this->lastColIdx]['b_bottom_color'] = $this->defaultTableColBorder['b_bottom_color'];
//        $this->tableCols[$this->lastColIdx]['b_left'] = $this->defaultTableColBorder['b_left'];
//        $this->tableCols[$this->lastColIdx]['b_left_style'] = $this->defaultTableColBorder['b_left_style'];
//        $this->tableCols[$this->lastColIdx]['b_left_color'] = $this->defaultTableColBorder['b_left_color'];
//        $this->tableCols[$this->lastColIdx]['b_right'] = $this->defaultTableColBorder['b_right'];
//        $this->tableCols[$this->lastColIdx]['b_right_style'] = $this->defaultTableColBorder['b_right_style'];
//        $this->tableCols[$this->lastColIdx]['b_right_color'] = $this->defaultTableColBorder['b_right_color'];
//        $this->tableRows[$this->lastColIdx]['b_inside'] = $this->defaultTableColBorder['b_inside'];
//        $this->tableRows[$this->lastColIdx]['b_inside_style'] = $this->defaultTableColBorder['b_inside_style'];
//        $this->tableRows[$this->lastColIdx]['b_inside_color'] = $this->defaultTableColBorder['b_inside_color'];

        // Clear cell contents
        $this->tableCols[$this->lastColIdx]['text'] = '';

        // Calculate current columns rightbound position
        if ( $this->tableRows[$this->lastRowIdx]['firstcol'] == $this->lastColIdx )
            $this->tableCols[$this->lastColIdx]['rightbound'] = $colWidth + $this->tableRows[$this->lastRowIdx]['pos'];
        else
            $this->tableCols[$this->lastColIdx]['rightbound'] = $this->tableCols[$this->lastColIdx-1]['rightbound'] + $this->tableRows[$this->lastRowIdx]['cellmarginr'] + $colWidth - $this->tableRows[$this->lastRowIdx]['cellmarginr'];

        if ( $this->CurrentCellColor > 0 )
            $this->tableCols[$this->lastColIdx]['color'] = $this->CurrentCellColor;

        // $this->tableCols[$this->lastColIdx][''] = ;

        // Start paragraph
        if ($fmt != null)
            $this->newParagraph($fmt);
    }


    // {{{ tableEndCol()

    /**
     * This method ends the table column
     *
     * @return void
     * @access public
     */
    function tableColEnd()
    {
        $this->endBlock( false );
    }

    // }}}


    // {{{ setTableBorders()

    /**
     * This method sets the current row or cell borders.
     * If a column has been started the cell borders are set,
     * otherwise the row borders.
     *
     * @param string $location Which border to set. One or more of t, b, l, r, i (top, bottom, left, right, inside)
     * @param string/int $width Width accepted by getTwips()
     * @param string $style Border style according to the RTF spec format (i.e. s/th/sh/db/dot/dash....)
     * @param int $color Color id as return by newColor(), 0 is auto-coloring
     * @return void
     * @access public
     */
    function setTableBorders( $location, $width, $style = 's', $color = 0)
    {
        if ( $this->tableRows[$this->lastRowIdx]['firstcol'] > $this->lastColIdx ) {
            // Column not started. Set the row borders
            $tablepiece =& $this->tableRows[$this->lastRowIdx];
        } else {
            // Column started. Set the column borders
            $tablepiece =& $this->tableCols[$this->lastColIdx];
        }

        if ( strpos( $location, 't' ) !== false ) {
            $tablepiece['b_top'] = $this->getTwips($width);
            $tablepiece['b_top_style'] = $style;
            $tablepiece['b_top_color'] = $color;
        }
        if ( strpos( $location, 'b' ) !== false ) {
            $tablepiece['b_bottom'] = $this->getTwips($width);
            $tablepiece['b_bottom_style'] = $style;
            $tablepiece['b_bottom_color'] = $color;
        }
        if ( strpos( $location, 'l' ) !== false ) {
            $tablepiece['b_left'] = $this->getTwips($width);
            $tablepiece['b_left_style'] = $style;
            $tablepiece['b_left_color'] = $color;
        }
        if ( strpos( $location, 'r' ) !== false ) {
            $tablepiece['b_right'] = $this->getTwips($width);
            $tablepiece['b_right_style'] = $style;
            $tablepiece['b_right_color'] = $color;
        }
        if ( strpos( $location, 'i' ) !== false ) {
            // Only exist in row context
            $this->tableRows[$this->lastColIdx]['b_inside'] = $this->getTwips($width);
            $this->tableRows[$this->lastColIdx]['b_inside_style'] = $style;
            $this->tableRows[$this->lastColIdx]['b_inside_color'] = $color;
        }
    }

    // }}}

    // {{{ setDefaultTableBorders()

    /**
     * This method sets the default cell borders.
     * If a column has been started the cell borders are set,
     * otherwise the row borders.
     *
     * @param string $rowcol Row or column: r[ow] or c[olumn]
     * @param string $location Which border to set. One or more of t, b, l, r, i (top, bottom, left, right, inside)
     * @param string/int $width Width accepted by getTwips()
     * @param string $style Border style according to the RTF spec format (i.e. s/th/sh/db/dot/dash....)
     * @param int $color Color id as return by newColor(), 0 is auto-coloring
     * @return void
     * @access public
     */
    function setDefaultTableBorders( $rowCol, $location, $width, $style = 's', $color = 0)
    {
        switch ( $rowCol ) {
            case 'r':
            case 'row':
                // Set the row borders
                $tablepiece =& $this->defaultTableRowBorders;
                break;
            case 'c':
            case 'col':
                // Set the column borders
                $tablepiece =& $this->defaultTableColBorders;
                break;
            default:
                $this->error( "Must specify row or column" , FATAL );
        }

        if ( strpos( $location, 't' ) !== false ) {
            $tablepiece['b_top'] = $this->getTwips($width);
            $tablepiece['b_top_style'] = $style;
            $tablepiece['b_top_color'] = $color;
        }
        if ( strpos( $location, 'b' ) !== false ) {
            $tablepiece['b_bottom'] = $this->getTwips($width);
            $tablepiece['b_bottom_style'] = $style;
            $tablepiece['b_bottom_color'] = $color;
        }
        if ( strpos( $location, 'l' ) !== false ) {
            $tablepiece['b_left'] = $this->getTwips($width);
            $tablepiece['b_left_style'] = $style;
            $tablepiece['b_left_color'] = $color;
        }
        if ( strpos( $location, 'r' ) !== false ) {
            $tablepiece['b_right'] = $this->getTwips($width);
            $tablepiece['b_right_style'] = $style;
            $tablepiece['b_right_color'] = $color;
        }
        if ( strpos( $location, 'i' ) !== false ) {
            // Only exist in row context
            $this->tableRows[$this->lastColIdx]['b_inside'] = $this->getTwips($width);
            $this->tableRows[$this->lastColIdx]['b_inside_style'] = $style;
            $this->tableRows[$this->lastColIdx]['b_inside_color'] = $color;
        }

    }

    // }}}


    // {{{ setTableFormat()

    /**
     * This should not be used. Use paragraph format instead!!!!!
     *
     * @param int $clr Color id as return by newColor(), 0 is auto-coloring
     * @return void
     * @access public
     */
    function setTableFormat( $fmt )
    {
        $fmt .= ' ';
        if ( $this->tables[$this->numTables]['parent_col'] < $this->lastColIdx ) {
            // Column started, set cell format
            $this->tableCols[$this->lastColIdx]['format'] = $fmt;
        } elseif ( $this->tables[$this->numTables]['parent_row'] < $this->lastRowIdx ) {
            // Row only started, set row format
            $this->tableRows[$this->lastRowIdx]['format'] = $fmt;
        } else {
            // Neither, set table format
            $this->tables[$this->numTables]['format'] = $fmt;
        }
    }

    // }}}


    // {{{ setTableCellColor()

    /**
     * This method sets cell color.
     * If a columns has not been started, sets default cell coloring.
     * If a column has been started, the current cell's color.
     *
     * @param int $clr Color id as return by newColor(), 0 is auto-coloring
     * @return void
     * @access public
     */
    function setTableCellColor( $clr )
    {
        if ( $this->tableRows[$this->lastRowIdx]['firstcol'] > $this->lastColIdx ) {
            // Column not started, set current cell color
            $this->currentCellColor = $clr;
        } else {
            // Column started, set color
            $this->tableCols[$this->lastColIdx]['color'] = $clr;
        }
    }

    // }}}




    function table_break()
    {
        $this->tables[$this->numTables]['content'] .= '\pard \ql \li0\ri0\widctlpar\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 { \par }';
        $this->tables[$this->numTables]['content'] .= '\pard \ql \li0\ri0\widctlpar\intbl\aspalpha\aspnum\faauto\adjustright\rin0\lin0 ';
    }




    // {{{ addToDoc()

    /**
     * This method adds RTF content to the buffer
     *
     * @return void
     * @access public
     */
    function addToDoc( $text )
    {
        if ( $this->numTables == 0 ) {
            // Not in table
            $this->buffer .= $text;
        } elseif ( $this->tableRows[$this->lastRowIdx]['firstcol'] > $this->lastColIdx ) {
            // Ignore text to put in row
        } else {
            // Remember text to put in column
            $this->tableCols[$this->lastColIdx]['text'] .= $text;
        }
    }

    // }}}


    // {{{ sendRTF()

    /**
     * This method sends the RTF document as an attachment
     *
     * @param string $filename The name of the attachment file
     * @return void
     * @access public
     */
    function sendRTF( $filename = 'document.rtf' )
    {
        // Header
        $rtf = '{';
        $rtf .= '\rtf' . $this->header['version']; // Version
        $rtf .= $this->header['charset']; // Character set
        $rtf .= '\deff' . $this->header['default_font']; // Default font
        $rtf .= $this->custom_header; // Additional headers
        $rtf .= NL;
        // Font table
        $rtf .= '{\fonttbl' . NL;
        foreach( $this->header['fonts'] as $font_number => $font )
        $rtf .= '{\f' . $font_number . $font . ';}' . NL;
        $rtf .= '}' . NL;
        // Color table
        $rtf .= '{\colortbl;' . NL;
        foreach( $this->header['colors'] as $color )
        $rtf .= $color . ';' . NL;
        $rtf .= '}' . NL;
        // List table
        $rtf .= '{\*\listtable' . NL;

        foreach( $this->formats as $fmt_id => $fmt ) {
            if ( isset( $fmt->plist['type'] ) ) {
                $rtf .= '{\list\listtemplateid' . $fmt_id . NL;
                for( $i = 1; $i <= 9; $i++ ) {
                    $rtf .= '{\listlevel';
                    if ( $fmt->plist['type'] == '#' ) {
                        // Number style
                        $rtf .= '\levelnfc' . $fmt->plist[$i]['style'];
                        $rtf .= '\levelnfcn' . $fmt->plist[$i]['style']; // New Word2000 interpretation
                    } else {
                        // Bullet style
                        $rtf .= '\levelnfc23';
                        $rtf .= '\levelnfcn23'; // New Word2000 interpretation
                    }
                    $rtf .= '\leveljc' . ( $fmt->plist[$i]['align'] );
                    $rtf .= '\levelfollow' . $fmt->plist[$i]['char'];
                    $rtf .= '\levelstartat1';
                    if ( $fmt->plist['type'] == '#' ) {
                        // Number style
                        $rtf .= '{\leveltext ' . ESC . sprintf( '%02x', 2 ) . ESC . sprintf( '%02x', $i-1 ) . '.;}';
                        $rtf .= '{\levelnumbers' . ESC . sprintf( '%02x', 1 ) . ';}';
                    } else {
                        // Bullet style
                        $rtf .= '{\leveltext ' . ESC . sprintf( '%02x', 1 ) . $fmt->plist[$i]['style'] . ' ?;}';
                        $rtf .= '{\levelnumbers;}';
                    }
                    $rtf .= '\fi' . $fmt->plist[$i]['fi'];
                    $rtf .= '\li' . $fmt->plist[$i]['li'];
                    // Font
                    if ( $fmt->plist[$i]['font'] != $this->header['default_font'] ) {
                        $rtf .= '\f' . $fmt->plist[$i]['font'];
                    }
                    if ( isset( $fmt->plist[$i]['fontsize'] ) ) {
                        $rtf .= '\fs' . 2 * $fmt->plist[$i]['fontsize'];
                    }
                    if ( isset( $fmt->plist[$i]['fontstyle'] ) ) {
                        $rtf .= $fmt->plist[$i]['fontstyle'];
                    }
                    // Color
                    if ( isset( $fmt->plist[$i]['fg'] ) ) {
                        $rtf .= '\cf' . $fmt->plist[$i]['fg'];
                    }
                    if ( isset( $fmt->plist[$i]['bg'] ) ) {
                        $rtf .= '\chcbpat' . $fmt->plist[$i]['bg']; // Works for OpenOffice - Word uses \highlight and the trick is to put it before \par
                    }
                    $rtf .= '}' . NL;
                }
                $rtf .= '\listid' . $fmt_id . '}' . NL;
            }
        }
        $rtf .= '}' . NL;

        $rtf .= '{\listoverridetable' . NL;
        foreach( $this->formats as $fmt_id => $fmt ) {
            if ( isset( $fmt->plist['type'] ) ) {
                $rtf .= '{\listoverride\listid' . $fmt_id . '\listoverridecount0\ls' . $fmt_id . '}' . NL;
            }
        }
        $rtf .= '}' . NL;

        // Document area

        // Info group
        $rtf .= '{\info';
        $rtf .= '{\title ' . $this->convertText( $this->doc_info['title'] ) . '}' . NL;
        $rtf .= '{\subject ' . $this->convertText( $this->doc_info['subject'] ) . '}' . NL;
        $rtf .= '{\author ' . $this->convertText( $this->doc_info['author'] ) . '}' . NL;
        $rtf .= '{\manager ' . $this->convertText( $this->doc_info['manager'] ) . '}' . NL;
        $rtf .= '{\company ' . $this->convertText( $this->doc_info['company'] ) . '}' . NL;
        $rtf .= '{\category ' . $this->convertText( $this->doc_info['category'] ) . '}' . NL;
        $rtf .= '{\keywords ' . $this->convertText( $this->doc_info['keywords'] ) . '}' . NL;
        $rtf .= '{\version' . $this->convertText( $this->doc_info['version'] ) . '}' . NL;
        $rtf .= '{\doccomm ' . $this->convertText( $this->doc_info['doccomm'] ) . '}' . NL;
        $rtf .= '{\creatim ' . $this->doc_info['creatime'] . '}' . NL;
        $rtf .= $this->custom_doc_info . NL;
        $rtf .= '}' . NL;

        // Document formatting
        if ( $this->doc_fmt['landscape'] ) {
            $rtf .= '\landscape';
        }
        $rtf .= '\paperw' . $this->getTwips( $this->doc_fmt['width'] );
        $rtf .= '\paperh' . $this->getTwips( $this->doc_fmt['height'] );
        $rtf .= '\margl' . $this->getTwips( $this->doc_fmt['margin_left'] );
        $rtf .= '\margr' . $this->getTwips( $this->doc_fmt['margin_right'] );
        $rtf .= '\margt' . $this->getTwips( $this->doc_fmt['margin_top'] );
        $rtf .= '\margb' . $this->getTwips( $this->doc_fmt['margin_bottom'] );
        $rtf .= '\deftab' . $this->getTwips( $this->doc_fmt['deftab'] );
        $rtf .= '\deflang' . $this->doc_fmt['deflang'];
        if ( $this->doc_fmt['widowctrl'] ) {
            $rtf .= '\widowctrl';
        }
        $rtf .= NL;

        // Return buffer
        $this->endBlock(); // First emit the final paragraph
        $rtf .= $this->buffer;

        // Finalize RTF document
        $rtf .= '}';

        header( "Content-Disposition: attachment; filename=$filename" );
        header( "Content-type: application/rtf" );
        echo $rtf;
    }

    // }}}

}

/*
Numbering styles:
0        Arabic (1, 2, 3)
1        Uppercase Roman numeral (I, II, III)
2        Lowercase Roman numeral (i, ii, iii)
3        Uppercase letter (A, B, C)
4        Lowercase letter (a, b, c)
5        Ordinal number (1st, 2nd, 3rd)
6        Cardinal text number (One, Two Three)
7        Ordinal text number (First, Second, Third)
10        Kanji numbering without the digit character (*dbnum1)
11        Kanji numbering with the digit character (*dbnum2)
12        46 phonetic katakana characters in "aiueo" order (*aiueo)
13        46 phonetic katakana characters in "iroha" order (*iroha)
14        Double-byte character
15        Single-byte character
16        Kanji numbering 3 (*dbnum3)
17        Kanji numbering 4 (*dbnum4)
18        Circle numbering (*circlenum)
19        Double-byte Arabic numbering
20        46 phonetic double-byte katakana characters (*aiueo*dbchar)
21        46 phonetic double-byte katakana characters (*iroha*dbchar)
22        Arabic with leading zero (01, 02, 03, ..., 10, 11)
23        Bullet (no number at all)
24        Korean numbering 2 (*ganada)
25        Korean numbering 1 (*chosung)
26        Chinese numbering 1 (*gb1)
27        Chinese numbering 2 (*gb2)
28        Chinese numbering 3 (*gb3)
29        Chinese numbering 4 (*gb4)
30        Chinese Zodiac numbering 1 (* zodiac1)
31        Chinese Zodiac numbering 2 (* zodiac2)
32        Chinese Zodiac numbering 3 (* zodiac3)
33        Taiwanese double-byte numbering 1
34        Taiwanese double-byte numbering 2
35        Taiwanese double-byte numbering 3
36        Taiwanese double-byte numbering 4
37        Chinese double-byte numbering 1
38        Chinese double-byte numbering 2
39        Chinese double-byte numbering 3
40        Chinese double-byte numbering 4
41        Korean double-byte numbering 1
42        Korean double-byte numbering 2
43        Korean double-byte numbering 3
44        Korean double-byte numbering 4
45        Hebrew non-standard decimal
46        Arabic Alif Ba Tah
47        Hebrew Biblical standard
48        Arabic Abjad style
255        No number
*/


?>