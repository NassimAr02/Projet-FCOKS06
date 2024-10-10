USE ListeLicencie;

DROP VIEW IF EXISTS vueEquipement;
CREATE VIEW vueEquipement AS

SELECT DISTINCT 
equipement.idLicence as `Identifiant de licence :`,
equipement.nomEquipement as `Equipement :`,
equipement.typeEquipement as "Type d'équipement :",
case 
    WHEN equipement.distribue = 1 THEN 'Oui'
    else 'Non' 
END as 'Distribue :',
equipement.dateDistribution as 'Date de distribution :',
equipement.tailleEquipement as 'Taille :',
case 
    WHEN equipement.coupe = 1 THEN 'Oui'
    else 'Non'
End as 'Coupe :'
FROM equipement
JOIN licencie ON licencie.idLicence = equipement.idLicence;