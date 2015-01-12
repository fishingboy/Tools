<?
$frmOutputJS = 1;
$frmCheckJs = '';
$focusInput = '';

class CText
{
	var $title, $name;
	var $value, $size, $maxleng, $check, $events, $width;  // optional

	function CText($title, $name)
	{
		$this->title = $title;
		$this->name = $name;

		$this->value = $this->check = '';
        $this->width = '';
		$this->size = '30';
		$this->maxlength = '255';
	}

	function SetValue($value)
	{
		$this->value = htmlspecialchars($value, ENT_QUOTES);
	}

	function SetEvent($evt, $handle)
	{
		$this->events .= " {$evt}=\"{$handle}\" ";
	}

	function GetHtml()
	{
		global $frmCheckJs, $focusInput, $msgPtNoData2;
		if ($focusInput == '') $focusInput = $this->name;

		$mark = $this->check;

		switch ($this->check)
		{
			case "*":
				$frmCheckJs .= "if (CheckTextEmpty(document.getElementById(\"fm\").{$this->name}, \"[$this->title] $msgPtNoData2\") == false) return false; \n";
				break;
			case "email":
				$frmCheckJs .= "if (CheckEmail(document.getElementById(\"fm\").{$this->name}, \"$this->title\") == false) return false; \n";
				$mark = "*";
				break;
		}

		return "\t<input type=text id={$this->name} name={$this->name} size=$this->size maxlength=$this->maxlength value=\"{$this->value}\" $this->events style='width:{$this->width}' class=Text> $mark \n";
	}
}

class CPassword
{
	var $title, $name;
	var $value, $size, $check_empty;  // optional

	function CPassword($title, $name)
	{
		$this->title = $title;
		$this->name = $name;

		$this->value = '';
		$this->check_empty = '*';
		$this->size = '30';
	}

	function GetHtml()
	{
		global $frmCheckJs, $focusInput, $msgUserPassword2;
		if ($focusInput == '') $focusInput = $this->name;

		$frmCheckJs .= "if (CheckPassword(document.getElementById(\"fm\").{$this->name}, \"$this->title\", \"{$this->check_empty}\") == false) return false; \n";
		return "      <input type=password id={$this->name} name={$this->name} size=$this->size value=\"{$this->value}\" class=Text> $this->check_empty &nbsp; &nbsp; \n $msgUserPassword2 <input type=password id={$this->name}2 name={$this->name}2 size=$this->size value=\"{$this->value}\" class=Text> $this->check_empty \n";
	}
}

class CDate
{
	var $title, $name;
	var $value, $size, $check;  // optional

	function CDate($title, $name)
	{
		$this->title = $title;
		$this->name = $name;

		$this->value = $this->check = '';
		$this->size = '8';
	}

	function GetHtml()
	{
		global $PHP_ROOT, $frmCheckJs, $focusInput;

		if ($focusInput == '') $focusInput = $this->name;


		$allow_empty = ($this->check == '*') ? 0 : 1;
		$frmCheckJs .= "if (CheckDate(document.getElementById(\"fm\").{$this->name}, \"$this->title\", $allow_empty) == false) return false; \n";

		return "\t<input type=text id={$this->name} name={$this->name} size=$this->size value=\"{$this->value}\" class=Text > $this->check <a href='javascript: show_calendar(\"fm.{$this->name}\"); '><img border=0 src='/$PHP_ROOT/res/common/ccld_select.gif' ></a> (格式: yyyy-mm-dd) \n";
	}
}

$editorStyle	= array(
							"origin"	=> "letter-spacing:1px;font:12px Arial,Helvetica,sans-serif;margin:2px",
							"addition"	=> ""
						);

class CEditor
{
	var $title, $name;
	var $value, $width, $height;    // optional

	function CEditor($title, $name, $width='100%', $height='360px')
	{
		$this->title	= $title;
		$this->name		= $name;
		$this->width	= $width;
		$this->height	= $height;
		$this->value	= "";
	}

	function SetValue($value)
	{
		$this->value = $value;
	}

	function GetHtml()
	{
	    global $LOCALE, $ceditorOutputJS;
		include ($_SERVER['DOCUMENT_ROOT'] . '/sys/lib/ceditor.php');
		return createEditor($this->name, $this->value, $this->width, $this->height);
	}
}

class CTextArea
{
	var $title, $name;
	var $value, $width, $height, $check, $attribute;    // optional

	function CTextArea($title, $name)
	{
		$this->title = $title;
		$this->name = $name;
		$this->value = $this->check = '';
		$this->width = '97%';
		$this->height = '100';
        $this->func = "";
        $this->css = "";
	}

