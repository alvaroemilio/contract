<?php
include '../conexao.php';

// Busca clientes
$stmt = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC");

$clientes = $stmt->fetchAll();

// Busca serviços
$stmt = $pdo->query("SELECT id, nome, preco FROM servicos");
$servicos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Criar Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .remove-btn { cursor: pointer; color: red; font-weight: bold; }
    </style>
</head>
<body class="container mt-5">
    <h2>Novo Contrato</h2>
    <form method="post" action="salvar.php">
        <div class="mb-3">
            <label>Cliente:</label>
            <select name="cliente_id" class="form-select" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= $c['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <h4>Serviços</h4>
        <table class="table" id="tabela-servicos">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="servicos-body">
                <!-- Linhas serão adicionadas via JS -->
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary mb-3" onclick="adicionarServico()">Adicionar Serviço</button>

        <div class="mb-3">
            <label>Total Geral:</label>
            <input type="text" id="total-geral" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Contrato</button>
        <a href="../index.php" class="btn btn-secondary">Cancelar</a>
    </form>

    <!-- Serviços disponíveis para JavaScript -->
    <script>
        const servicosDisponiveis = <?= json_encode($servicos) ?>;
    </script>

    <script>
        function adicionarServico() {
            const tbody = document.getElementById('servicos-body');
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>
                    <select name="servico_id[]" class="form-select servico-select" onchange="atualizarPreco(this)">
                        <option value="">Selecione</option>
                        ${servicosDisponiveis.map(s => `<option value="${s.id}" data-preco="${s.preco}">${s.nome}</option>`).join('')}
                    </select>
                </td>
                <td><input type="number" name="quantidade[]" class="form-control" min="1" value="1" oninput="calcularLinha(this)"></td>
                <td><input type="text" name="preco[]" class="form-control preco" readonly></td>
                <td><input type="text" name="total[]" class="form-control total" readonly></td>
                <td><span class="remove-btn" onclick="removerLinha(this)">×</span></td>
            `;

            tbody.appendChild(row);
        }

        function atualizarPreco(select) {
            const preco = select.selectedOptions[0].getAttribute('data-preco') || 0;
            const row = select.closest('tr');
            row.querySelector('.preco').value = parseFloat(preco).toFixed(2);
            calcularLinha(select);
        }

        function calcularLinha(el) {
            const row = el.closest('tr');
            const quantidade = parseFloat(row.querySelector('[name="quantidade[]"]').value || 0);
            const preco = parseFloat(row.querySelector('[name="preco[]"]').value || 0);
            const total = quantidade * preco;

            row.querySelector('.total').value = total.toFixed(2);
            calcularTotalGeral();
        }

        function calcularTotalGeral() {
            let total = 0;
            document.querySelectorAll('.total').forEach(input => {
                total += parseFloat(input.value || 0);
            });
            document.getElementById('total-geral').value = total.toFixed(2);
        }

        function removerLinha(el) {
            el.closest('tr').remove();
            calcularTotalGeral();
        }
    </script>
</body>
</html>
