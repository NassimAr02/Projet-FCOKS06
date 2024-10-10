-- Assurez-vous d'être dans la bonne base de données
USE ListeLicencie;

-- Changement du délimiteur pour gérer les blocs d'instructions
DELIMITER //

-- Création de l'événement pour supprimer les cotisations chaque année
CREATE EVENT delete_cotisations
ON SCHEDULE EVERY 1 YEAR
STARTS DATE_ADD(DATE_FORMAT(NOW(), '%Y-07-01 00:00:00'), INTERVAL 1 YEAR)
DO
BEGIN
    DELETE FROM cotisation 
    WHERE YEAR(datePaiement) = YEAR(CURDATE()) - 1 
    AND datePaiement IS NOT NULL;
END//

-- Restauration du délimiteur par défaut
DELIMITER ;
