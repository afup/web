<?php 
namespace ESPACEdeNOM;


class cnxBDD
{ 

 var $host;
 var $login;
 var $password;
 var $base;
	

public function __construct($serveur='$serveur',$user='$user', $password='$passwd', $bdd='$bdd')
{
 $this->host=$serveur;
 $this->login=$user;
 $this->password=$password;
 $this->base=$bdd;
}

function connect() 
{ 

try
{	
$connect = mysqli_connect ($this->host, $this->login, $this->password,$this->base); 

if (mysqli_connect_errno()) 
    die ("Echec de la connexion : ". mysqli_connect_error());
 
$this->connect = $connect; 

}


catch (PDOException $error) 
{	
echo 'N° : '.$error->getCode().'<br />';
die ('Erreur : '.$error->getMessage().'<br />');
}

} 


function requeteSelect ($sql) 
{ 
try
{
	$result = $this->connect->query($sql); 

	if(!$result) 
		die ("Probleme requete : ".$sql); 

	if ($result->num_rows==0 )
	{
		$rows=0;	
	} 
	else 
	{
		while ($row = $result->fetch_object())
		{
			$rows[] = $row;
		}
	}
	return $rows;
}
catch (PDOException $error) 
{	
echo 'N° : '.$error->getCode().'<br />';
die ('Erreur : '.$error->getMessage().'<br />');
}

} 

function requeteOther ($sql) 
{ 

try
{	
	$qid=$this->connect->prepare($sql);
	$qid->execute();
}

catch (PDOException $error) 
{	
	echo 'N° : '.$error->getCode().'<br />';
	die ('Erreur : '.$error->getMessage().'<br />');
}

} 


function deconnect() 
{
	mysqli_close($this->connect); 
} 


function __destruct()
{
	unset ($this->connect);
}

} 
?>
