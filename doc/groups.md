# Google Groups

Gestion des mailing-list sur la base de groupes google.

1. Utilisation de l'api google avec le client google/apiclient
2. Création d'identifiants au format json
3. [Délégation de droits "domain-wide"](https://developers.google.com/api-client-library/php/auth/service-accounts) pour cette application
4. Création de l'utilisateur "admin-mailing-listes-api@afup.org" dédié à cet api sur le compte google - il sera "impersonifié" par le client lors des requetes
5. Attribution à cet utilisateur du role "Administrateur des groupes" (pas d'escalade de droit possible en cas de faille)
6. Une copie du fichier d'identifiants est dispo dans le gestionnaire de mot de passe partagés, nom "Compte google API Gestion mailing listes"
