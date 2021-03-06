<?php

namespace classes;

use classes\App;

/**
 * Class BBCODE_EDITOR
 *
 * Textarea with BBCode support
 *
 */

class BBCodeEditor
{

    private $__controlId;
    private $__value;
    private $__imagePath;
    private $__width;
    private $__height;

    public function __construct()
    {
        if (isset($__numControls)) {
            $__numControls++;
        } else {
            $__numControls = 1;
        }
        $this->__controlId = $__numControls;
        if (array_key_exists('bbcode_textarea', $_POST)) {
            $this->__value = $_POST['bbcode_textarea'];
        } else {
            $this->__value = '';
        }
        $this->__imagePath = '';
        $this->__width = 0;
        $this->__cols = 0;
    }

    /**
     * Parse BBCode to HTML
     *
     * @param string $string Input string
     *
     * @return string Output string
     */

    private function bb_parse($string)
    {
        $string = strip_tags($string);
        while (preg_match_all('`\[(.+?)=?(.*?)\](.+?)\[/\1\]`', $string, $matches)) {
            foreach ($matches[0] as $key => $match) {
                list($tag, $param, $innertext) = [$matches[1][$key], $matches[2][$key], $matches[3][$key]];
                switch ($tag) {
                    case 'b':
                        $replacement = "<strong>$innertext</strong>";
                        break;
                    case 'i':
                        $replacement = "<em>$innertext</em>";
                        break;
                    case 'u':
                        $replacement = "<u>$innertext</u>";
                        break;
                    case 'size':
                        $replacement = "<span style=\"font-size: $param;\">$innertext</a>";
                        break;
                    case 'color':
                        $replacement = "<span style=\"color: $param;\">$innertext</a>";
                        break;
                    case 'left':
                        $replacement = "<p align=left>$innertext</p>";
                        break;
                    case 'center':
                        $replacement = "<p align=center>$innertext</p>";
                        break;
                    case 'right':
                        $replacement = "<p align=right>$innertext</p>";
                        break;
                    case 'quote':
                        $replacement = "<blockquote>$innertext</blockquote>";
                        break;
                    case 'url':
                        $replacement = '<a href="' . ($param ? $param : $innertext) . "\">$innertext</a>";
                        break;
                    case 'img':
                        $replacement = "<img src=\"$innertext\" border=0>";
                        break;
                }
                $string = str_ireplace($match, $replacement, $string);
            }
        }
        return $string;
    }


