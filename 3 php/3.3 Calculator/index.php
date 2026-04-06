<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .calculator {
            background: white;
            padding: 20px;
        }

        input,
        select,
        button {
            font-size: 16px;
            padding: 8px;
            margin: 5px;
        }

        input {
            width: 150px;
        }

        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #e8f4f8;
            font-size: 18px;
            font-weight: bold;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 8px 20px;
        }
    </style>
</head>

<body>
    <div class="calculator">
        <h2>Простейший калькулятор</h2>

        <form method="post" action="">
            <input type="text" name="num1" placeholder="Первое число"
                value="<?php echo isset($_POST['num1']) ? htmlspecialchars($_POST['num1']) : ''; ?>" required>

            <select name="operation">
                <option value="+" <?php echo (isset($_POST['operation']) && $_POST['operation'] == '+') ? 'selected' : ''; ?>>+</option>
                <option value="-" <?php echo (isset($_POST['operation']) && $_POST['operation'] == '-') ? 'selected' : ''; ?>>-</option>
                <option value="*" <?php echo (isset($_POST['operation']) && $_POST['operation'] == '*') ? 'selected' : ''; ?>>*</option>
                <option value="/" <?php echo (isset($_POST['operation']) && $_POST['operation'] == '/') ? 'selected' : ''; ?>>/</option>
            </select>

            <input type="text" name="num2" placeholder="Второе число"
                value="<?php echo isset($_POST['num2']) ? htmlspecialchars($_POST['num2']) : ''; ?>" required>

            <button type="submit" name="calculate">Вычислить</button>
        </form>

        <?php
        if (isset($_POST['calculate'])) {
            $num1 = $_POST['num1'];
            $num2 = $_POST['num2'];
            $operation = $_POST['operation'];
            $result = null;
            $error = null;

            if ($num1 === "" || $num2 === "") {
                $error = "Пожалуйста, заполните оба поля!";
            } else {
                if (!is_numeric($num1) || !is_numeric($num2)) {
                    $error = "Пожалуйста, введите корректные числа!";
                } else {
                    $num1 = floatval($num1);
                    $num2 = floatval($num2);

                    switch ($operation) {
                        case '+':
                            $result = $num1 + $num2;
                            break;
                        case '-':
                            $result = $num1 - $num2;
                            break;
                        case '*':
                            $result = $num1 * $num2;
                            break;
                        case '/':
                            if ($num2 == 0) {
                                $error = "Ошибка: Деление на ноль невозможно!";
                            } else {
                                $result = $num1 / $num2;
                            }
                            break;
                        default:
                            $error = "Неизвестная операция!";
                    }
                }
            }

            if ($error) {
                echo "<div class='error'>$error</div>";
            } elseif ($result !== null) {
                echo "<div class='result'>";
                echo "Результат: " . number_format($result, 4, '.', '');
                echo "</div>";
            }
        }
        ?>
    </div>
</body>

</html>