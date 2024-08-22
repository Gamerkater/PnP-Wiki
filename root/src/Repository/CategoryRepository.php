<?php /** @noinspection ALL */

namespace App\Repository;

use App\Collection\CategoryCollection;
use App\Database;
use App\Model\Category;
use App\Repository\RepositoryInterface;
use mysqli;
use DateTime;

class CategoryRepository implements RepositoryInterface
{
    private mysqli $db;
    private string $table = 'categories';

    public function findAll(string $order = ''): ?CategoryCollection
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

    public function findById(int $id): ?Category
    {
        $this->connectDB();
        $stmt = $this->db->prepare("SELECT * FROM `$this->table` WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $this->findOne($result);
    }

    public function findBy(string $column, mixed $value, string $order = ''): ?CategoryCollection
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

    public function findOneBy(string $column, mixed $value): ?Category
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
        if(!$entity instanceof Category){
            throw new InvalidArgumentException(sprintf("Entity must be instance of %s", Category::class));
        }
        else{
            $this->connectDB();
            $id = $entity->getId();
            $name = $entity->getName();
            $description = $entity->getDescription();
            $published  = date("Y-m-d H:i:s", $entity->getPublished()->getTimestamp());
            $createdBy = $entity->getCreatedBy()->getId();
            $lastEdit  = date("Y-m-d H:i:s");
            $lastEditBy = $entity->getLastEditBy()->getId();
            $icon = $entity->getIcon();
        }
        if($id === 0){
            $query = "INSERT INTO `$this->table` (name, description, created_by, last_edit_by, icon) VALUES(?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssiis", $name, $description, $createdBy, $lastEditBy, $icon);
        }
        else{
            $query = "UPDATE `$this->table` SET `name` = ?, `description` = ?, `published` = ?, `created_by` = ?, `last_edit` = ?, `last_edit_by` = ?, `icon` = ? WHERE `id` = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssisisi", $name, $description, $published, $createdBy, $lastEdit, $lastEditBy, $icon, $id);
        }
        $stmt->execute();
        $this->closeDB();
    }

    public function delete(object $entity): void
    {
        if(!$entity instanceof Category){
            throw new InvalidArgumentException(sprintf("Entity must be instance of %s", Category::class));
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

    private function findCollection(false|\mysqli_stmt $stmt): ?CategoryCollection
    {
        $categories = new CategoryCollection();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($category = $result->fetch_object()) {
                $category = $this->convertDataTypes($category);
                $category = new Category($category->id, $category->name, $category->description, $category->published, $category->created_by, $category->last_edit, $category->last_edit_by, $category->icon);
                $categories->offsetSet($categories->key(), $category);
                $categories->next();
            }
            $this->closeDB();
            return $categories;
        }
        else {
            $this->closeDB();
            return null;
        }
    }

    private function findOne(false|\mysqli_result $result): ?Category
    {
        $category = $result->fetch_object();
        $this->closeDB();
        if(!empty($category)){
            $category = $this->convertDataTypes($category);
            return new Category($category->id, $category->name, $category->description, $category->published, $category->created_by, $category->last_edit, $category->last_edit_by, $category->icon);
        }
        else{
            return null;
        }
    }
    private function convertDataTypes(object $category): object{
        $category->published = (new DateTime($category->published));
        $category->created_by = (new UserRepository())->findById($category->created_by);
        $category->last_edit = (new DateTime($category->last_edit));
        $category->last_edit_by = (new UserRepository())->findById($category->last_edit_by);
        $this->connectDB();
        return $category;
    }
}