<?php
require('fpdf/fpdf.php');
include '../conexao.php';

// Função para converter número para extenso em reais
function valorPorExtenso($valor = 0) {
    $valor = number_format($valor, 2, '.', '');
    list($inteiro, $centavos) = explode('.', $valor);

    $extenso = extensoParteInteira($inteiro);
    if ((int)$centavos > 0) {
        $extenso .= ' e ' . extensoParteInteira($centavos, true);
    }

    return $extenso;
}

function extensoParteInteira($numero, $centavos = false) {
    $singular = $centavos ? ["centavo"] : ["real", "mil", "milhão", "bilhão", "trilhão"];
    $plural = $centavos ? ["centavos"] : ["reais", "mil", "milhões", "bilhões", "trilhões"];

    $c = [
        0 => "", 1 => "um", 2 => "dois", 3 => "três", 4 => "quatro",
        5 => "cinco", 6 => "seis", 7 => "sete", 8 => "oito", 9 => "nove",
        10 => "dez", 11 => "onze", 12 => "doze", 13 => "treze", 14 => "quatorze",
        15 => "quinze", 16 => "dezesseis", 17 => "dezessete", 18 => "dezoito", 19 => "dezenove",
        20 => "vinte", 30 => "trinta", 40 => "quarenta", 50 => "cinquenta",
        60 => "sessenta", 70 => "setenta", 80 => "oitenta", 90 => "noventa",
        100 => "cem", 200 => "duzentos", 300 => "trezentos", 400 => "quatrocentos",
        500 => "quinhentos", 600 => "seiscentos", 700 => "setecentos",
        800 => "oitocentos", 900 => "novecentos"
    ];

    if ($numero == 0) return "zero " . $plural[0];

    $z = 0;
    $numero = str_pad($numero, ceil(strlen($numero) / 3) * 3, "0", STR_PAD_LEFT);
    $chunks = str_split($numero, 3);
    $ret = [];

    foreach ($chunks as $i => $chunk) {
        $n = (int)$chunk;

        if ($n == 0) continue;

        $hundreds = (int)($chunk[0]) * 100;
        $tens = (int)($chunk[1]) * 10;
        $units = (int)($chunk[2]);

        $parte = '';

        if ($n == 100) {
            $parte = "cem";
        } else {
            if ($hundreds > 0) $parte .= $c[$hundreds];
            if ($tens + $units > 0) {
                if ($parte) $parte .= " e ";
                if ($tens + $units < 20) {
                    $parte .= $c[$tens + $units];
                } else {
                    $parte .= $c[$tens];
                    if ($units > 0) $parte .= " e " . $c[$units];
                }
            }
        }

        $pos = count($chunks) - $i - 1;
        $parte .= " " . ($n > 1 ? $plural[$pos] : $singular[$pos]);
        $ret[] = $parte;
    }

    return implode(" e ", $ret);
}


// Obter ID do contrato
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Contrato não informado.";
    exit();
}

// Buscar contrato + cliente
$stmt = $pdo->prepare("
    SELECT c.*, cl.nome AS cliente_nome, cl.email, cl.telefone, cl.endereco, cl.cnpj 
    FROM contratos c
    JOIN clientes cl ON cl.id = c.cliente_id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$contrato = $stmt->fetch();

if (!$contrato) {
    echo "Contrato inválido.";
    exit();
}

// Buscar itens do contrato
$stmt = $pdo->prepare("
    SELECT ic.*, s.nome AS servico_nome, s.descricao AS servico_descricao
    FROM itens_contrato ic
    JOIN servicos s ON s.id = ic.servico_id
    WHERE ic.contrato_id = ?
");
$stmt->execute([$id]);
$itens = $stmt->fetchAll();

// Criar PDF
$pdf = new FPDF();
$pdf->AddPage();

// Fonte padrão reduzida
$pdf->SetFont('Arial', '', 9);

// Cabeçalho
$pdf->Cell(0, 6, utf8_decode("Contrato #{$contrato['id']}"), 0, 1);
//$pdf->Cell(0, 6, utf8_decode("Data: " . date('d/m/Y H:i', strtotime($contrato['data_criacao']))), 0, 1);

//Adicionar Logo
//$pdf->Image('', 160, 10, 30);

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, utf8_decode("Fornecedor/ Locador"), 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, utf8_decode("Nome: "), 0, 1);



$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, utf8_decode("Cliente / Locatário"), 0, 1);

