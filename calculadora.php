<?php
session_start();

if (!isset($_SESSION['historico'])) {
    $_SESSION['historico'] = [];
}

if (!isset($_SESSION['memoria'])) {
    $_SESSION['memoria'] = null;
}

function calcular($numero1, $numero2, $operacao) {
    $resultado = 0;
    $simboloOperacao = '';
    switch ($operacao) {
        case 'somar':
            $resultado = $numero1 + $numero2;
            $simboloOperacao = '+';
            break;
        case 'subtrair':
            $resultado = $numero1 - $numero2;
            $simboloOperacao = '-';
            break;
        case 'multiplicar':
            $resultado = $numero1 * $numero2;
            $simboloOperacao = '*';
            break;
        case 'dividir':
            if ($numero2 == 0) return "Erro: Divisão por zero!";
            $resultado = $numero1 / $numero2;
            $simboloOperacao = '/';
            break;
        case 'potencia':
            $resultado = pow($numero1, $numero2);
            $simboloOperacao = '^';
            break;
        case 'fatorial':
            $resultado = fatorial($numero1);
            $simboloOperacao = 'n!';
            break;
        default:
            return "Operação inválida!";
    }
    array_push($_SESSION['historico'], "$numero1 $simboloOperacao " . ($operacao !== 'fatorial' ? "$numero2" : "") . " = $resultado");
    return $resultado;
}

function fatorial($num) {
    if ($num <= 1) return 1;
    return $num * fatorial($num - 1);
}

if (isset($_POST['salvar'])) {
    $_SESSION['memoria'] = [
        'numero1' => $_POST['numero1'],
        'numero2' => $_POST['numero2'],
        'operacao' => $_POST['operacao']
    ];
}

if (isset($_POST['usar_memoria']) && $_SESSION['memoria']) {
    $_POST['numero1'] = $_SESSION['memoria']['numero1'];
    $_POST['numero2'] = $_SESSION['memoria']['numero2'];
    $_POST['operacao'] = $_SESSION['memoria']['operacao'];
}

if (isset($_POST['limpar_historico'])) {
    $_SESSION['historico'] = [];
}

$resultado = "";
if (isset($_POST['calcular'])) {
    $numero1 = floatval($_POST['numero1']);
    $numero2 = $_POST['operacao'] === 'fatorial' ? 0 : floatval($_POST['numero2']);
    $operacao = $_POST['operacao'];
    $resultado = calcular($numero1, $numero2, $operacao);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calculadora PHP</title>
    <style>
        @keyframes rgbBorderAnimation {
            0% {
                border-image: linear-gradient(360deg, #ff0000, #ffff00, #00ff00, #0000ff, #ff00ff, #ff0000) 1;
            }
            100% {
                border-image: linear-gradient(360deg, #ff00ff, #ff0000, #ffff00, #00ff00, #0000ff, #ff00ff) 1;
            }
        }

        body {
            margin: 0;
            padding: 0;
            background-color: black;
            color: white;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            border: 10px solid transparent;
            border-image-slice: 1;
            animation: rgbBorderAnimation 10s linear infinite;
        }

        .calculadora {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .entrada-numero, .dropdown-operacao, button {
            width: 200px;
            margin: 5px;
            padding: 10px;
            border-radius: 10px;
            border: none;
            background-color: #333;
            color: white;
        }

        button {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #555;
        }

        #resultado, #historico {
            margin-top: 15px;
            width: calc(4 * 210px);
            padding: 20px;
            border-radius: 10px;
            background-color: #444;
            text-align: center;
            box-sizing: border-box;
            position: relative;
            border: 5px solid transparent;
            animation: rgbBorderAnimation 10s linear infinite;
        }

        .linha-entrada, .linha-botoes {
            display: flex;
            justify-content: center;
        }

        .linha-botoes button {
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <div class="calculadora">
        <h1>Calculadora PHP</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="linha-entrada">
                <input type="text" name="numero1" class="entrada-numero" placeholder="Número 1" value="<?php echo $_POST['numero1'] ?? ''; ?>">
                <select name="operacao" class="dropdown-operacao">
                    <option value="somar" <?php echo (isset($_POST['operacao']) && $_POST['operacao'] == 'somar') ? 'selected' : ''; ?>>+</option>
                    <option value="subtrair" <?php echo (isset($_POST['operacao']) && $_POST['operacao'] == 'subtrair') ? 'selected' : ''; ?>>-</option>
                    <option value="multiplicar" <?php echo (isset($_POST['operacao']) && $_POST['operacao'] == 'multiplicar') ? 'selected' : ''; ?>>*</option>
                    <option value="dividir" <?php echo (isset($_POST['operacao']) && $_POST['operacao'] == 'dividir') ? 'selected' : ''; ?>>/</option>
                    <option value="potencia" <?php echo (isset($_POST['operacao']) && $_POST['operacao'] == 'potencia') ? 'selected' : ''; ?>>^</option>
                    <option value="fatorial" <?php echo (isset($_POST['operacao']) && $_POST['operacao'] == 'fatorial') ? 'selected' : ''; ?>>n!</option>
                </select>
                <input type="text" name="numero2" class="entrada-numero" placeholder="Número 2" value="<?php echo $_POST['numero2'] ?? ''; ?>" <?php echo ($_POST['operacao'] ?? '') === 'fatorial' ? 'disabled' : ''; ?>>
                <button type="submit" name="calcular">Calcular</button>
            </div>
            <div class="linha-botoes">
                <button type="submit" name="salvar">Salvar M</button>
                <button type="submit" name="usar_memoria">Usar M</button>
                <button type="submit" name="limpar_historico">Apagar Histórico</button>
            </div>
        </form>
        <div id="resultado"> <?php echo $resultado; ?></div>
        <div id="historico">
            <h2>Histórico de Operações:</h2>
            <?php
            foreach ($_SESSION['historico'] as $entrada) {
                echo $entrada . "<br>";
            }
            ?>
        </div>
    </div>
</body>
</html>
