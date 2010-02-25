<?php
/* header.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003, 2004 Charles NEPOTE
Copyright 2002  Patrick PAUL
Copyright 2003  Eric DELORD
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
	$message = $this->GetMessage();
	$user = $this->GetUser();
	
	// Patch pour forcer l'identification des l'utilisateurs sur le wiki 
	if (! is_array($user) && $this->tag != "ParametresUtilisateur") {
		$this->Redirect($this->href("", 'ParametresUtilisateur'));
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>


<head>
<title><?php echo $this->GetWakkaName().":".$this->GetPageTag(); ?></title>
<?php if ($this->GetMethod() != 'show')
    echo "<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<meta name="keywords" content="<?php echo $this->GetConfigValue("meta_keywords") ?>" />
<meta name="description" content="<?php echo  $this->GetConfigValue("meta_description") ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="wakka.basic.css" />
<style type="text/css" media="all"> @import "<?php echo (!isset($_COOKIE["sitestyle"]))?'wakka':$_COOKIE["sitestyle"] ?>.css";</style>
<script type="text/javascript">
function fKeyDown()	{
	if (event.keyCode == 9) {
		event.returnValue= false;
		document.selection.createRange().text = String.fromCharCode(9) } }
</script>
</head>


<body <?php echo (!$user || ($user["doubleclickedit"] == 'Y')) && ($this->GetMethod() == "show") ? "ondblclick=\"document.location='".$this->href("edit")."';\" " : "" ?>
<?php echo $message ? "onLoad=\"alert('".$message."');\" " : "" ?> >


<div style="display: none;"><a href="<?php echo $this->href() ?>/resetstyle" accesskey="7"></a></div>


<h1 class="wiki_name"><?php echo $this->config["wakka_name"] ?></h1>


<h1 class="page_name">
<a href="<?php echo $this->config["base_url"] ?>RechercheTexte&amp;phrase=<?php echo htmlentities($this->GetPageTag()); ?>">
<?php echo htmlentities($this->GetPageTag()); ?>
</a>
</h1>


<div class="header">
<?php echo $this->ComposeLinkToPage($this->config["root_page"]); ?> ::
<?php echo $this->config["navigation_links"] ? $this->Format($this->config["navigation_links"])." :: \n" : "" ?>
<?php if ($this->GetUser() !== '') { ?><a href="../pages/administration/index.php?page=accueil">Acc&egrave;sPartieAdministration</a> ::<?php } ?>
Vous &ecirc;tes <?php echo $this->Format($this->GetUserName()); if ($user = $this->GetUser()) echo " (<a href=\"".$this->config["base_url"] ."ParametresUtilisateur&amp;action=logout\">D&eacute;connexion</a>)\n"; ?>
</div>