$pdf->SetFont('Arial', '', 8);

$pdf->SetFont('Arial', '', 8); // ou qualquer tamanho de fonte

$pdf->Cell(50, 5, utf8_decode("Nome: {$contrato['cliente_nome']}"), 0, 0);
$pdf->Cell(50, 5, utf8_decode("CNPJ: {$contrato['cnpj']}"), 0, 0);
$pdf->Cell(60, 5, utf8_decode("Email: {$contrato['email']}"), 0, 0);
$pdf->Cell(40, 5, utf8_decode("Telefone: {$contrato['telefone']}"), 0, 1);

// Endereço em uma nova linha (pode usar Cell se for curto)
$pdf->Cell(0, 5, utf8_decode("Endereço: {$contrato['endereco']}"), 0, 1);

$pdf->MultiCell(0, 4, utf8_decode($texto_contrato1));

// Tabela de serviços
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, utf8_decode("Serviços"), 0, 1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(40, 6, utf8_decode('Serviço'), 1);
$pdf->Cell(50, 6, utf8_decode('Descrição'), 1);
$pdf->Cell(15, 6, 'Qtd', 1, 0, 'C');
$pdf->Cell(25, 6, utf8_decode('Preço Unit.'), 1);
$pdf->Cell(25, 6, 'Total', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
$total_geral = 0;

foreach ($itens as $item) {
    $total = $item['quantidade'] * $item['preco_unitario'];
    $total_geral += $total;

    $y_inicial = $pdf->GetY();
    $x = $pdf->GetX();

    $pdf->MultiCell(40, 4, utf8_decode($item['servico_nome']), 1);
    $y_pos_nome = $pdf->GetY();

    $pdf->SetXY($x + 40, $y_inicial);
    $pdf->MultiCell(50, 4, utf8_decode($item['servico_descricao']), 1);
    $y_pos_desc = $pdf->GetY();

    $altura = max($y_pos_nome, $y_pos_desc) - $y_inicial;

    $pdf->SetXY($x + 90, $y_inicial);
    $pdf->Cell(15, $altura, $item['quantidade'], 1, 0, 'C');
    $pdf->Cell(25, $altura, "R$ " . number_format($item['preco_unitario'], 2, ',', '.'), 1);
    $pdf->Cell(25, $altura, "R$ " . number_format($total, 2, ',', '.'), 1);
    $pdf->Ln();
}

// Total
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(130, 6, utf8_decode('Total Geral'), 1);
$pdf->Cell(25, 6, "R$ " . number_format($total_geral, 2, ',', '.'), 1);
$pdf->Ln(4);

$pdf->Ln(2);
$texto_contrato2 = " text";
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(0, 4, utf8_decode($texto_contrato2));

$pdf->Cell(0, 6, utf8_decode("Local e Data  : " . date('d/m/Y H:i', strtotime($contrato['data_criacao']))), 0, 1);

// Assinaturas
$pdf->Ln(10);
$pdf->Cell(80, 8, utf8_decode("______________________________"), 0, 0, 'C');
$pdf->Cell(30, 8, '', 0, 0);
$pdf->Cell(80, 8, utf8_decode("______________________________"), 0, 1, 'C');

$pdf->Cell(80, 5, utf8_decode("Assinatura do Locador"), 0, 0, 'C');
$pdf->Cell(30, 5, '', 0, 0);
$pdf->Cell(80, 5, utf8_decode("Assinatura do Locatário"), 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 7);
$pdf->Cell(0, 5, utf8_decode('Info'), 0, 1, 'C');



// Gerar PDF como string
$pdf_content = $pdf->Output('S');

// Atualiza o contrato com o PDF gerado (blob)
$stmt = $pdo->prepare("UPDATE contratos SET pdf_gerado = ? WHERE id = ?");
$stmt->bindParam(1, $pdf_content, PDO::PARAM_LOB);
$stmt->bindParam(2, $id, PDO::PARAM_INT);
$stmt->execute();

// Exibir PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="contrato_' . $id . '.pdf"');
echo $pdf_content;
exit;
