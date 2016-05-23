SELECT apm . *
FROM `afup_personnes_physiques` app
LEFT JOIN afup_personnes_morales apm ON app.id_personne_morale = apm.id
WHERE app.`etat` =1
AND app.id NOT
IN (
SELECT app.id
FROM `afup_personnes_physiques` app
LEFT JOIN afup_personnes_morales apm ON app.id_personne_morale = apm.id
WHERE app.`etat` =1
AND (apm.etat IS NULL OR apm.etat =1)
)