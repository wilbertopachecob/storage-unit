<?php
//echo realpath("./lib/db/Models");
require "./lib/db/Models/item.php";
class ItemController
{
    public function addItem(Item $item): bool
    {
        return $item->add();
    }

    public function editItem(Item $item, int $id): bool
    {
        return $item->edit($id);
    }

    public function deleteItem(int $id, $conn): bool
    {
        return Item::delete($id, $conn);
    }

    public function getItemById(int $id, $conn): array
    {
        return Item::getItemById($id, $conn);
    }

    public function getItemByTitle(string $title, int $user_id, $conn): array
    {
        return Item::getItemByTitle($title, $user_id, $conn);
    }

    public function getAllItems(int $user_id, $conn): array
    {
        return Item::getAllItems($user_id, $conn);
    }

    public function getItemsAmountTotal(int $user_id, $conn): int
    {
        return Item::getItemsAmountTotal($user_id, $conn);
    }

}

