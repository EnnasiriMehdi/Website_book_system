<?php
session_start();

foreach (glob("utils/*.php") as $filename)
{
    require  $filename ;
}



$connect = connectDb();



// Création de la class PDF
class PDF extends FPDF {
    // Header
    function Header() {

        //$this->Image('logo_agence.png',8,2);
        // Saut de ligne 20 mm
        $this->Ln(20);

        // Titre gras (B) police Helbetica de 11
        $this->SetFont('Helvetica','B',11);
        // fond de couleur gris (valeurs en RGB)
        $this->setFillColor(89,133,255);
        // position du coin supérieur gauche par rapport à la marge gauche (mm)
        $this->SetX(70);
        // Texte : 60 >largeur ligne, 8 >hauteur ligne. Premier 0 >pas de bordure, 1 >retour à la ligneensuite, C >centrer texte, 1> couleur de fond ok
        $this->Cell(60,8,'AEN',0,1,'C',1);
        // Saut de ligne 10 mm
        $this->Ln(20);
    }
    // Footer
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Helvetica','I',9);
        // Numéro de page, centré (C)
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}


if(isset($_GET['id']) && !empty($_GET['id'])){


    $get_line = $connect->prepare("SELECT * FROM invoice_line WHERE invoice = :id");
    $get_line->execute([ ":id" => $_GET['id']]);

    $get_line = $get_line->fetchAll();

    $get_invoice = $connect->prepare("SELECT * FROM invoice WHERE id = :id");
    $get_invoice->execute([ ":id" => $_GET['id']]);

    $get_invoice = $get_invoice->fetchAll();




}else
{
    header('Location: history.php');
}
$pdf = new PDF('P','mm','A4');

// Nouvelle page A4 (incluant ici logo, titre et pied de page)
$pdf->AddPage();
// Polices par défaut : Helvetica taille 9
$pdf->SetFont('Helvetica','',9);
// Couleur par défaut : noir
$pdf->SetTextColor(0);
// Compteur de pages {nb}
$pdf->AliasNbPages();

$pdf->SetFont('Helvetica','B',11);
// couleur de fond de la cellule : gris clair
$pdf->setFillColor(89,133,255);
// Cellule avec les données du sous-titre sur 2 lignes, pas de bordure mais couleur de fond grise
$pdf->Cell(75,6,'Facture '. $get_invoice[0]["id"],0,1,'L',1);
//$pdf->Cell(75,6,strtoupper(utf8_decode($data_voyageur['prenom'].' '.$data_voyageur['nom'])),0,1,'L',1);
$pdf->Cell(75,6,$_SESSION["lastname"] . ' '. $_SESSION["firstname"] ,0,1,'L',1);
$pdf->Cell(75,6,'Date :  '. $get_invoice[0]["date"],0,1,'L',1);
$pdf->Ln(10); // saut de ligne 10mm


function entete_table($position_entete) {
    global $pdf;
    $pdf->SetDrawColor(183); // Couleur du fond RVB
    $pdf->setFillColor(89,133,255); // Couleur des filets RVB
    $pdf->SetTextColor(0); // Couleur du texte noir
    $pdf->SetY($position_entete);
    // position de colonne 1 (10mm à gauche)
    $pdf->SetX(5);
    $pdf->Cell(40,8,'Prestation',1,0,'C',1);  // 60 >largeur colonne, 8 >hauteur colonne
    // position de la colonne 2 (70 = 10+60)
    $pdf->SetX(45);
    $pdf->Cell(40,8,'Date',1,0,'C',1);
    $pdf->SetX(85);
    $pdf->Cell(40,8,'Prix HT',1,0,'C',1);
    $pdf->SetX(125);
    $pdf->Cell(40,8,'Tva',1,0,'C',1);
    $pdf->SetX(165);
    $pdf->Cell(40,8,iconv("UTF-8", "CP1250//TRANSLIT", "Quantité/Durée"),1,0,'C',1);

    // position de la colonne 3 (130 = 70+60)


    $pdf->Ln(); // Retour à la ligne
}
// AFFICHAGE EN-TÊTE DU TABLEAU
// Position ordonnée de l'entête en valeur absolue par rapport au sommet de la page (70 mm)
$position_entete = 100;
// police des caractères
$pdf->SetFont('Helvetica','',9);
$pdf->SetTextColor(0);

entete_table($position_entete);
for ($i = 0; $i<count($get_line); $i++) {
    $position_detail = 108 + 10*$i;
    $pdf->SetY($position_detail);
    $pdf->SetX(5);
    $pdf->MultiCell(40, 8, utf8_decode($get_line[$i]['name']), 1, 'C');
// position abcisse de la colonne 2 (70 = 10 + 60)
    $pdf->SetY($position_detail);
    $pdf->SetX(45);
    $pdf->MultiCell(40, 8, utf8_decode($get_line[$i]['start_date']), 1, 'C');
// position abcisse de la colonne 3 (130 = 70+ 60)
    $pdf->SetY($position_detail);
    $pdf->SetX(85);
    $pdf->MultiCell(40, 8, utf8_decode($get_line[$i]['price_DF']), 1, 'C');

    $pdf->SetY($position_detail);
    $pdf->SetX(125);
    $pdf->MultiCell(40, 8, utf8_decode($get_line[$i]['VAT']), 1, 'C');

    $pdf->SetY($position_detail);
    $pdf->SetX(165);
    $pdf->MultiCell(40, 8, utf8_decode($get_line[$i]['quantity']), 1, 'C');
}



$pdf->Ln();


$pdf->Cell(60,8,'TOTAL',1,0,'C',1);
$pdf->SetX(70);
$pdf->Cell(60,8, $get_invoice[0]["amount"] .  iconv("UTF-8", "CP1250//TRANSLIT", "€") ,1,0,'C',0);

$pdf->Output('test.pdf','I');