	function SetValue($value)
	{
		$this->value = htmlspecialchars(br2nl($value), ENT_QUOTES);
	}


	function GetHtml()
	{
		global $frmCheckJs, $focusInput, $msgPtNoData2;
		if ($focusInput == '') $focusInput = $this->name;

		if ($this->check)
		{
			$frmCheckJs .= "if (CheckTextEmpty(document.getElementById(\"fm\").{$this->name}, \"[$this->title] $msgPtNoData2\") == false) return false; \n";
        }

		// return "     <textarea id={$this->name} name={$this->name} style='width:{$this->width}; height:{$this->height}' class=Text>{$this->value}</textarea> \n";
        
		$ret  = "\t<textarea id={$this->name} name={$this->name} {$this->func} style='{$this->css}; resize:none; width:{$this->width}; height:{$this->height}' class=Text>{$this->value}</textarea> $this->check \n";
        $ret .= "<script src='/sys/lib/js/textarea.js'></script>";
		return $ret;
	}        
}

class CButton
{
	var $title, $name;
	var $value, $func;    // optional

	function CButton($title, $name)
	{
		$this->title = $title;
		$this->name = $name;
		$this->value = $this->func = '';
	}

	function GetHtml() {  return "\t<input id={$this->name} type=button class=button value='{$this->value}' onclick='$this->func'> \n";  }
}

class CFile
{
	var $title, $name;
	var $value, $func;    // optional

	function CFile($title, $name)
	{
		$this->title = $title;
		$this->name = $name;
		$this->value = $this->func = '';
	}

	function GetHtml() {  return "\t<input type=file name=\"$this->name\" class=button> \n";  }
}

class CRadio
{
	var $title, $name;
	var $br, $func;    // optional
	var $items;

	function CRadio($title, $name)
	{
		$this->title = $title;
		$this->name = $name;
		$this->br = $this->func = '';
		$this->items = '';
	}

	function AddItem($value, $msg, $checked='')
	{
		$this->items .= "<input type=radio name={$this->name} $checked value=\"$value\" onclick=\"{$this->func}()\"> $msg &nbsp {$this->br} \n";
	}

	function GetHtml() {  return $this->items;  }
}

class CCheckbox
{
	var $title, $name;
	var $br, $func;    // optional
	var $items;

	function CCheckbox($title, $name)
	{
		$this->title = $title;
		$this->name = $name;
		$this->br = $this->func = '';
		$this->items = '';
	}

	function AddItem($value, $msg, $checked='')
	{
		$this->items .= "<input type=checkbox name=\"{$this->name}[]\" $checked value=\"$value\" onclick=\"{$this->func}()\"> $msg &nbsp {$this->br} \n";
	}

	function GetHtml() {  return $this->items;  }
}

class CSelect
{
	var $title, $name;
	var $func;    // optional
	var $items;
	var $class;

	function CSelect($title, $name)
	{
		$this->title = $title;
		$this->name = $name;
		$this->func = '';
		$this->items = '';
		$this->class = '';
	}

	function AddItem($value, $msg, $selected='')
	{
	    $class = ($this->class == '') ? " class=Text " : " class=" . $this->class;
		if ($this->items == '')
		{
			$onchange = ($this->func == '') ? '' : "onchange=\"{$this->func}()\"";
			$this->items = "<select id=$this->name name=$this->name $class $onchange> \n";
		}
		$this->items .= "<option $selected value=\"$value\">$msg</option> \n";
	}

	function GetHtml() {  return $this->items . '</select>';  }
}


class CHtml
{
	var $title;
	function CHtml($title) {  $this->title = $title;  }
	function GetHtml()
	{
	}
}




class CForm
{
	var $width;
	var $leftWidth;
	var $rightWidth;
	var $caption;
	var $hidden;
	var $submit, $reset, $frmButtons, $link;

	function CForm($caption, $action, $enctype='', $left='150', $right='', $width='100%')
	{
		if ($enctype)
			$enctype = " enctype=\"$enctype\"";

		$this->width = $width;
		$this->leftWidth = "width=$left";
		$this->rightWidth = ($right == '') ? '' : "width=$right";
		$this->caption = $caption;
		$this->hidden = '';

		$this->submit = 'Submit';
		$this->reset = 'Reset';
		$this->frmButtons = '';
        $this->link = '';
		$thi->btnCallback = 'cfrm_btnCallback';

		echo "<form id=fm name=fm method=POST action=\"$action\"$enctype onsubmit=\"return fmSubmitCheck()\">\n\n";
	}

