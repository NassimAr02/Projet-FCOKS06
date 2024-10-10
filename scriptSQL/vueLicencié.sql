USE ListeLicencie;
CREATE OR REPLACE VIEW vue_licencie AS
SELECT DISTINCT
    licencie.idLicence AS `Identifiant de licence :`,
    licencie.numLicence AS `Numéro de licence :`,
    licencie.fonction as 'Fonction :',
    licencie.nom AS `Nom du licencie :`,
    licencie.prenom AS `Prénom du licencie :`,
    licencie.dateNaissance AS `Date de naissance :`,
    licencie.codeCat AS `Catégorie :`,
    licencie.nomEquipe AS `Equipe du licencie :`,
    licencie.adresse AS `Adresse du licencie :`,
    licencie.cp AS `Code postale :`,
    licencie.ville AS `Ville :`,
    licencie.numTel AS `Numéro de téléphone :`,
    licencie.mail AS `Mail du licencie :`
FROM licencie;
