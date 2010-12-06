<?php
/**
 * Class Box
 * 
 * Instance of a new Box with rounded header.
 * @access public
 * @author Emilien MOREL <admin@website-php.com>
 * @link http://www.website-php.com
 * @copyright website-php.com 29/11/2009
 * @version 1.0
 */
 
class Box extends WebSitePhpObject {
	/**#@+
	* Box style
	* @access public
	* @var string
	*/
	const STYLE_MAIN = "main";
	const STYLE_SECOND = "second";
	/**#@-*/
	
	/**#@+
	* Box alignment
	* @access public
	* @var string
	*/
	const ALIGN_LEFT = "left";
	const ALIGN_CENTER = "center";
	const ALIGN_RIGHT = "right";
	const ALIGN_JUSTIFY = "justify";
	/**#@-*/
	
	/**#@+
	* Box vertical alignment
	* @access public
	* @var string
	*/
	const VALIGN_TOP = "top";
	const VALIGN_CENTER = "center";
	const VALIGN_BOTTOM = "bottom";
	/**#@-*/
	
	/**#@+
	* @access private
	*/
	private $title = "";
	private $style_header = "main";
	private $style_content = "main";
	private $shadow = false;
	private $link = "";
	private $id = "";
	private $content = null;
	private $width = "100%";
	private $height = "";
	private $valign = "top";
	private $align = "center";
	private $is_browser_ie_6 = false;
	private $browser_ie_version = false;
	private $tagH = "";
	
	private $move = false;
	private $move_revert = false;
	
	private $force_box_with_picture = true;
	private $box_border_color = "";
	/**#@-*/
	
	/**
	 * Constructor Box
	 * @param object|string $title title in the header the box
	 * @param boolean $shadow if box has shadow
	 * @param string $style_header style of the header (Box::STYLE_MAIN or Box::STYLE_SECOND)
	 * @param string $style_content style of the content (Box::STYLE_MAIN or Box::STYLE_SECOND)
	 * @param string $link heander title link
	 * @param string $id unique id of the box
	 * @param string $width width of the box
	 * @param string $height height of the bo
	 * @param string $move if box can be move
	 */
	function __construct($title, $shadow=false, $style_header='main', $style_content='main', $link='', $id='main_box', $width='100%', $height="", $move=false) {
		parent::__construct();
		
		if (!isset($title)) {
			throw new NewException("1 argument for ".get_class($this)."::__construct() is mandatory", 0, 8, __FILE__, __LINE__);
		}
		
		$this->is_browser_ie_6 = is_browser_ie_6();
		$this->browser_ie_version = get_browser_ie_version();
		
		$this->title = $title;
		if ($this->is_browser_ie_6) {
			$this->shadow = false;
		} else {
			$this->shadow = $shadow;
		}
		$this->style_header = $style_header;
		$this->style_content = $style_content;
		$this->link = $link;
		$this->id = $id;
		$this->move = $move;
		$this->width = $width;
		$this->height = $height;
		$this->tagH = "";
		
		if (constant("DEFINE_STYLE_BCK_PICTURE_".strtoupper($this->style_header)) == "") {
			$this->force_box_with_picture = false;
		} else {
			$this->box_border_color = constant("DEFINE_STYLE_BORDER_TABLE_".strtoupper($this->style_header));
		}
		
		$this->addCss(BASE_URL."wsp/css/angle.css.php", "", true);
	}
	
