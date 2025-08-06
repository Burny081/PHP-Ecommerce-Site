<?php
session_start();
require_once 'config.php';
if (!isset($pdo)) {
    $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', 'Nestor667');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit;
}

// FPDF library
require_once('fpdf/fpdf.php');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.offer FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Order Receipt',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,10,'Date: '.date('Y-m-d H:i'),0,1);
$pdf->Ln(3);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(70,10,'Product',1);
$pdf->Cell(30,10,'Price',1);
$pdf->Cell(30,10,'Offer',1);
$pdf->Cell(20,10,'Qty',1);
$pdf->Cell(30,10,'Total',1);
$pdf->Ln();
$pdf->SetFont('Arial','',12);
$total = 0;
foreach ($cart_items as $item) {
    $line_total = $item['offer'] * $item['quantity'];
    $pdf->Cell(70,10,iconv('UTF-8','windows-1252',$item['title']),1);
    $pdf->Cell(30,10,$item['price'],1);
    $pdf->Cell(30,10,$item['offer'],1);
    $pdf->Cell(20,10,$item['quantity'],1);
    $pdf->Cell(30,10,$line_total,1);
    $pdf->Ln();
    $total += $line_total;
}
$pdf->SetFont('Arial','B',12);
$pdf->Cell(150,10,'Total',1);
$pdf->Cell(30,10,$total,1);
$pdf->Ln(15);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Thank you for your purchase!',0,1,'C');
$pdf->Output('I','order_receipt.pdf');
exit;
