<?php
/*
wakka.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002, 2003 Patrick PAUL
Copyright  2003  Eric DELORD
Copyright  2003  Eric FELDSTEIN
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
// This may look a bit strange, but all possible formatting tags have to be in a single regular expression for this to work correctly. Yup!

if (!function_exists("wakka2callback"))
{
	function wakka2callback($things)
	{
		$thing = $things[1];
        $result='';

		static $oldIndentLevel = 0;
		static $oldIndentLength= 0;
		static $indentClosers = array();
		static $newIndentSpace= array();
		static $br = 1;

		$brf=0;
		global $wiki;
	
		// convert HTML thingies
		if ($thing == "<")
			return "&lt;";
		else if ($thing == ">")
			return "&gt;";
		// bold
		else if ($thing == "**")
		{
			static $bold = 0;
			return (++$bold % 2 ? "<b>" : "</b>");
		}
		// italic
		else if ($thing == "//")
		{
			static $italic = 0;
			return (++$italic % 2 ? "<i>" : "</i>");
		}
		// underlinue
		else if ($thing == "__")
		{
			static $underline = 0;
			return (++$underline % 2 ? "<u>" : "</u>");
		}
		// monospace
		else if ($thing == "##")
		{
			static $monospace = 0;
			return (++$monospace % 2 ? "<tt>" : "</tt>");
		}
		// Deleted 
                else if ($thing == "@@")
                {
                        static $deleted = 0;
                        return (++$deleted % 2 ? "<span class=\"del\">" : "</span>");
                }
                // Inserted
                else if ($thing == "££")
                {
                        static $inserted = 0;
                        return (++$inserted % 2 ? "<span class=\"add\">" : "</span>");
                }
		// urls
		else if (preg_match("/^([a-z]+:\/\/\S+?)([^[:alnum:]^\/])?$/", $thing, $matches)) {
			$url = $matches[1];
			if (!isset($matches[2])) $matches[2] = '';
			return "<a href=\"$url\">$url</a>".$matches[2];
		}
		// header level 5
                else if ($thing == "==")
                {
                        static $l5 = 0;
			$br = 0;
                        return (++$l5 % 2 ? "<h5>" : "</h5>");
                }
		// header level 4
                else if ($thing == "===")
                {
                        static $l4 = 0;
			$br = 0;
                        return (++$l4 % 2 ? "<h4>" : "</h4>");
                }
		// header level 3
                else if ($thing == "====")
                {
                        static $l3 = 0;
			$br = 0;
                        return (++$l3 % 2 ? "<h3>" : "</h3>");
                }
		// header level 2
                else if ($thing == "=====")
                {
                        static $l2 = 0;
			$br = 0;
                        return (++$l2 % 2 ? "<h2>" : "</h2>");
                }
		// header level 1
                else if ($thing == "======")
                {
                        static $l1 = 0;
			$br = 0;
                        return (++$l1 % 2 ? "<h1>" : "</h1>");
                }
		// forced line breaks
		else if ($thing == "---")
		{
			return "<br />";
		}
		// escaped text
		else if (preg_match("/^\"\"(.*)\"\"$/s", $thing, $matches))
		{
			return $matches[1];
		}
		// code text
		else if (preg_match("/^\%\%(.*)\%\%$/s", $thing, $matches))
		{
			// check if a language has been specified
			$code = $matches[1];
			$language='';
			if (preg_match("/^\((.+?)\)(.*)$/s", $code, $matches))
			{
				list(, $language, $code) = $matches;
			}
			//Select formatter for syntaxe hightlighting
			if (file_exists("formatters/coloration_".$language.".php")){
				$formatter = "coloration_".$language;
			}else{
				$formatter = "code";
			}

			$output = "<div class=\"code\">";
			$output .= $wiki->Format(trim($code), $formatter);
			$output .= "</div>";

			return $output;
		}
		// raw inclusion from another wiki
		// (regexp documentation : see "forced link" below)
		else if (preg_match("/^\[\[\|(\S*)(\s+(.+))?\]\]$/", $thing, $matches))
		{
			list (,$url,,$text) = $matches;
			if (!$text) $text = "404";
			if ($url)
			{
    				$url.="/wakka.php?wiki=".$text."/raw";
				return $wiki->Format($wiki->Format($url, "raw"),"wakka");
			}
			else
			{
				return "";
			}
		}
		// forced links
		// \S : any character that is not a whitespace character
		// \s : any whitespace character
		else if (preg_match("/^\[\[(\S*)(\s+(.+))?\]\]$/", $thing, $matches))
		{
			list (, $url, $text) = $matches;
			if ($url)
			{
				if ($url!=($url=(preg_replace("/@@|££|\[\[/","",$url))))$result="</span>";
				if (!$text) $text = $url;
				$text=preg_replace("/@@|££|\[\[/","",$text);
				return $result.$wiki->Link($url, "", $text);
			}
			else
			{
				return "";
			}
		}
		// indented text
		else if ((preg_match("/\n(\t+|([ ]{1})+)(-|([0-9,a-z,A-Z]+)\))?/s", $thing, $matches))
		 ||  (preg_match("/^(\t+|([ ]{1})+)(-|([0-9,a-z,A-Z]+)\))?/s", $thing, $matches) && $brf=1))
		{
			// new line
			if ($brf) $br=0;
			$result .= ($br ? "<br />\n" : "");

			// we definitely want no line break in this one.
			$br = 0;

			// find out which indent type we want
			if (!isset($matches[3])) $matches[3] = '';
			$newIndentType = $matches[3];
			if (!$newIndentType) { $opener = "<div class=\"indent\">"; $closer = "</div>"; $br = 1; }
			else if ($newIndentType == "-") { $opener = "<ul>\n"; $closer = "</li>\n</ul>"; $li = 1; }
			else { $opener = "<ol type=\"".$matches[4]."\">\n"; $closer = "</li>\n</ol>"; $li = 1; }

			// get new indent level
			
			if (strpos($matches[1],"\t")) $newIndentLevel = strlen($matches[1]);
			else
			{
				$newIndentLevel=$oldIndentLevel;
				$newIndentLength = strlen($matches[1]);
				if ($newIndentLength>$oldIndentLength)
				{ 
					$newIndentLevel++;
					$newIndentSpace[$newIndentLength]=$newIndentLevel;
				}
				else if ($newIndentLength<$oldIndentLength)
						$newIndentLevel=$newIndentSpace[$newIndentLength];
			}
  			$op=0;
			if ($newIndentLevel > $oldIndentLevel)
			{
				for ($i = 0; $i < $newIndentLevel - $oldIndentLevel; $i++)
				{
					$result .= $opener;
					$op=1;
					array_push($indentClosers, $closer);
				}
			}
			else if ($newIndentLevel < $oldIndentLevel)
			{
				for ($i = 0; $i < $oldIndentLevel - $newIndentLevel; $i++)
				{
					$op=1;
					$result .= array_pop($indentClosers);
			                if ($oldIndentLevel && $li) $result .= "</li>";
				}
			}

			if (isset($li) && $op) $result .= "<li>";
			else if (isset($li))
				$result .= "</li>\n<li>";

			$oldIndentLevel = $newIndentLevel;
			$oldIndentLength= $newIndentLength;

			return $result;
		}
		// new lines
		else if ($thing == "\n")
		{
			// if we got here, there was no tab in the next line; this means that we can close all open indents.
			$c = count($indentClosers);
			for ($i = 0; $i < $c; $i++)
			{
				$result .= array_pop($indentClosers);
				$br = 0;
			}
			$oldIndentLevel = 0;
			$oldIndentLength= 0;
			$newIndentSpace=array();

			$result .= ($br ? "<br />\n" : "\n");
			$br = 1;
			return $result;
		}
		// events
		else if (preg_match("/^\{\{(.*?)\}\}$/s", $thing, $matches))
		{
			if ($matches[1])
				return $wiki->Action($matches[1]);
			else
				return "{{}}";
		}
		// interwiki links!
                else if (preg_match("/^[A-Z][A-Z,a-z]+[:]([A-Z,a-z,0-9]*)$/s", $thing))

		{
			return $wiki->Link($thing);
		}
		// wiki links!
		else if (preg_match("/^[A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*$/s", $thing))
		{
			return $wiki->Link($thing);
		}
		// separators
		else if (preg_match("/-{4,}/", $thing, $matches))
		{
			// TODO: This could probably be improved for situations where someone puts text on the same line as a separator.
			//       Which is a stupid thing to do anyway! HAW HAW! Ahem.
			$br = 0;
			return "<hr />";
		}
		// if we reach this point, it must have been an accident.
		return $thing;
	}
}


$text = str_replace("\r", "", $text);
$text = chop($text)."\n";
$text = preg_replace_callback(
	"/(\%\%.*?\%\%|".
	"\"\".*?\"\"|".
	"\[\[.*?\]\]|".
	"\b[a-z]+:\/\/\S+|".
	"\*\*|\#\#|@@|££|__|<|>|\/\/|".
	"======|=====|====|===|==|".
	"-{4,}|---|".
	"\n(\t+|([ ]{1})+)(-|[0-9,a-z,A-Z]+\))?|".
	"^(\t+|([ ]{1})+)(-|[0-9,a-z,A-Z]+\))?|".
	"\{\{.*?\}\}|".
        "\b[A-Z][A-Z,a-z]+[:]([A-Z,a-z,0-9]*)\b|".
	"\b([A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*)\b|".
	"\n)/ms", "wakka2callback", $text);

// we're cutting the last <br />
$text = preg_replace("/<br \/>$/","", trim($text));
echo $text ;
?>
