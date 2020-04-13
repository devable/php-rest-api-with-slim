<?php

namespace App\Storage\CRM;

use Doctrine\DBAL\Connection;

class Auth
{

    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $hash
     * @return string|null User ID
     */
    public function validation(string $hash): ?string
    {
        $query = "SELECT `user_id` FROM `devices_hash`
            WHERE `hash` = :hash AND `created_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";

        $statement = $this->db->prepare($query);
        $statement->bindValue('hash', $hash);
        $statement->execute();

        if (!$statement) {
            return null;
        }

        return $statement->fetchColumn();
    }

    public function registration(int $userId, string $uid, string $token): bool
    {
        $query = 'INSERT INTO `devices`(`user_id`, `uid`, `token`)
            VALUES (:userId, :uid, :token) ON DUPLICATE KEY UPDATE `uid`= :uid, `token`= :token';

        $statement = $this->db->prepare($query);
        $statement->bindValue('userId', $userId);
        $statement->bindValue('uid', $uid);
        $statement->bindValue('token', $token);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function logout(string $uid, string $token): bool
    {
        $query = 'DELETE FROM `devices` WHERE `uid` = :uid AND `token` = :token';

        $statement = $this->db->prepare($query);
        $statement->bindValue('uid', $uid);
        $statement->bindValue('token', $token);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function check(string $uid, string $token): bool
    {
        $query = 'SELECT EXISTS(SELECT * FROM `devices` WHERE `uid` = :uid AND `token` = :token)';

        $statement = $this->db->prepare($query);
        $statement->bindValue('uid', $uid);
        $statement->bindValue('token', $token);
        $statement->execute();

        return boolval($statement->fetchColumn());
    }

    public function user(string $uid, string $token): ?array
    {
        $query = 'SELECT u.`name`, u.`fullname` FROM `devices` d
            LEFT JOIN `users` u ON u.id = d.user_id
            WHERE `uid` = :uid AND `token` = :token';

        $statement = $this->db->prepare($query);
        $statement->bindValue('uid', $uid);
        $statement->bindValue('token', $token);
        $statement->execute();

        return $statement->fetch() ?: null;
    }
}
