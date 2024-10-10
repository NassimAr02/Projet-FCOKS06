<?php
require_once 'vendor/autoload.php'; // Charger DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

// Créer une instance de DomPDF avec les options
$options = new Options();
$options->set('isHtml5ParserEnabled', true); // Activer le support HTML5
$options->set('isPhpEnabled', true); // Activer l'exécution PHP dans DomPDF (pour l'inclusion dynamique)
$dompdf = new Dompdf($options);

// Charger le contenu HTML à partir d'un fichier ou d'une chaîne
ob_start(); // Démarrer la capture de sortie
include 'pageàimprimer.php'; // Inclure le contenu HTML dynamique
$html = ob_get_clean(); // Récupérer et nettoyer la capture de sortie

// Charger le contenu HTML dans DomPDF
$dompdf->loadHtml($html);

// Rendre le PDF
$dompdf->render();

// Afficher l'aperçu du PDF dans le navigateur
$dompdf->stream('informations_licencie.pdf', array('Attachment' => false));
?>
