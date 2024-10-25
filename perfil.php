<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}


// Conexão com o banco de dados
include 'conexao.php'; // Inclua seu arquivo de conexão aqui

// Verifique se a conexão foi criada
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Obtenha as informações do usuário
$email = $_SESSION['email'];
$query = "SELECT * FROM pessoa WHERE email = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o usuário foi encontrado
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        die("Usuário não encontrado.");
    }
} else {
    die("Erro ao preparar a consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuário - Frog Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #fafafa; /* Fundo suave */
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Para garantir que o footer fique na parte inferior */
        }

        header {
            background-color: #fff;
            padding: 15px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            width: 180px;
            height: auto;
        }

        /* Card centralizado */
        .profile-card {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px; /* Adiciona margem para o cabeçalho fixo */
        }

        .profile-card p {
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .edit-btn, .logout-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .edit-btn {
            background-color: #007bff; /* Cor do botão de editar */
            color: white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .logout-btn {
            background-color: #ff0000;
            color: white;
        }

        .logout-btn:hover {
            background-color: #e60000;
        }

        .menu-icon {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .menu-icon:hover .bar {
            background-color: #4CAF50;
        }

        .bar {
            height: 3px;
            width: 100%;
            background-color: #333;
            border-radius: 5px;
            transition: 0.3s;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.05);
            transition: 0.5s;
            z-index: 1001;
        }

        .sidebar.open {
            right: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 20px;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px 0;
            border-bottom: 1px solid #e1e1e1;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .sidebar ul li a:hover {
            color: #4CAF50;
        }

        .sidebar ul li a.logout {
            color: #ff0000;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1000;
        }

        .overlay.show {
            display: block;
        }

        footer {
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: auto; /* Faz com que o footer fique na parte inferior */
        }

        footer p {
            font-size: 0.9rem;
            color: black; /* Cor do texto do footer */
        }

        /* Estilo do ícone */
        .profile-icon {
            width: 30px; /* Ajuste o tamanho do ícone conforme necessário */
            height: auto;
            margin-right: 10px;
        }
    </style>
</head>

<body>

<header>
    <div class="logo">
        <img src="img/logo2.png" alt="Frog Tech Logo">
    </div>
    <div class="menu-icon" id="menuIcon">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
</header>

<div class="sidebar" id="sidebarMenu">
    <ul>
        <li><a href="loja.htm">Loja</a></li>
        <li><a href="carrinho.php">Carrinho de Compras</a></li>
        <li><a href="paginahome.php">Home</a></li>
        <li><a href="logout.php" class="logout">Sair</a></li>
    </ul>
</div>

<div class="overlay" id="overlay"></div>

<div class="profile-card">
    <h1>Perfil de Usuário</h1>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Nome:</strong> <?php echo htmlspecialchars($user['nome']); ?></p>
    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($user['telefone'] ?? 'Não disponível'); ?></p>
    <p><strong>CPF:</strong> <?php echo htmlspecialchars($user['cpf'] ?? 'Não disponível'); ?></p>

    <a href="alterar_informacoes.php" class="edit-btn">Alterar Informações</a>
    <a href="logout.php" class="logout-btn">Sair</a>
</div>

<footer>
    <p>&copy; 2024 Frog Tech. Todos os direitos reservados.</p>
</footer>

<script>
    const menuIcon = document.getElementById('menuIcon');
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('overlay');

    menuIcon.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });
</script>
</body>
</html>
