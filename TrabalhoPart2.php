<?php
$db = new PDO('sqlite:tarefas.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec
("
CREATE TABLE IF NOT EXISTS tarefas 
(
id INTEGER PRIMARY KEY AUTOINCREMENT,
descricao TEXT NOT NULL,
data_vencimento DATE,
concluida INTEGER DEFAULT 0
)
");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_tarefa'])) 
{
$descricao = htmlspecialchars($_POST['descricao']);
$data_vencimento = htmlspecialchars($_POST['data_vencimento']);
$stmt = $db->prepare("INSERT INTO tarefas (descricao, data_vencimento) VALUES (:descricao, :data_vencimento)");
$stmt->execute([':descricao' => $descricao, ':data_vencimento' => $data_vencimento]);
header("Location: " . $_SERVER['PHP_SELF']);
exit();
}
if (isset($_GET['concluir'])) 
{
$id = (int) $_GET['concluir'];
$stmt = $db->prepare("UPDATE tarefas SET concluida = 1 WHERE id = :id");
$stmt->execute([':id' => $id]);
header("Location: " . $_SERVER['PHP_SELF']);
exit();
}
if (isset($_GET['excluir'])) 
{
$id = (int) $_GET['excluir'];
$stmt = $db->prepare("DELETE FROM tarefas WHERE id = :id");
$stmt->execute([':id' => $id]);
header("Location: " . $_SERVER['PHP_SELF']);
exit();
}
$stmt = $db->query("SELECT * FROM tarefas ORDER BY concluida, data_vencimento");
$tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de tarefas</title>
</head>
<body>
    <h1>Gerenciador de tarefas</h1>
    <h2>Adicionar tarefa/serviço</h2>
    <form method="POST">
        <input type="text" name="descricao" placeholder="Descrição da tarefa" required>
        <input type="date" name="data_vencimento" required>
        <button type="submit" name="adicionar_tarefa">Adicionar</button>
    </form>
    <h2>Tarefas</h2>
    <h3>Não concluídas</h3>
    <ul>
        <?php foreach ($tarefas as $tarefa): ?>
            <?php if (!$tarefa['concluida']): ?>
                <li>
                    <?= htmlspecialchars($tarefa['descricao']) ?> - <?= htmlspecialchars($tarefa['data_vencimento']) ?>
                    <a href="?concluir=<?= $tarefa['id'] ?>">Concluir</a>
                    <a href="?excluir=<?= $tarefa['id'] ?>">Excluir</a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <h3>Concluídas</h3>
    <ul>
        <?php foreach ($tarefas as $tarefa): ?>
            <?php if ($tarefa['concluida']): ?>
                <li>
                    <?= htmlspecialchars($tarefa['descricao']) ?> - <?= htmlspecialchars($tarefa['data_vencimento']) ?>
                    <a href="?excluir=<?= $tarefa['id'] ?>">Excluir</a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</body>
</html>