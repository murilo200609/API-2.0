<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = 'mysql';
$user = 'thermometer';
$pass = '190906';
$db = 'my_thermometer';

// Conectar ao MySQL
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Erro de conexão: ' . $conn->connect_error]));
}

// Criar tabela se não existir
$createTable = "CREATE TABLE IF NOT EXISTS temperature_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature FLOAT NOT NULL,
    reading_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createTable)) {
    http_response_code(500);
    die(json_encode(['error' => 'Erro ao criar tabela: ' . $conn->error]));
}

// Rotas da API
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

switch ($request_method) {
    case 'GET':
        if (strpos($request_uri, '/api/temperature/latest') !== false) {
            // Obter última leitura
            $result = $conn->query("SELECT * FROM temperature_readings ORDER BY reading_time DESC LIMIT 1");
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Nenhuma leitura encontrada']);
            }
        } elseif (strpos($request_uri, '/api/temperature/history') !== false) {
            // Obter histórico
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $result = $conn->query("SELECT * FROM temperature_readings ORDER BY reading_time DESC LIMIT $limit");
            $readings = [];
            while ($row = $result->fetch_assoc()) {
                $readings[] = $row;
            }
            echo json_encode($readings);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint não encontrado']);
        }
        break;
        
    case 'POST':
        if (strpos($request_uri, '/api/temperature') !== false) {
            // Adicionar nova leitura
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['temperature'])) {
                $temperature = floatval($data['temperature']);
                $stmt = $conn->prepare("INSERT INTO temperature_readings (temperature) VALUES (?)");
                $stmt->bind_param("d", $temperature);
                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'Leitura registrada com sucesso',
                        'id' => $stmt->insert_id
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao registrar leitura']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Campo "temperature" é obrigatório']);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}

$conn->close();
?>