	function StartFormatedForm()
	{
		echo "<table cellpadding=2 cellspacing=1 class=Form width=$this->width>\n";

		if ($this->caption != '')
			echo "\t<tr height=25px><td class=FormHeader colspan=2 align=left>$this->caption</td></tr>";
	}

	function SetFocus($ctrlName)
	{
		global $focusInput;
		$focusInput = $ctrlName;
	}

	function AddHidden($name, $value)
	{
		//$this->hidden .= "<input type=hidden id=$name name=$name value=\"$value\"> \n";
		echo "<input type=hidden id=$name name=$name value=\"$value\">\n";
	}

	function AddInput($obj, $msg='', $tr='')
	{
		echo "<tr$tr>\n";
		echo "\t<td class=FormLeft {$this->leftWidth}>$obj->title</td>\n";
		echo "\t<td class=FormRight {$this->rightWidth}> \n";
		if ($msg != '') $msg = " <span id=msg_{$obj->name}>$msg</span>";
		echo $obj->GetHtml() . "$msg\n";
		echo "\t</td>\n";
		echo "</tr>\n";
	}

	function AddButton($value, $callback, $id='')
	{
		$this->frmButtons .= "<input id='$id' type='button' value='$value' onclick='$callback()' class=button>";
	}        
    
	function HR()
	{
	    echo "<tr><td colspan=2><hr></td></tr>";
	}

	function Close()
	{
		global $frmOutputJS;
		echo "</table>\n\n";

		//echo $this->hidden;
		echo '<table border=0><tr><td align=left width=100%>';
		if ($this->submit)
			echo "<input type=submit id=\"id_submit\" value=\"$this->submit\" class=button>";

		if ($this->reset != "") echo "<input type=reset value=\"$this->reset\" class=button> \n";
		echo $this->frmButtons;
        echo $this->link;

		echo '</td></tr></table>';
		echo "</form>\n\n";
		if ($frmOutputJS == 1)
		{
			$this->OutputJS();
			$frmOutputJS = 0;
		}
	}

	function OutputJS()
	{
		global $frmCheckJs, $focusInput, $PHP_ROOT, $msgPtNoData2, $msgUserPtAppPassword, $msgUserPtEmail;
		echo "
<script src='/sys/lib/js/calendar.js'></script>
<script src='/sys/lib/js/datetime.js'></script>
<script>
function CheckForm()
{
	$frmCheckJs
	return true;
}

function CheckTextEmpty(ctrl, msg)
{
	if (trim(ctrl.value) == '')
	{
		alert(msg);
		ctrl.focus();
		return false;
	}
	return true;
}

function CheckDate(ctrl, title, allow_empty)
{
	var date = trim(ctrl.value);
	if (date == '' && allow_empty) return true;
	if (CheckTextEmpty(ctrl, \"[\" + title + \"] $msgPtNoData2\") == false) return false;

	if (dateCheck(date, \"%yyyy-%mm-%dd\") == false)
	{
		ctrl.focus();
		return false;
	}
	return true;
}

function CheckEmail(ctrl, title)
{
	if (CheckTextEmpty(ctrl, \"[\" + title + \"] $msgPtNoData2\") == false) return false;
	if (ctrl.value.length > 0)
	{
		at = ctrl.value.indexOf(\"@\");
		if (at == -1)
        {
            alert(\"[\" + title + \"] $msgUserPtEmail\");
            ctrl.focus();
            return false;
        }

        var at2 = ctrl.value.indexOf(\"@\", (at+1));
		var dot = ctrl.value.indexOf(\".\",at);
		var len = ctrl.value.length;
		var comma = ctrl.value.indexOf(\",\");
		var space = ctrl.value.indexOf(\" \");
		var lastToken = ctrl.value.lastIndexOf(\".\") + 1;
		if ((at2 != -1) || (dot <= (1+1)) || (comma != -1) || (space != -1) || (len-lastToken < 2))
		{
			alert(\"[\" + title + \"] $msgUserPtEmail\");
			ctrl.focus();
			return false;
		}
	}
	return true;
}

function CheckPassword(ctrl, title, check_empty)
{
	var ctrl2 = document.getElementById(ctrl.id + \"2\");

	if (check_empty == \"*\")
	if (CheckTextEmpty(ctrl, \"[\" + title + \"] $msgPtNoData2\") == false) return false;

	if (ctrl.value != ctrl2.value)
	{
		alert(\"[\" + title + \"] $msgUserPtAppPassword\");
		return false;
	}

	return true;
}

function _setFocus()
{
	try
	{
        if (\"$focusInput\")
		document.getElementById(\"$focusInput\").focus();
	} catch (e) { };
}
window.setTimeout(\"_setFocus()\", 300);
</script>
";
    }
}
?>