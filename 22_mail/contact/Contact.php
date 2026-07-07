<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class Contact
{
    private PHPMailer $mailer;
    private string $template = __DIR__ . "/templates/contact_mail.html";

    // 環境変数をクラスメンバで保持
    private string $from_address;
    private string $from_name;
    private string $host;
    private string $username;
    private string $password;
    private string $encryption;
    private int    $port;
    private string $subject = "";

    public function __construct()
    {
        // .env 読み込み
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // クラスメンバに代入
        $this->from_address = $_ENV['MAIL_FROM_ADDRESS'];
        $this->from_name    = $_ENV['MAIL_FROM_NAME'];
        $this->host         = $_ENV['MAIL_HOST'];
        $this->username     = $_ENV['MAIL_USERNAME'];
        $this->password     = $_ENV['MAIL_PASSWORD'];
        $this->encryption   = $_ENV['MAIL_ENCRYPTION'];
        $this->port         = (int) $_ENV['MAIL_PORT'];
        $this->subject      = $_ENV['MAIL_SUBJECT'];

        $this->setupMailer();
    }

    /**
     * メーラー初期設定
     */
    private function setupMailer(): void
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Host       = $this->host;
        $this->mailer->Username   = $this->username;
        $this->mailer->Password   = $this->password;
        $this->mailer->SMTPSecure = $this->encryption;
        $this->mailer->Port       = $this->port;
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->Encoding   = 'base64';
    }

    /**
     * 入力バリデーション
     */
    public function validate(string $name, string $email, string $body): string
    {
        if (empty($name) || empty($email) || empty($body)) {
            return "すべてのフィールドを入力してください。";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "有効なメールアドレスを入力してください。";
        }
        return '';
    }

    /**
     * テンプレートを読み込んで置換
     */
    private function loadTemplate(array $values)
    {
        $template = file_get_contents($this->template);
        foreach ($values as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        return $template;
    }

    /**
     * メール送信
     */
    public function send(string $name, string $email, string $body)
    {
        try {
            // HTMLメール作成
            $html = $this->loadTemplate([
                "name"  => $name,
                "email" => $email,
                "body"  => nl2br($body),
            ]);

            $this->mailer->setFrom($this->from_address, $this->from_name);
            $this->mailer->addAddress($email, $name);
            $this->mailer->addReplyTo($this->from_address, $this->from_name);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $this->subject;
            $this->mailer->Body = $html;

            return $this->mailer->send();
        } catch (Exception $e) {
            return $this->mailer->ErrorInfo;
        }
    }
}
