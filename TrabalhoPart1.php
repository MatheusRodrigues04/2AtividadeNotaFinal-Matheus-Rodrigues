<?php
function conectarBanco()
{
    $db = new SQLite3('livros.db');
    $db->exec("CREATE TABLE IF NOT EXISTS livros (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        autor TEXT NOT NULL,
        ano INTEGER NOT NULL
    )");
    return $db;
}
$db = conectarBanco();
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST['acao'])) {
        if ($_POST['acao'] === 'adicionar') {
            $titulo = $_POST['titulo'];
            $autor = $_POST['autor'];
            $ano = intval($_POST['ano']);
            $stmt = $db->prepare("INSERT INTO livros (titulo, autor, ano) VALUES (:titulo, :autor, :ano)");
            $stmt->bindValue(':titulo', $titulo, SQLITE3_TEXT);
            $stmt->bindValue(':autor', $autor, SQLITE3_TEXT);
            $stmt->bindValue(':ano', $ano, SQLITE3_INTEGER);
            $stmt->execute();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($_POST['acao'] === 'excluir')
        {
        $id = intval($_POST['id']);
        $stmt = $db->prepare("DELETE FROM livros WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
        }
    }
}
$resultado = $db->query("SELECT * FROM livros");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de livros</title>
</head>
<body>
    <h1>Gerenciador de livros</h1>
    <h2>Adicionar livro</h2>
    <form action="" method="POST">
        <input type="hidden" name="acao" value="adicionar">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" required><br>
        <label for="autor">Autor:</label>
        <input type="text" name="autor" id="autor" required><br>
        <label for="ano">Ano da publicação:</label>
        <input type="number" name="ano" id="ano" required><br>
        <button type="submit">Adicionar</button>
    </form>
    <h2>Lista de livros</h2>
    <ul>
        <?php while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)): ?>
            <li>
                <strong><?= htmlspecialchars($linha['titulo']) ?></strong> - 
                <?= htmlspecialchars($linha['autor']) ?> (<?= htmlspecialchars($linha['ano']) ?>)
                <form action="" method="POST" style="display:inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?= $linha['id'] ?>">
                    <button type="submit">Excluir</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
