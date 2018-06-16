-- Script permettant de rendre anonyme la base AFUP avant sa mise à disposition !

update afup_accreditation_presse set nom = 'Nom ' + id, prenom = 'Prenom ' + id, carte_presse = 'PRESSE_' + id, adresse = id + ' rue grande', telephone = '0102030405', email = 'presse_' + id + '@afup.org';
update afup_blacklist set email = 'blacklist_' + id + '@afup.org';
update afup_compta_facture set societe = 'Societe ' + id, nom = 'Nom ' + id, prenom = 'Prenom ' + id, adresse = id + ' rue grande', tel = '0102030405', email = 'facture_' + id + '@afup.org', observation = '', ref_clt1 = '', ref_clt2 = '', ref_clt3 = '';

SET @row_number = 0;
alter table afup_conferenciers add column id int null;
update afup_conferenciers set id = (@row_number:=@row_number + 1);
update afup_conferenciers set societe = 'Societe ' + id, nom = 'Nom ' + id, prenom = 'Prenom ' + id, email = 'conferenciers_' + id + '@afup.org', biographie = '', twitter = '';
alter table afup_conferenciers drop column id;

update afup_contacts set nom = 'Nom ' + id, prenom = 'Prenom ' + id, email = 'contact_' + id + '@afup.org', organisation = '';
update afup_cotisations set commentaires = '';

SET @row_number = 0;
alter table afup_email add column id int null;
update afup_email set id = (@row_number:=@row_number + 1);
update afup_email set email = 'email_' + id + '@afup.org';
alter table afup_email drop column id;

alter table afup_facturation_forum add column id int null;
SET @row_number = 0;
update afup_facturation_forum set id = (@row_number:=@row_number + 1);
update afup_facturation_forum set societe = 'Societe ' + id, nom = 'Nom ' + id, prenom = 'Prenom ' + id, adresse = id + ' rue grande', email = 'facture_' + id + '@afup.org';
alter table afup_facturation_forum drop column id;
update afup_inscriptions_rappels set email = 'rappel_' + id + '@afup.org';
update afup_inscription_forum set nom = 'Nom ' + id, prenom = 'Prenom ' + id, telephone = '0102030405', email = 'facture_' + id + '@afup.org', commentaires='';

truncate table afup_logs;
update afup_personnes_morales set raison_sociale = 'Societe ' + id, siret = id, nom = 'Nom ' + id, prenom = 'Prenom ' + id, adresse = id + ' rue grande', telephone_fixe = '0102030405', telephone_portable = '0602030405', email = 'persmorale' + id + '@afup.org';
update afup_personnes_physiques set login = 'l'+ id, mot_de_passe = md5(id), compte_svn = 'svn_' + id, nom = 'Nom ' + id, prenom = 'Prenom ' + id, adresse = id + ' rue grande', telephone_fixe = '0102030405', telephone_portable = '0602030405', email = 'persphysique' + id + '@afup.org';


update afup_rendezvous_inscrits set entreprise = 'Societe ' + id, nom = 'Nom ' + id, prenom = 'Prenom ' + id, telephone = '0102030405', email = 'facture_' + id + '@afup.org';

update annuairepro_MembreAnnuaire set RaisonSociale = 'Societe ' + id, SIREN = id, Adresse = id + ' rue grande', telephone = '0102030405', Fax = '', Email = 'membre_' + id + '@afup.org', SiteWeb = '', NumeroFormateur = '';
-- Abandonné ?
-- update annuairepro_MembreAnnuaire_iso set RaisonSociale = 'Societe ' + id, SIREN = id, Adresse = id + ' rue grande', telephone = '0102030405', Fax = '', Email = 'membre_' + id + '@afup.org', SiteWeb = '', NumeroFormateur = '', Password = md5(ID);

truncate table rdv_afup;