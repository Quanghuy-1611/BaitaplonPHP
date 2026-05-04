<?php
class UserModel extends Model
{
    protected $table = 'users';

    public function findByUsername($username)
    {
        return $this->queryOne(
            "SELECT * FROM users WHERE username = ?",
            [$username], 's'
        );
    }

    public function getAllUsers()
    {
        return $this->query("SELECT * FROM users ORDER BY role, username");
    }

    public function create($data)
    {
        return $this->insert(
            "INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)",
            [$data['username'], password_hash($data['password'], PASSWORD_DEFAULT),
             $data['full_name'], $data['email'], $data['role']],
            'sssss'
        );
    }

    public function update($id, $data)
    {
        if (!empty($data['password'])) {
            return $this->execute(
                "UPDATE users SET username=?, full_name=?, email=?, role=?, password=? WHERE id=?",
                [$data['username'], $data['full_name'], $data['email'], $data['role'],
                 password_hash($data['password'], PASSWORD_DEFAULT), $id],
                'sssssi'
            );
        }
        return $this->execute(
            "UPDATE users SET username=?, full_name=?, email=?, role=? WHERE id=?",
            [$data['username'], $data['full_name'], $data['email'], $data['role'], $id],
            'ssssi'
        );
    }

    public function updateProfile($id, $data)
    {
        return $this->execute(
            "UPDATE users SET full_name=?, email=? WHERE id=?",
            [$data['full_name'], $data['email'], $id],
            'ssi'
        );
    }

    public function changePassword($id, $newPassword)
    {
        return $this->execute(
            "UPDATE users SET password=? WHERE id=?",
            [password_hash($newPassword, PASSWORD_DEFAULT), $id],
            'si'
        );
    }

    public function usernameExists($username, $excludeId = 0)
    {
        return $this->queryOne(
            "SELECT id FROM users WHERE username=? AND id!=?",
            [$username, $excludeId], 'si'
        ) !== null;
    }
}
