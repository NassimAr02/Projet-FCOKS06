USE ListeLicencie;

DROP VIEW IF EXISTS vue_Cotisation;

CREATE VIEW vue_Cotisation AS
SELECT
    cotisation.idLicence AS "Identifiant de licence", 
    cotisation.numCotisation AS "Numéro de la cotisation",
    COUNT(ajoutePaiement.numeroVersement) AS "Nombre de versement effectué", 
    CASE
        WHEN cotisation.complet = TRUE THEN 'Oui'
        ELSE 'Non'
    END AS 'Complet',
    (cotisation.montant - IFNULL(SUM(ajoutePaiement.montantR), 0)) AS 'Reste à payer',
    MAX(ajoutePaiement.datePaiement) AS 'Date du dernier paiement',
    cotisation.moyenPaiement AS 'Moyen de paiement'
FROM
    cotisation
JOIN
    licencie ON licencie.idLicence = cotisation.idLicence
JOIN
    ajoutePaiement ON ajoutePaiement.numCotisation = cotisation.numCotisation

GROUP BY
    cotisation.numCotisation, cotisation.complet, cotisation.montant, cotisation.moyenPaiement;
