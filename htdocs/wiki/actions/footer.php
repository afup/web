

<div class="footer">
<?php
/* footer.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003, 2004 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002, 2003  Patrick PAUL
Copyright  2003  Eric DELORD
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
echo  $this->FormOpen("", "RechercheTexte", "get");
echo  $this->HasAccess("write") ? "<a href=\"".$this->href("edit")."\" title=\"Cliquez pour &eacute;diter cette page.\">&Eacute;diter cette page</a> ::\n" : "";
echo  $this->GetPageTime() ? "<a href=\"".$this->href("revisions")."\" title=\"Cliquez pour voir les derni&egrave;res modifications sur cette page.\">".$this->GetPageTime()."</a> ::\n" : "";
	// if this page exists
	if ($this->page)
	{
		// if owner is current user
		if ($this->UserIsOwner())
		{
			echo 
			"Propri&eacute;taire&nbsp;: vous :: \n",
			"<a href=\"",$this->href("acls")."\" title=\"Cliquez pour &eacute;diter les permissions de cette page.\">&Eacute;diter permissions</a> :: \n",
			"<a href=\"",$this->href("deletepage")."\">Supprimer</a> :: \n";
		}
		else
		{
			if ($owner = $this->GetPageOwner())
			{
				echo "Propri&eacute;taire : ",$this->Format($owner);
			}
			else
			{
				echo "Pas de propri&eacute;taire ";
				echo ($this->GetUser() ? "(<a href=\"".$this->href("claim")."\">Appropriation</a>)" : "");
			}
			echo " :: \n";
		}
	}
?>
<a href="<?php echo $this->href("referrers") ?>" title="Cliquez pour voir les URLs faisant r&eacute;f&eacute;rence &agrave; cette page.">
R&eacute;f&eacute;rences</a> ::
Recherche : <input name="phrase" size="15" class="searchbox" />
<?php echo  $this->FormClose(); ?>
</div>


<div class="copyright">
<a href="http://validator.w3.org/check/referer">XHTML 1.0 valide ?</a> ::
<a href="http://jigsaw.w3.org/css-validator/check/referer">CSS valide ?</a> ::
-- Fonctionne avec <?php echo $this->Link("WikiNi:PagePrincipale", "", "WikiNi ".$this->GetWikiNiVersion()) . "\n"; ?>
</div>


<?php
	if ($this->GetConfigValue("debug")=="yes")
	{
		echo "<span class=\"debug\"><b>Query log :</b><br />\n";
		$t_SQL=0;
        foreach ($this->queryLog as $query)
		{
			echo $query["query"]." (".round($query["time"],4).")<br />\n";
			$t_SQL = $t_SQL + $query["time"];
		}
		echo "</span>\n";

		echo "<span class=\"debug\">".round($t_SQL, 4)." s (total SQL time)</span><br />\n";
		
		list($g2_usec, $g2_sec) = explode(" ",microtime());
		define ("t_end", (float)$g2_usec + (float)$g2_sec);
		echo "<span class=\"debug\"><b>".round(t_end-t_start, 4)." s (total time)</b></span><br />\n";

		echo "<span class=\"debug\">SQL time represent : ".round((($t_SQL/(t_end-t_start))*100),2)."% of total time</span>\n";
	}
?> 


</body>
</html>