    /**
     * Show textarea with BBCode controls.
     *
     * @param integer $Width Textarea width
     * @param integer $Height Textarea heught
     * @param string $ImagePath Path to controls images
     *
     * @return void
     */
    private function ShowControl($Width, $Height, $ImagePath): void
    {
        $this->__width = $Width;
        $this->__height = $Height;
        $this->__imagePath = $ImagePath;
        ?>
        <style>
            #bbcode_editor table {
                border: #999999 1px solid;  
                background-color:#D7D7D7;
                width: <?= $this->__width ?>px;
                height: <?= $this->__height ?>px;
                padding: 0px;
                margin: 0px;
                font-size: 11px; 
                font-family: Verdana, Arial, Helvetica, sans-serif;
                color: #111111;
            }       
            #bbcode_editor table > tr,td {
                border-width: 0px;
                padding: 0px;
                margin: 0px;
            }       
            #bbcode_editor .buttons {
                padding-top: 2px;
                padding-left: 3px;
                font-size: 11px; 
                max-width: 220px;
            }       
            #bbcode_editor .color_select {
                font-size: 10px;
                font-family: Verdana, Arial, Helvetica, sans-serif;
            }       
            #bbcode_editor select.color_select {
                border: #999999 1px solid;  
            }       
            #bbcode_textarea {
                border: #999999 1px solid;  
            }       
            #bbcode_helpbox {
                border-style:none;
                background-color:#D7D7D7;
                margin:0px;
                font-size: 10px;
            }
        </style>
        <div id=bbcode_editor>
            <table>
                <tr valign="middle">
                    <td class=buttons>
                        <a href=# onclick="javascript:return tag_misc('[b]', '[/b]');" onMouseOver="helpline('bold')"><img src=<?= $this->__imagePath ?>/bold.gif border=0></a>
                        <a href=# onclick="javascript:return tag_misc('[i]', '[/i]');" onMouseOver="helpline('italic')"><img src=<?= $this->__imagePath ?>/italic.gif border=0></a>
                        <a href=# onclick="javascript:return tag_misc('[u]', '[/u]');" onMouseOver="helpline('underline')"><img src=<?= $this->__imagePath ?>/underline.gif border=0></a>
                        <a href=# onclick="javascript:return tag_misc('[quote]', '[/quote]');" onMouseOver="helpline('quote')"><img src=<?= $this->__imagePath ?>/quote.gif border=0></a>
                        <a href=# onclick="javascript:return tag_misc('[left]', '[/left]');" onMouseOver="helpline('left')"><img src=<?= $this->__imagePath ?>/left.gif border=0></a>
                        <a href=# onclick="javascript:return tag_misc('[center]', '[/center]');" onMouseOver="helpline('center')"><img src=<?= $this->__imagePath ?>/center.gif border=0></a>
                        <a href=# onclick="javascript:return tag_misc('[right]', '[/right]');" onMouseOver="helpline('right')"><img src=<?= $this->__imagePath ?>/right.gif border=0></a>
                        <a href=# onclick="javascript:return tag_url();" onMouseOver="helpline('url')"><img src=<?= $this->__imagePath ?>/link.gif border=0></a>
                        <a href=# onclick="javascript:return tag_img();" onMouseOver="helpline('img')"><img src=<?= $this->__imagePath ?>/image.gif border=0></a>
                    </td>
                    <td class=color_select>
                        ???????? ????????????: <select name="fontcolor" 
                                        onChange="tag_misc('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]');
                                        this.selectedIndex = 0;" onMouseOver="helpline('fontcolor')" class="color_select">
                            <option value="black" style="color:black">Black</option>
                            <option value="silver" style="color:silver">Silver</option>
                            <option value="gray" style="color:gray">Gray</option>
                            <option value="maroon" style="color:maroon">Maroon</option>
                            <option value="red" style="color:red">Red</option>                      
                            <option value="purple" style="color:purple">Purple</option>
                            <option value="fuchsia" style="color:fuchsia">Fuchsia</option>
                            <option value="navy" style="color:navy">Navy</option>
                            <option value="blue" style="color:blue">Blue</option>
                            <option value="aqua" style="color:aqua">Aqua</option>
                            <option value="teal" style="color:teal">Teal</option>
                            <option value="lime" style="color:lime">Lime</option>
                            <option value="green" style="color:green">Green</option>
                            <option value="olive" style="color:olive">Olive</option>
                            <option value="yellow" style="color:yellow">Yellow</option>
                            <option value="white" style="color:white">White</option>                    
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <textarea cols="<?= ($this->__width)/7 ?>" rows="12" name="bbcode_textarea" id="bbcode_textarea"><?= $this->__value ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <input type="text" id="bbcode_helpbox" size="40" readonly="readonly">
                    </td>
                </tr>
            </table>
        </div>
        <script language="javascript">
            //Helpbox messages
            bold_help = "???????????? ??????????: [b]text[/b]";
            italic_help = "?????????????????? ??????????: [i]text[/i]";
            underline_help = "????????????????????????: [u]text[/u]";
            quote_help = "????????????: [quote]text[/quote] ?????? [quote=name]text[/quote]";
            left_help = "???????????????????????? ??????????: [left]text[/left]";
            center_help = "???????????????????????? ???? ????????????: [center]text[/center]";
            right_help = "???????????????????????? ????????????: [right]text[/right]";
            img_help = "???????????????? ??????????????????????: [img]http://image_url[/img]";
            url_help = "???????????????? ????????????: [url]http://url[/url] ?????? [url=http://url]URL text[/url]";
            fontcolor_help = "???????? ????????????: [color=red]text[/color]";
            fontsize_help = "???????????? ????????????: [size=50%]small text[/size]";

            // Shows the help messages in the helpline window
            function helpline(help) {
                var helpbox = document.getElementById("bbcode_helpbox");
                helpbox.value = eval(help + "_help");
            }
            // Remember the current position.
            function storeCaret(text) {
                // Only bother if it will be useful.
                if (typeof (text.createTextRange) != "undefined")
                    text.caretPos = document.selection.createRange().duplicate();
            }
            // Replaces the currently selected text with the passed text.
            function replaceText(text, textarea) {
                // Attempt to create a text range (IE).
                if (typeof (textarea.caretPos) != "undefined" && textarea.createTextRange) {
                    var caretPos = textarea.caretPos;
                    if (caretPos.text.charAt(caretPos.text.length - 1) == ' ') {
                        caretPos.text = text + ' ';
                    } else {
                        caretPos.text = text;
                    }
                    caretPos.select();
                }
                // Mozilla text range replace.
                else if (typeof (textarea.selectionStart) != "undefined") {
                    var begin = textarea.value.substr(0, textarea.selectionStart);
                    var end = textarea.value.substr(textarea.selectionEnd);
                    var scrollPos = textarea.scrollTop;

                    textarea.value = begin + text + end;

                    if (textarea.setSelectionRange)
                    {
                        textarea.focus();
                        textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
                    }
                    textarea.scrollTop = scrollPos;
                }
                // Just put it on the end.
                else {
                    textarea.value += text;
                    textarea.focus(textarea.value.length - 1);
                }
            }
            // Surrounds the selected text with text1 and text2.
            function surroundText(text1, text2, textarea) {
                // Can a text range be created?
                if (typeof (textarea.caretPos) != "undefined" && textarea.createTextRange) {
                    var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
                    caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

                    if (temp_length == 0) {
                        caretPos.moveStart("character", -text2.length);
                        caretPos.moveEnd("character", -text2.length);
                        caretPos.select();
                    }
                    else
                        textarea.focus(caretPos);
                }
                // Mozilla text range wrap.
                else if (typeof (textarea.selectionStart) != "undefined") {
                    var begin = textarea.value.substr(0, textarea.selectionStart);
                    var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
                    var end = textarea.value.substr(textarea.selectionEnd);
                    var newCursorPos = textarea.selectionStart;
                    var scrollPos = textarea.scrollTop;

                    textarea.value = begin + text1 + selection + text2 + end;

                    if (textarea.setSelectionRange) {
                        if (selection.length == 0)
                            textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
                        else
                            textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
                        textarea.focus();
                    }
                    textarea.scrollTop = scrollPos;
                }
                // Just put them on the end, then.
                else {
                    textarea.value += text1 + text2;
                    textarea.focus(textarea.value.length - 1);
                }
            }
            function doinsert(text1, text2, textarea) {
                // Can a text range be created?
                if (typeof (textarea.caretPos) != "undefined" && textarea.createTextRange)
                {
                    var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
                    caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text1 + text2 + ' ' : caretPos.text + text1 + text2;

                    if (temp_length == 0)
                    {
                        caretPos.moveStart("character", 0);
                        caretPos.moveEnd("character", 0);
                        caretPos.select();
                    }
                    else
                        textarea.focus(caretPos);
                }
                // Mozilla text range wrap.
                else if (typeof (textarea.selectionStart) != "undefined")
                {
                    var begin = textarea.value.substr(0, textarea.selectionStart);
                    var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
                    var end = textarea.value.substr(textarea.selectionEnd);
                    var newCursorPos = textarea.selectionStart;
                    var scrollPos = textarea.scrollTop;

                    textarea.value = begin + text1 + selection + text2 + end;

                    if (textarea.setSelectionRange)
                    {
                        if (selection.length == 0)
                            textarea.setSelectionRange(newCursorPos + text1.length + text2.length, newCursorPos + text1.length + text2.length);
                        else
                            textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
                        textarea.focus();
                    }
                    textarea.scrollTop = scrollPos;
                }
                // Just put them on the end, then.
                else
                {
                    textarea.value += text1 + text2;
                    textarea.focus(textarea.value.length - 1);
                }

            }
            function tag_misc(start_tag, end_tag) {
                var post = document.getElementById("bbcode_textarea");
                surroundText(start_tag, end_tag, post);
                return false;
            }
            function tag_url() {
                var post = document.getElementById("bbcode_textarea");
                var FoundErrors = '';
                var enterURL = prompt("?????????????? ??????????: ", "http://");
                var enterTITLE = prompt("?????????????? ???????????????? ??????????: ", "My WebPage");

                if (!enterURL || enterURL == 'http://') {
                    FoundErrors = 1;
                }
                else if (!enterTITLE) {
                    FoundErrors = 1;
                }

                if (FoundErrors) {
                    return;
                }

                doinsert('[url=' + enterURL + ']' + enterTITLE, '[/url]', post);

                return false;
            }
            function tag_img() {
                var post = document.getElementById("bbcode_textarea");
                var FoundErrors = '';
                var enterURL = prompt("?????????????? ?????????? ????????????????: ", "http://");

                if (!enterURL || enterURL == 'http://' || enterURL.length < 10) {
                    return;
                }

                doinsert("[img]" + enterURL, "[/img]", post);

                return false;
            }
        </script>

        <?php
    }

    /**
     * Get HTML and JS code of textarea with BBCode controls.
     *
     * @param integer $Width Textarea width
     * @param integer $Height Textarea heught
     * @param string $ImagePath Path to controls images
     *
     * @return string Output string
     */
    public function GetContol($Width, $Height, $ImagePath)
    {
        ob_start();
        $this->ShowControl($Width, $Height, $ImagePath);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Set control value.
     *
     * @param string $NewValue New value
     *
     * @return void
     */
    public function SetValue($NewValue): void
    {
        $this->__value = $NewValue;
    }

    /**
     * Get control value.
     *
     * @return string Output string
     */
    public function GetValue()
    {
        return App::$input['bbcode_textarea'];
    }

    /**
     * Get control value as HTML
     *
     * @return string Output string
     */
    public function GetHTML()
    {
        return $this->bb_parse(App::$input['bbcode_textarea']);
    }
}
