<?php
// ============================================================
// api.php - 社員データベース API
// MySQL からデータを取得し JSON で返す
//
// エンドポイント一覧:
//   GET api.php?type=companies
//   GET api.php?type=departments[&company_id=N]
//   GET api.php?type=employees[&company_id=N][&department_id=N]
// ============================================================

// ---------- ① レスポンスヘッダー設定 ----------
header('Content-Type: application/json; charset=utf-8');

// ---------- ② DB接続 ----------
require_once 'env.php';
require_once 'lib/Database.php';

use Lib\Database;

// ---------- ③ クエリパラメータ取得 ----------
$type          = $_GET['type']          ?? 'employees';
$company_id    = isset($_GET['company_id'])    ? (int)$_GET['company_id']    : null;
$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : null;

// ---------- ④ DBクエリ実行 ----------
try {
    $pdo = Database::getInstance();

    switch ($type) {

        // --- 会社一覧 ---
        case 'companies':
            $stmt = $pdo->query('SELECT * FROM companies ORDER BY id');
            $data = $stmt->fetchAll();
            break;

        // --- 部署一覧（会社でフィルタ可） ---
        case 'departments':
            if ($company_id) {
                $stmt = $pdo->prepare(
                    'SELECT d.*, c.name AS company_name
                     FROM departments d
                     JOIN companies c ON d.company_id = c.id
                     WHERE d.company_id = ?
                     ORDER BY d.id'
                );
                $stmt->execute([$company_id]);
            } else {
                $stmt = $pdo->query(
                    'SELECT d.*, c.name AS company_name
                     FROM departments d
                     JOIN companies c ON d.company_id = c.id
                     ORDER BY d.id'
                );
            }
            $data = $stmt->fetchAll();
            break;

        // --- 社員一覧（会社・部署でフィルタ可） ---
        case 'employees':
        default:
            $sql    = 'SELECT e.*, c.name AS company_name, d.name AS department_name
                       FROM employees e
                       JOIN companies c    ON e.company_id    = c.id
                       JOIN departments d  ON e.department_id = d.id';
            $params = [];
            $where  = [];

            if ($company_id) {
                $where[]  = 'e.company_id = ?';
                $params[] = $company_id;
            }
            if ($department_id) {
                $where[]  = 'e.department_id = ?';
                $params[] = $department_id;
            }
            if ($where) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }
            $sql .= ' ORDER BY e.id';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            break;
    }

    // ---------- ⑤ JSON で返却 ----------
    echo json_encode([
        'type'  => $type,
        'count' => count($data),
        'data'  => $data,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB接続エラー: ' . $e->getMessage()]);
}
