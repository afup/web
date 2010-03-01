<?php
/*
usersettings.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002  Patrick PAUL
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
if (!isset($_REQUEST["action"])) $_REQUEST["action"] = '';

require_once dirname(__FILE__) . '/../../../sources/Afup/Bootstrap/OtherToolPlugin.php';

require_once 'Afup/AFUP_Base_De_Donnees.php';
require_once 'Afup/AFUP_Utils.php';

if ($_REQUEST["action"] == "logout")
{
	$bdd = new AFUP_Base_De_Donnees($GLOBALS['wakkaConfig']['mysql_host'],
                               $GLOBALS['wakkaConfig']['mysql_database'],
                               $GLOBALS['wakkaConfig']['mysql_user'],
                               $GLOBALS['wakkaConfig']['mysql_password']);
			
	$droits = AFUP_Utils::fabriqueDroits($bdd);
	$droits->seDeconnecter();

	$this->SetMessage("Vous &ecirc;tes maintenant d&eacute;connect&eacute; !");
	$this->Redirect($this->href());
} else {
	// user is not logged in
	
	// is user trying to log in or register?
	if ($_REQUEST["action"] == "login")
	{
		$bdd = new AFUP_Base_De_Donnees($GLOBALS['wakkaConfig']['mysql_host'],
                                $GLOBALS['wakkaConfig']['mysql_database'],
                                $GLOBALS['wakkaConfig']['mysql_user'],
                                $GLOBALS['wakkaConfig']['mysql_password']);
			
		$droits = AFUP_Utils::fabriqueDroits($bdd);
		$droits->seConnecter($_POST["name"], $_POST["password"]);

		if ($droits->estConnecte()) {
				$this->Redirect($this->href("", $this->config["root_page"]));
		}
		else
		{
			$error = "Mauvais mot de passe&nbsp;!";
		}
	}
	
	echo $this->FormOpen();
	?>
	<input type="hidden" name="action" value="login" />
	<table>
		<tr>
			<td></td>
			<td><?php echo $this->Format("Pour acc&egrave;der au Wiki, veuillez vous identifier"); ?></td>
		</tr>
		<?php
		if ($error)
		{
			echo "<tr><td></td><td><div class=\"error\">", $this->Format($error), "</div></td></tr>\n";
		}
		?>
		<tr>
			<td align="right">Votre NomWiki&nbsp;:</td>
			<td><input name="name" size="40" value="<?php echo $name ?>" /></td>
		</tr>
		<tr>
			<td align="right">Mot de passe &nbsp;:</td>
			<td>
			<input type="password" name="password" size="40" />
			<input type="hidden" name="remember" value="0" />
			<!--<input type="checkbox" name="remember" value="1" />&nbsp;Se souvenir de moi.-->
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Identification" size="40" /></td>
		</tr>
	</table>
<?php
	echo $this->FormClose();
}
?>