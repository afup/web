ALTER TABLE `afup_forum`
ADD `place_name` varchar(255) NULL,
ADD `place_address` varchar(255) NULL AFTER `place_name`;


UPDATE afup_forum SET place_name = "Beffroi de Montrouge", place_address="2 Place Emile Cresp, 92120 Montrouge" WHERE id = 13;
UPDATE afup_forum SET place_name = "Le Polydôme", place_address="Place Du Premier Mai, 63100 Clermont-Ferrand" WHERE id = 14;
UPDATE afup_forum SET place_name = "Beffroi de Montrouge", place_address="2 Place Emile Cresp, 92120 Montrouge" WHERE id = 15;
UPDATE afup_forum SET place_name = "C.C.O. de Nantes", place_address="Tour Bretagne - Place de Bretagne, 44047 Nantes" WHERE id = 16;
UPDATE afup_forum SET place_name = "Marriott Paris Rive Gauche Hotel & Conference Center", place_address="17 Boulevard Saint-Jacques, 75014 Paris" WHERE id = 17;
