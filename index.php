<?php
session_start();
include 'db.php';

// Adicionar novo usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];

    $sql = "INSERT INTO users (nome, idade, genero) VALUES ('$nome', $idade, '$genero')";
    $conn->query($sql);
    $_SESSION['message'] = "Usuário adicionado com sucesso!";
    header("Location: index.php");
    exit();
}

// Editar usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];

    $sql = "UPDATE users SET nome='$nome', idade=$idade, genero='$genero' WHERE id=$id";
    $conn->query($sql);
    $_SESSION['message'] = "Usuário editado com sucesso!";
    header("Location: index.php");
    exit();
}

// Excluir usuário
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id=$id";
    $conn->query($sql);
    $_SESSION['message-error'] = "Usuário excluído com sucesso!";
    header("Location: index.php");
    exit();
}


$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM users WHERE nome LIKE '%$searchTerm%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Simples</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }

        .form-wrapper {
            display: flex;
            justify-content: space-around;

        }

        table {
            margin: auto;
            margin-top: 6rem;

            width: 900px;
            height: 200px;
        }

        table tr th, td {
            text-align: center;
        }

    </style>
</head>
<body>
    <h1>Cadastro de Usuários</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); 
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['message-error'])): ?>
        <div style="background-color: #D95555; color: white; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php 
                echo $_SESSION['message-error']; 
                unset($_SESSION['message-error']); 
            ?>
        </div>
    <?php endif; ?>



    <div class="form-wrapper">
        <form method="POST" id="userForm">
            <input type="hidden" name="id" id="userId">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="number" name="idade" placeholder="Idade" required>
            <select name="genero" required>
                <option value="">Selecione Gênero</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select>
            <button type="submit" name="action" value="add">Add User</button>
        </form>

    
        <form method="GET" id="searchForm">
            <input type="text" name="search" placeholder="Pesquisar por nome" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Pesquisar</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Nome</th>
            <th>Idade</th>
            <th>Gênero</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                <td><?php echo htmlspecialchars($row['idade']); ?></td>
                <td><?php echo htmlspecialchars($row['genero']); ?></td>
                <td>
                    <button onclick="editUser(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nome']); ?>', <?php echo $row['idade']; ?>, '<?php echo $row['genero']; ?>')">Editar</button>
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        function editUser(id, nome, idade, genero) {
            document.getElementById('userId').value = id;
            document.getElementsByName('nome')[0].value = nome;
            document.getElementsByName('idade')[0].value = idade;
            document.getElementsByName('genero')[0].value = genero;
            document.querySelector('button[type="submit"]').value = 'edit'; // Muda o valor do botão
            document.querySelector('button[type="submit"]').name = 'action'; // Define o nome do botão como "action"
        }
    </script>
</body>
</html>
