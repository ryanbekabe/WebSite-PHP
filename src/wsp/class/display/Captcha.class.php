<?php
class Captcha extends WebSitePhpObject {
	/**#@+
	* @access private
	*/
	protected $class_name = "";
	protected $page_object = null;
	protected $form_object = null;
	private $name = "";
	private $value = "";
	private $refresh = true;
	private $sound = true;
	private $default_value = "";
	private $width = 230;
	private $height = 80;
	/**#@-*/
	
	function __construct($page_or_form_object, $name='', $refresh=true, $sound=true) {
		parent::__construct();
		
		if (!isset($page_or_form_object) || gettype($page_or_form_object) != "object" || (!is_subclass_of($page_or_form_object, "Page") && get_class($page_or_form_object) != "Form")) {
			throw new NewException("Argument page_or_form_object for ".get_class($this)."::__construct() error", 0, 8, __FILE__, __LINE__);
		}
		
		if (is_subclass_of($page_or_form_object, "Page")) {
			$this->class_name = get_class($page_or_form_object);
			$this->page_object = $page_or_form_object;
			$this->form_object = null;
		} else {
			$this->page_object = $page_or_form_object->getPageObject();
			$this->class_name = get_class($this->page_object)."_".$page_or_form_object->getName();
			$this->form_object = $page_or_form_object;
		}
		
		if ($name == "") {
			$name = $this->page_object->createObjectName($this);
		} else {
			$exist_object = $this->page_object->existsObjectName($name);
			if ($exist_object != false) {
				throw new NewException("Tag name \"".$name."\" for object ".get_class($this)." already use for other object ".get_class($exist_object), 0, 8, __FILE__, __LINE__);
			}
		}
		
		$this->name = $name;
		$this->refresh = $refresh;
		$this->sound = $sound;
		
		$this->page_object->addEventObject($this, $this->form_object);
	}
	
	public function setValue($value) {
		$this->value = $value;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}

	public function setDefaultValue($value) {
		$this->default_value = $value;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setWidth($width) {
		if (!is_integer($width)) {
			throw new NewException("width attribut must be an integer in the method setWidth (Captcha object)", 0, 8, __FILE__, __LINE__);
		}
		$this->width = $width;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function setHeight($height) {
		$this->height = $height;
		if ($GLOBALS['__PAGE_IS_INIT__']) { $this->object_change =true; }
		return $this;
	}
	
	public function forceObjectChange() {
		$this->object_change =true;
		return $this;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getEventObjectName() {
		return $this->class_name."_".$this->name;
	}

	public function getValue() {
		return $this->value;
	}

	public function getDefaultValue() {
		return $this->default_value;
	}

	public function getFormObject() {
		return $this->form_object;
	}
	
	public function check() {
		$securimage = new Securimage();
		return $securimage->check($this->value);
	}
	
	public function render($ajax_render=false) {
		$html = "";
		if ($this->class_name != "") {
			if (!$ajax_render) {
				$html .= "<div id=\"wsp_captcha_".$this->name."\">\n";
			}
			$html .= "<div style=\"width:";
			if ($this->sound || $this->refresh) {
				$html .= ($this->width + 30);
			} else {
				$html .= $this->width;
			}
			$html .= "px;\">\n";
			$html .= "<img id=\"captcha_img_".$this->name."\" src=\"wsp/includes/securimage/securimage_show.php?width=".$this->width."&height=".$this->height."\" alt=\"CAPTCHA Image\" align=\"left\" width=\"".$this->width."\" height=\"".$this->height."\" />\n";
			$html .= "<br />\n";
			if ($this->sound) {
				$html .= "<object type=\"application/x-shockwave-flash\" data=\"wsp/includes/securimage/securimage_play.swf?audio=wsp/includes/securimage/securimage_play.php&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000\" height=\"19\" width=\"19\">\n";
	    		$html .= "	<param name=\"movie\" value=\"wsp/includes/securimage/securimage_play.swf?audio=wsp/includes/securimage/securimage_play.php&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000\" />\n";
	  			$html .= "</object>\n";
			}
  			$html .= "<br /><br />\n";
  			if ($this->refresh) {
				$html .= "<a tabindex=\"-1\" style=\"border-style: none\" href=\"#\" title=\"Refresh Captcha Image\" onclick=\"$('#captcha_img_".$this->name."').attr('src', 'wsp/includes/securimage/securimage_show.php?width=".$this->width."&height=".$this->height."&sid=' + Math.random()); return false\"><img src=\"wsp/includes/securimage/images/refresh.gif\" alt=\"Reload Captcha Image\" border=\"0\" onclick=\"this.blur()\" align=\"bottom\" /></a>\n";
  			}
  			$html .= "<br />\n";
			$html .= "<strong>".__(CAPTCHA_CODE)."</strong> <input type=\"text\" name=\"".$this->getEventObjectName()."\" size=\"8\" value=\"".$this->value."\" style=\"width: ".($this->width - 100)."px\" />\n";
			$html .= "</div>\n";
			if (!$ajax_render) {
				$html .= "</div>\n";
			}
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
		$html .= "$('#wsp_captcha_".$this->name."').html(\"".str_replace('"', '\"', str_replace("\n", "", str_replace("\r", "", $this->render(true))))."\");\n";
		$html .= "$('#wsp_captcha_".$this->name."').css('width', \"".$this->width."px\");\n";
		$html .= "$('#captcha_img_".$this->name."').attr('src', 'wsp/includes/securimage/securimage_show.php?width=".$this->width."&height=".$this->height."&sid=' + Math.random());\n";
		return $html;
	}
}
?>