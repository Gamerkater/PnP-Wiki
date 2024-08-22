<?php /** @noinspection ALL */

namespace App\Repository;

use App\Collection\UserCollection;
use App\Database;
use App\Model\User;
use DateTime;
use Exception;
use http\Exception\InvalidArgumentException;
use mysqli;
use mysqli_result;
use mysqli_stmt;

class UserRepository implements RepositoryInterface
{
    private mysqli $db;
    private string $table = 'users';
    public function findAll(string $order = ''): ?UserCollection
    {
        $this->connectDB();
        if(!empty($order)){
            $query = "SELECT * FROM `$this->table` ORDER BY `$order`";
        }
        else{
            $query = "SELECT * FROM `$this->table`";
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $this->findCollection($stmt);
    }

    public function findById(int $id): ?User
    {
        $this->connectDB();
        $stmt = $this->db->prepare("SELECT * FROM `$this->table` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $this->findOne($result);
    }

    public function findBy(string $column, mixed $value, string $order = ''): ?UserCollection
    {
        $this->connectDB();
        if(!empty($order) && $value !== null){
            $query = "SELECT * FROM `$this->table` WHERE `$column` = ? ORDER BY `$order`";
        }
        elseif(!empty($order) && $value === null){
            $query = "SELECT * FROM `$this->table` WHERE `$column` IS null ORDER BY `$order`";
        }
        elseif(empty($order) && $value === null){
            $query = "SELECT * FROM `$this->table` WHERE `$column` IS NULL";
        }
        else{
            $query = "SELECT * FROM `$this->table` WHERE `$column` = ?";
        }
        $stmt = $this->db->prepare($query);
        if($value === null){
            $stmt->execute();
        }
        else{
            $stmt->execute([$value]);
        }
        return $this->findCollection($stmt);
    }
    public function findOneBy(string $column, mixed $value): ?User
    {
        $this->connectDB();
        if($value === null){
            $query = "SELECT * FROM `$this->table` WHERE `$column` IS null";
        }
        else{
            $query = "SELECT * FROM `$this->table` WHERE `$column` = ?";
        }
        $stmt = $this->db->prepare($query);
        if($value === null){
            $stmt->execute();
        }
        else{
            $stmt->execute([$value]);
        }
        $result = $stmt->get_result();
        return $this->findOne($result);
    }

    public function save(object $entity): void
    {
        if(!$entity instanceof User){
            throw new InvalidArgumentException(sprintf("Entity must be instance of %s", User::class));
        }
        else{
            $this->connectDB();
            $id = $entity->getId();
            $registrationDate = date("Y-m-d H:i:s", $entity->getRegistrationDate()->getTimestamp());
            $firstname = $entity->getFirstname();
            $lastname = $entity->getLastname();
            $email = $entity->getEmail();
            $username = $entity->getUsername();
            $password = $entity->getPassword();
            $verified = $entity->getVerified() === true ? 1 : 0;
            $token = $entity->getToken();
            $firstnamePublic = $entity->getFirstnamePublic() === true ? 1 : 0;
            $lastnamePublic = $entity->getLastnamePublic() === true ? 1 : 0;
            $profileText = $entity->getProfileText();
            if($id === 0){
                $query = "INSERT INTO `$this->table` (firstname, lastname, email, username, password, verified, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sssssis", $firstname, $lastname, $email, $username, $password, $verified, $token);
            }
            else{
                $query = "UPDATE `$this->table` SET `firstname` = ?, `registration_date` = ?, `lastname` = ?, `email` = ?, `username` = ?, `password` = ?, `verified` = ?, `token` = ?, `firstname_public` = ?, `lastname_public` = ?, `profiletext` = ? WHERE `id` = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ssssssisiisi", $firstname, $registrationDate, $lastname, $email, $username, $password, $verified, $token, $firstnamePublic, $lastnamePublic, $profileText, $id);
            }
            $stmt->execute();
            $this->closeDB();
        }
    }

    public function delete(object $entity): void
    {
        if(!$entity instanceof User){
            throw new InvalidArgumentException(sprintf("Entity must be instance of %s", User::class));
        }
        else{
            $this->connectDB();
            $id = $entity->getId();
            $query = "DELETE FROM `$this->table` WHERE `id` = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $this->closeDB();
        }
    }
    public function connectDB(): void
    {
        $this->db = Database::dbConnect();
    }
    public function closeDB(): void
    {
        $this->db->close();
    }

    /**
     * @param false|mysqli_result $result
     * @return User|null
     * @throws Exception
     */
    private function findOne(false|mysqli_result $result): ?User
    {
        $user = $result->fetch_object();
        $this->closeDB();
        if (!empty($user)) {
            $user->registration_date = (new DateTime($user->registration_date));
            return new User($user->id, $user->registration_date, $user->firstname, $user->lastname, $user->email, $user->username, $user->password, $user->verified === 1, $user->token, $user->firstname_public === 1, $user->lastname_public === 1, $user->profiletext);
        } else {
            return null;
        }
    }

    /**
     * @param false|mysqli_stmt $stmt
     * @return UserCollection|null
     * @throws Exception
     */
    private function findCollection(false|mysqli_stmt $stmt): ?UserCollection
    {
        $users = new UserCollection();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($user = $result->fetch_object()) {
                $user->registration_date = (new DateTime($user->registration_date));
                $user = new User($user->id, $user->registration_date, $user->firstname, $user->lastname, $user->email, $user->username, $user->password, $user->verified === 1, $user->token, $user->firstname_public === 1, $user->lastname_public === 1, $user->profiletext);
                $users->offsetSet($users->key(), $user);
                $users->next();
            }
            $this->closeDB();
            return $users;
        }
        else {
            $this->closeDB();
            return null;
        }
    }
}