	/**
	 * function setDraggable
	 * @param string $bool if box can be move
	 * @param string $revert if box revert first place when dropped
	 */
	public function setDraggable($bool=true, $revert=false) {
		$this->move = $bool;
		$this->move_revert = $revert;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setHeight($height) {
		$this->height = $height;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setWidth($width) {
		$this->width = $width;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setValign($valign) {
		$this->valign = $valign;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setAlign($align) {
		$this->align = $align;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setShadow($shadow) {
		$this->shadow = $shadow;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setTitleTagH1() {
		$this->tagH = "h1";
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setTitleTagH2() {
		$this->tagH = "h2";
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setTitleTagH3() {
		$this->tagH = "h3";
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setTitleTagH4() {
		$this->tagH = "h4";
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	/**
	 * function setObject
	 * @param object|string $content content of the box
	 */
	public function setContent($content) {
		$this->content = $content;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function forceBoxWithPicture($bool, $border_color="") {
		$this->force_box_with_picture = $bool;
		$this->box_border_color = $border_color;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	/**
	 * function render
	 * @return string html code of the box
	 */
	public function render($ajax_render=false) {
		$html = "";
		if ($this->force_box_with_picture) {
			$this->shadow = true;
		}
		
		if (!$ajax_render) {
			$html .= "<div id=\"wsp_box_".$this->id."\">\n";
		}
		if (!$this->shadow && !$this->is_browser_ie_6 && !$this->force_box_with_picture) {
			$html .= "<div style=\"position:relative;top:-5px;\">\n";
		}
		$html .= "<div id=\"drag_box_".$this->id."\" align=\"left\" class=\"";
		if ($this->move) {
			$html .= "draggable";
		}
		if (!$this->force_box_with_picture) {
			if ($this->move) {
				$html .= " ";
			}
			$html .= "AngleRond".ucfirst($this->style_header);
			if ($this->shadow) {
				$html .= "Ombre";
			}
		}
		
		if ($this->width != "" || $this->height != "") {
			$html .= "\" style=\"";
			if ($this->width != "") {
				if (is_integer($this->width)) {
					$html .= "width:".$this->width."px;";
				} else {
					$html .= "width:".$this->width.";";
				}
			}
			if ($this->height != "") {
				if (is_integer($this->height)) {
					$html .= "height:".$this->height."px;";
				} else {
					$html .= "height:".$this->height.";";
				}
			}
			$html .= "\">\n";
		}
		
		if (!$this->force_box_with_picture) {
			if (!$this->is_browser_ie_6) {
				$html .= "	<b class=\"pix1\"></b>\n";
				$html .= "	<b class=\"pix2\"></b>\n";
				$html .= "	<b class=\"pix3\"></b>\n";
				$html .= "	<b class=\"pix4\"></b>\n";
				$html .= "	<b class=\"pix5\"></b>\n";
			}
			
			if ($this->shadow) {
				$html .= "	<div class=\"ombre\">\n";
			}
			$html .= "		<div";
			if ($this->shadow) {
				$html .= " class=\"boiteTxt\"";
			}
			$html .= " >\n";
			$html .= "			<table class=\"table_".$this->style_header."_angle\" cellpadding=\"6\" cellspacing=\"0\"";
			if ($this->height != "") {
				$html .= " height=\"".$this->height."\"";
			}
			$html .= " style=\"table-layout:fixed;overflow:hidden;\">\n";
			$html .= "				<tr>\n";
			$html .= "					<td class=\"header_".$this->style_header."_bckg\">";
			if ($this->tagH != "") {
				$html .= "<".$this->tagH.">";
			}
			if ($this->link != "") {
				$html .= "<a href=\"".$this->link."\">";
			}
			$html .= "<b>";
			if (gettype($this->title) != "object") {
				$html .= $this->title;
			} else if (gettype($this->title) == "object") {
				$html .= $this->title->render();
			}
			$html .= "</b>";
			if ($this->link != "") {
				$html .= "</a>";
			}
			if ($this->tagH != "") {
				$html .= "</".$this->tagH.">";
			}
			$html .= "</td>\n";
			$html .= "				</tr>\n";
			$html .= "				<tr id=\"".$this->id."\">\n";
			$html .= "					<td class=\"table_".$this->style_content."_bckg\" width=\"9999\" valign=\"".$this->valign."\" style=\"height:100%;\">\n";
			$html .= "						<div ";
			if ($this->align == Box::ALIGN_JUSTIFY) {
				$html .= "style=\"text-align:justify;";
			} else {
				$html .= "align=\"".$this->align."\"";
			}
			$html .= "\">\n";
			if ($this->content != null) {
				if (gettype($this->content) == "object") {
					$html .= "						".$this->content->render($ajax_render)."\n";
				} else {
					$html .= "						".$this->content."\n";
				}
			}
			$html .= "						</div>\n";
			$html .= "					</td>\n";
			$html .= "				</tr>\n";
			$html .= "			</table>\n";
			$html .= "		</div>\n";
			
			if ($this->shadow) {
				$html .= "	</div>\n";
			}
		} else {
			$html .= "		<div id=\"left".ucfirst($this->style_header)."\">\n";
			$html .= "			<div id=\"right".ucfirst($this->style_header)."\" style=\"padding-bottom:".(($this->browser_ie_version <= 7) ? 5 : 3)."px;";
			if ($this->height != "") {
				$html .= "height:100%;";
			}
			$html .= "\">\n";
			$html .= "				<div id=\"top".ucfirst($this->style_header)."\">\n";
			$html .= "					<div style=\"height:30px;\"></div>\n";
			$html .= "				</div>\n";
			
			$html .= "				<div class=\"header_".$this->style_header."_bckg\" style=\"background:none;padding-bottom:3px;position:relative;top:-20px;height:0px;\">";
			if ($this->tagH != "") {
				$html .= "<".$this->tagH.">";
			}
			if ($this->link != "") {
				$html .= "<a href=\"".$this->link."\">";
			}
			$html .= "<b>";
			if (gettype($this->title) != "object") {
				$html .= $this->title;
			} else if (gettype($this->title) == "object") {
				$html .= $this->title->render();
			}
			$html .= "</b>";
			if ($this->link != "") {
				$html .= "</a>";
			}
			if ($this->tagH != "") {
				$html .= "</".$this->tagH.">";
			}
			$html .= "				</div>\n";
			
			$html .= "				<div style=\"margin-left:".(($this->browser_ie_version!=false && $this->browser_ie_version > 7) ? -9 : -7)."px;\">\n";
			$html .= "				<div id=\"wsp_box_content_".$this->id."\" class=\"table_".$this->style_content."_bckg\" style=\"padding-left:0px;border:1px solid ".$this->box_border_color.";";
			$html .= "display:table-cell;";
			if (is_integer($this->width)) {
				if ($this->style_content == Box::STYLE_MAIN) {
					$html .= "width:".($this->width - 12)."px;";
				} else {
					$html .= "width:".($this->width - 7)."px;";
				}
			} else {
				throw new NewException("width attribute in ".get_class($this)." must be an integer", 0, 8, __FILE__, __LINE__);
			}
			if ($this->valign == Box::VALIGN_CENTER) {
				$html .= "vertical-align:middle;";
			} else {
				$html .= "vertical-align:".$this->valign.";";
			}
			if ($this->height != "") {
				if (is_integer($this->height)) {
					$html .= "height:".($this->height-20)."px;";
				} else {
					$html .= "height:".$this->height.";";
				}
			}
			$html .= "\">\n";
			
			if ($this->height != "" && is_integer($this->height) && $this->valign == RoundBox::VALIGN_CENTER) {
				$html .= "<div style=\"display:table;height:100%;#position:relative;overflow:hidden;width:100%;\">\n";
				$html .= "	<div style=\"#position:absolute;#top:50%;display:table-cell;vertical-align:middle;width:100%;\">\n";
				$html .= "		<div style=\"#position:relative;#top:-50%;\">\n";
			}
			
			$html .= "					<div";
			if ($this->align == Box::ALIGN_JUSTIFY) {
				$html .= " style=\"width:98%;text-align:justify;";
			} else {
				$html .= " align=\"".$this->align."\" style=\"width:98%;";
			}
			$html .= "margin:5px;padding-right:5px;\">\n";
			if ($this->content != null) {
				if (gettype($this->content) == "object") {
					$html .= "					".$this->content->render($ajax_render)."\n";
				} else {
					$html .= "					".$this->content."\n";
				}
			}
			$html .= "				</div>\n";
		
			if ($this->height != "" && is_integer($this->height) && $this->valign == RoundBox::VALIGN_CENTER) {
				$html .= "		</div>\n";
				$html .= "	</div>\n";
				$html .= "</div>\n";
			}
			
			$html .= "				</div>\n";
			$html .= "				</div>\n";
			$html .= "			</div>\n";
			$html .= "		</div>\n";
		}
		
		$html .= "</div>\n";
		if (!$this->shadow && !$this->is_browser_ie_6 && !$this->force_box_with_picture) {
			$html .= "</div>\n";
		}
		if (!$ajax_render) {
			$html .= "</div>\n";
		}
		$html .= "<br/>\n";
		
		if ($this->move) {
			$html .= $this->getJavascriptTagOpen();
			$html .= "$(\"#wsp_box_".$this->id."\").draggable({opacity: 0.8, scroll: true";
			if ($this->move_revert) {
				$html .= ", revert: true";
			}
			$html .= "});\n";
			$html .= "$(\"#drag_box_".$this->id."\").resizable();\n";
			$html .= "$(\"#drag_box_".$this->id."\").find('.ui-resizable-e').remove();\n";
			$html .= "$(\"#drag_box_".$this->id."\").find('.ui-resizable-s').remove();\n";
			$html .= "$(\"#drag_box_".$this->id."\").find('.ui-resizable-se').remove();\n";
			$html .= $this->getJavascriptTagClose();
		}
		$this->object_change = false;
		
		return $html;
	}
	
	/**
	 * function getAjaxRender
	 * @return string javascript code to update initial html with ajax call
	 */
	public function getAjaxRender() {
		$html = "";
		if ($this->object_change && !$this->is_new_object_after_init) {
			// Extract JavaScript from HTML
			$array_ajax_render = extract_javascript($this->render(true));
			for ($i=1; $i < sizeof($array_ajax_render); $i++) {
				new JavaScript($array_ajax_render[$i], true);
			}
			
			$html .= "$('#wsp_box_".$this->id."').innerHTML = \"".str_replace('"', '\"', str_replace("\n", "", str_replace("\r", "", $array_ajax_render[0])))."\";\n";
		}
		return $html;
	}
}
?>