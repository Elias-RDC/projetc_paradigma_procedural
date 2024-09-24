<?php
session_start();


$PHP_with_hints = array(
    "elefante" => "É o maior mascote do PHP.",
    "procedimentos" => "Conjunto de etapas para realizar uma tarefa.",
    "subrotina" =>  "Função ou bloco de código reutilizável.",
    "sequencial" => "Executado em uma ordem linear, passo a passo.",
    "script" =>  "Código simples que automatiza tarefas.",
    "servidor" => "Computador que fornece serviços a outros.",
    "array" => "Estrutura que armazena múltiplos valores.",
    "include" => "Instrução para incluir outro arquivo no código.",
    "interpretação" => "Processo de traduzir código para execução.",
    "fluxograma" => "Diagrama que representa o fluxo de um processo."
);

if (!isset($_SESSION['word'])) {
    // Escolher aleatoriamente um nome e a dica
    $random_PHP = array_rand($PHP_with_hints);
    $_SESSION['word'] = $random_PHP;
    $_SESSION['hint'] = $PHP_with_hints[$random_PHP];
    $_SESSION['guesses'] = [];
    $_SESSION['attempts'] = 6; // 6 tentativas
}

// Função para exibir a palavra com as letras adivinhadas
function displayWord($word, $guesses) {
    $display = '';
    for ($i = 0; $i < strlen($word); $i++) {
        if (in_array($word[$i], $guesses)) {
            $display .= $word[$i] . ' ';
        } else {
            $display .= '_ ';
        }
    }
    return $display;
}

// Função para exibir a estrutura da forca (que permanece inalterada) e o boneco (que vai sendo construído)
function displayHangman($attempts) {
    // Estrutura da forca (fixa)
    $forca = "
       ------
       |    |
       |    
       |    
       |    
       |    
    --------
    ";

    // Partes do boneco que surgem conforme o número de tentativas diminui
    $boneco = ['', 'O', '|', '/', '\\', '/', '\\'];

    // Adicionando as partes do boneco conforme o número de tentativas restantes
    $parte1 = ($attempts <= 5) ? $boneco[1] : ' '; // Cabeça
    $parte2 = ($attempts <= 4) ? $boneco[2] : ' '; // Tronco
    $parte3 = ($attempts <= 3) ? $boneco[3] : ' '; // Braço esquerdo
    $parte4 = ($attempts <= 2) ? $boneco[4] : ' '; // Braço direito
    $parte5 = ($attempts <= 1) ? $boneco[5] : ' '; // Perna esquerda
    $parte6 = ($attempts <= 0) ? $boneco[6] : ' '; // Perna direita

    // Montagem do boneco com a forca fixa
    $resultado = "
       ------
       |    |
       |    $parte1
        |   $parte3$parte2$parte4
        |   $parte5 $parte6
      |    
    --------
    ";

    return $resultado;
}

// Processa o palpite
if (isset($_POST['guess'])) {
    $guess = strtolower($_POST['guess']);

    if (!in_array($guess, $_SESSION['guesses'])) {
        $_SESSION['guesses'][] = $guess;

        if (strpos($_SESSION['word'], $guess) === false) {
            $_SESSION['attempts']--;
        }
    }
}

// Verifica se o jogo acabou
$wordCompleted = true;
for ($i = 0; $i < strlen($_SESSION['word']); $i++) {
    if (!in_array($_SESSION['word'][$i], $_SESSION['guesses'])) {
        $wordCompleted = false;
        break;
    }
}

// Reiniciar o jogo
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jogo da Forca - Animais</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        .hangman {
            font-family: monospace;
            margin-bottom: 20px;
        }
        .word {
            font-size: 24px;
            letter-spacing: 5px;
        }
        .guesses {
            margin-top: 10px;
        }
        .guess-form input {
            font-size: 18px;
            padding: 5px;
            margin-right: 10px;
        }
        .guess-form button {
            padding: 5px 10px;
            font-size: 18px;
            cursor: pointer;
        }
        .reset-form button {
            padding: 5px 15px;
            font-size: 18px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border: none;
        }
        .hint {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Jogo da Forca</h1>

    <div class="hint">
        <strong>Dica:</strong> <?php echo $_SESSION['hint']; ?>
    </div>

    <div class="hangman">
        <pre><?php echo displayHangman($_SESSION['attempts']); ?></pre>
    </div>

    <p><strong>Palavra:</strong></p>
    <div class="word">
        <?php echo displayWord($_SESSION['word'], $_SESSION['guesses']); ?>
    </div>

    <p><strong>Tentativas restantes:</strong> <?php echo $_SESSION['attempts']; ?></p>

    <?php if ($_SESSION['attempts'] > 0 && !$wordCompleted): ?>
        <form class="guess-form" method="POST">
            <label for="guess">Digite uma letra:</label>
            <input type="text" name="guess" maxlength="1" required>
            <button type="submit">Enviar</button>
        </form>
    <?php elseif ($wordCompleted): ?>
        <p><strong>Parabéns! Você adivinhou <?php echo $_SESSION['word']; ?></strong></p>
    <?php else: ?>
        <p><strong>Você perdeu! A palavra é: <?php echo $_SESSION['word']; ?></strong></p>
    <?php endif; ?>

    <form class="reset-form" method="POST">
        <button type="submit" name="reset">Reiniciar Jogo</button>
    </form>
</body>
</html>
