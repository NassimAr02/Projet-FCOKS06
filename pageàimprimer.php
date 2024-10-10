<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations du Licencié</title>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #recapToPrint, #recapToPrint * {
                visibility: visible;
            }
            #recapToPrint {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .recap-section {
            margin-bottom: 10px;
        }
        .recap-section h2 {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .recap-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .recap-section table th, .recap-section table td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 12px;
        }
        .recap-section table th {
            background-color: #f2f2f2;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-section img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body onload="window.print()">
    <div id="recapToPrint">
        <div class="logo-section">
            <img src="logo.png" alt="Logo du Club">
        </div>
        <?php
        // Inclure le fichier de connexion PDO
        include "connPDO.php";
        
        // Récupérer les données du formulaire POST
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomLicencié']) && isset($_POST['prenomLicencié']) && isset($_POST['saison']) && isset($_POST['fonction'])) {
            $nomLicencié = $_POST['nomLicencié'];
            $prenomLicencié = $_POST['prenomLicencié'];
            $fonction = $_POST['fonction'];
            $saison = $_POST['saison'];
            
            // Définir les dates de début et de fin en fonction de la saison sélectionnée
            $saisons = [
                '2022-2023' => ['start' => '2022-08-01', 'end' => '2023-07-05'],
                '2023-2024' => ['start' => '2023-08-01', 'end' => '2024-07-05'],
                '2024-2025' => ['start' => '2024-08-01', 'end' => '2025-07-05'],
                '2025-2026' => ['start' => '2025-08-01', 'end' => '2026-07-05'],
                '2026-2027' => ['start' => '2026-08-01', 'end' => '2027-07-05'],
                '2027-2028' => ['start' => '2027-08-01', 'end' => '2028-07-05'],
                '2028-2029' => ['start' => '2028-08-01', 'end' => '2029-07-05'],
                '2029-2030' => ['start' => '2029-08-01', 'end' => '2030-07-05']
            ];
            
            if (isset($saisons[$saison])) {
                $dateDebut = $saisons[$saison]['start'];
                $dateFin = $saisons[$saison]['end'];
            }
            
            try {
                // Requête pour récupérer les données du licencié
                $sql = "SELECT * FROM vue_licencie WHERE `Nom du licencie :` = :nom AND `Prénom du licencie :` = :prenom AND `Fonction :` = :fonction";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nom', $nomLicencié);
                $stmt->bindParam(':prenom', $prenomLicencié);
                $stmt->bindParam(':fonction', $fonction);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        // Affichage des informations du licencié
                        ?>
                        <div class="recap-section">
                            <h2>Informations du licencié</h2>
                            <table>
                                <tr>
                                    <th>Numéro de licence</th>
                                    <td><?php echo htmlspecialchars($row["Numéro de licence :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Nom du licencié</th>
                                    <td><?php echo htmlspecialchars($row["Nom du licencie :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Prénom du licencié</th>
                                    <td><?php echo htmlspecialchars($row["Prénom du licencie :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Date de naissance</th>
                                    <td><?php echo htmlspecialchars(date_format(date_create($row["Date de naissance :"]), 'd/m/Y')); ?></td>
                                </tr>
                                <tr>
                                    <th>Catégorie</th>
                                    <td><?php echo htmlspecialchars($row["Catégorie :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Equipe du licencié</th>
                                    <td><?php echo htmlspecialchars($row["Equipe du licencie :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Adresse du licencié</th>
                                    <td><?php echo htmlspecialchars($row["Adresse du licencie :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Code postal</th>
                                    <td><?php echo htmlspecialchars($row["Code postale :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Ville</th>
                                    <td><?php echo htmlspecialchars($row["Ville :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Numéro de téléphone</th>
                                    <td><?php echo htmlspecialchars($row["Numéro de téléphone :"]); ?></td>
                                </tr>
                                <tr>
                                    <th>Mail du licencié</th>
                                    <td><?php echo htmlspecialchars($row["Mail du licencie :"]); ?></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                        
                        // Requête pour récupérer les équipements du licencié
                        $sql2 = "SELECT * FROM vueEquipement WHERE `Identifiant de Licence :` = :idLicence AND `Date de distribution :` BETWEEN :dateDebut AND :dateFin";
                        $stmt2 = $pdo->prepare($sql2);
                        $stmt2->bindParam(':idLicence', $row["Identifiant de licence :"]);
                        $stmt2->bindParam(':dateDebut', $dateDebut);
                        $stmt2->bindParam(':dateFin', $dateFin);
                        $stmt2->execute();
                        $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($result2) > 0) {
                            ?>
                            <div class="recap-section">
                                <h2>Informations équipements</h2>
                                <table>
                                    <tr>
                                        <th>Equipement</th>
                                        <th>Type d'équipement</th>
                                        <th>Distribué</th>
                                        <th>Coupé</th>
                                        <th>Date de distribution</th>
                                        <th>Taille</th>
                                    </tr>
                                    <?php foreach ($result2 as $row2) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row2["Equipement :"]); ?></td>
                                            <td><?php echo htmlspecialchars($row2["Type d'équipement :"]); ?></td>
                                            <td><?php echo htmlspecialchars($row2["Distribue :"]); ?></td>
                                            <td><?php echo htmlspecialchars($row2["Coupe :"]); ?></td>
                                            <td><?php echo htmlspecialchars(date_format(date_create($row2["Date de distribution :"]), 'd/m/Y')); ?></td>
                                            <td><?php echo htmlspecialchars($row2["Taille :"]); ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <?php
                        }
                        
                        // Requête pour récupérer les cotisations du licencié
                        $sql3 = "SELECT `Complet`, `Nombre de versement effectué`, `Reste à payer`, `Date du dernier paiement`, `Moyen de paiement`
                                 FROM vue_Cotisation
                                 WHERE `Identifiant de licence` = :idLicence AND `Date du dernier paiement` BETWEEN :dateDebut AND :dateFin";
                        $stmt3 = $pdo->prepare($sql3);
                        $stmt3->bindParam(':idLicence', $row["Identifiant de licence :"]);
                        $stmt3->bindParam(':dateDebut', $dateDebut);
                        $stmt3->bindParam(':dateFin', $dateFin);
                        $stmt3->execute();
                        $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($result3) > 0) {
                            ?>
                            <div class="recap-section">
                                <h2>Informations cotisations</h2>
                                <table>
                                    <?php foreach ($result3 as $row3) { ?>
                                        <tr>
                                            <th>Paiement Complet</th>
                                            <td><?php echo htmlspecialchars($row3["Complet"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nombre de versement effectué</th>
                                            <td><?php echo htmlspecialchars($row3["Nombre de versement effectué"]); ?></td>
                                        </tr>
                                        <?php if ($row3["Complet"] == 'Non') { ?>
                                            <tr>
                                                <th>Reste à payer</th>
                                                <td><?php echo htmlspecialchars($row3["Reste à payer"]); ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th>Date du dernier paiement</th>
                                            <td><?php echo htmlspecialchars(date_format(date_create($row3["Date du dernier paiement"]), 'd/m/Y')); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Moyen de paiement</th>
                                            <td><?php echo htmlspecialchars($row3["Moyen de paiement"]); ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo "Aucun résultat trouvé pour ce licencié.";
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
            
            // Fermer la connexion PDO
            $pdo = null;
        }
        ?>
    </div>
</body>
</html>
