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

require_once('fpdf/fpdf.php');

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if (!$order_id) {
    header("Location: order_history.php");
    exit;
}

// Fetch order and items
$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$order_stmt->execute([$order_id, $user_id]);
$order = $order_stmt->fetch();
if (!$order) {
    header("Location: order_history.php");
    exit;
}
$item_stmt = $pdo->prepare("SELECT oi.*, p.title FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$item_stmt->execute([$order_id]);
$order_items = $item_stmt->fetchAll();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Order Receipt',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,10,'Date: '.date('Y-m-d H:i', strtotime($order['created_at'])),0,1);
$pdf->Ln(3);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(70,10,'Product',1);
$pdf->Cell(30,10,'Price',1);
$pdf->Cell(20,10,'Qty',1);
$pdf->Cell(30,10,'Subtotal',1);
$pdf->Ln();
$pdf->SetFont('Arial','',12);
$total = 0;
foreach ($order_items as $item) {
    $line_total = $item['price'] * $item['quantity'];
    $pdf->Cell(70,10,iconv('UTF-8','windows-1252',$item['title']),1);
    $pdf->Cell(30,10,$item['price'],1);
    $pdf->Cell(20,10,$item['quantity'],1);
    $pdf->Cell(30,10,$line_total,1);
    $pdf->Ln();
    $total += $line_total;
}
$pdf->SetFont('Arial','B',12);
$pdf->Cell(120,10,'Total',1);
$pdf->Cell(30,10,$total,1);
$pdf->Ln(15);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Thank you for your purchase!',0,1,'C');
$pdf->Output('I','order_receipt.pdf');
exit;
