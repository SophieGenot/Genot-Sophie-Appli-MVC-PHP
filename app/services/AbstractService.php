<?php
abstract class AbstractService {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public static function isPhoneValid(string &$phone): bool {
        $phone = str_replace([' ', '.', '-', '/'], '', $phone);

        return preg_match('/^[0-9]{10}$/', $phone) === 1;
    }

    public static function isEmailValid(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    public static function formatDateFr(?string $date, bool $withTime = true): string {
    if (!$date) return "N/C";

    $dateTime = new DateTime($date);
    return $withTime ? $dateTime->format('d/m/Y à H\hi') : $dateTime->format('d/m/Y');
    }
}