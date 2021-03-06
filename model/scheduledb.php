<?php

function get_schedule_by_id($schedule_id) {
    global $db;

    $query = "select *
    from schedule
    where schedule_id = :schedule_id";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':schedule_id', $schedule_id);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result;
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function get_item_by_id($item_id) {
    global $db;

    $query = "select *
    from item
    where item_id = :item_id";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':item_id', $item_id);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result;
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function get_schedules_by_user_id($user_id) {
    global $db;

    $query = "select schedule_id, schedule_name, schedule_desc
              from schedule
              where user_id = :user_id
              order by schedule_id";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $result = $statement->fetchAll();
        $statement->closeCursor();
        return $result;
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function get_items_by_schedule_id($schedule_id) {
    global $db;

    $query = "select item_id, item_name, item_duration
              from item
              where schedule_id = :schedule_id
              order by item_id";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':schedule_id', $schedule_id);
        $statement->execute();
        $result = $statement->fetchAll();
        $statement->closeCursor();
        return $result;
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function get_total_duration($schedule_id) {
    global $db;

    $query = "select sum(item_duration) as tot
              from item
              where schedule_id = :schedule_id";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':schedule_id', $schedule_id);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result;
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function last_insert() {
    global $db;

    $query = "SELECT LAST_INSERT_ID()";

    try {
        $statement = $db->prepare($query);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
        return $result[0];
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function add_schedule($user_id, $schedule_name, $schedule_desc) {
    global $db;

    $query = "insert into schedule (user_id, schedule_name, schedule_desc)
              values (:user_id, :schedule_name, :schedule_desc)";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->bindValue(':schedule_name', $schedule_name);
        $statement->bindValue(':schedule_desc', $schedule_desc);
        $statement->execute();
        $statement->closeCursor();
        return last_insert();
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function add_item_to_schedule($schedule_id, $item_name, $item_duration) {
    global $db;

    $query = "insert into item (schedule_id, item_name, item_duration)
              values (:schedule_id, :item_name, :item_duration)";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':schedule_id', $schedule_id);
        $statement->bindValue(':item_name', $item_name);
        $statement->bindValue(':item_duration', $item_duration);
        $statement->execute();
        $statement->closeCursor();
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function delete_schedule($schedule_id) {
    global $db;

    $query = "delete from item
              where schedule_id = :schedule_id;
              delete from schedule
              where schedule_id = :schedule_id";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':schedule_id', $schedule_id);
        $statement->execute();
        $statement->closeCursor();
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function delete_item($item_id) {
    global $db;

    $query = "delete from item
              where item_id = :item_id;";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':item_id', $item_id);

        $statement->execute();
        $statement->closeCursor();
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}

function swap_items($id1, $id2) {

    global $db;

    $query = "UPDATE item, item as item2
              SET item.item_name = item2.item_name,
                  item.item_duration = item2.item_duration,
                  item2.item_name = item.item_name,
                  item2.item_duration = item.item_duration
              WHERE item.item_id = :id1 AND item2.item_id = :id2";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':id1', $id1);
        $statement->bindValue(':id2', $id2);
        $statement->execute();
        $statement->closeCursor();
    } catch (PDOException $e) {
        echo ($e);
        exit();
    }
}