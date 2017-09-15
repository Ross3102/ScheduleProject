<?php

include "../util/main.php";
include "../model/taskdb.php";

$action = strtolower(filter_input(INPUT_POST, 'action'));
if ($action == NULL) {
    $action = strtolower(filter_input(INPUT_GET, 'action'));
    if ($action == NULL) {
        $action = 'list_tasks';
    }
}


switch ($action) {
    case 'list_tasks':
        $categories = get_categories_by_user_id($user["user_id"]);
        include "view.php";
        break;
    case 'add_category':
        $category_name = filter_input(INPUT_POST, "category_name");
        add_category($user["user_id"], $category_name);
        header("Location: .");
        break;
    case 'add_task':
        $category_id = filter_input(INPUT_POST, "task_category");
        $task_name = filter_input(INPUT_POST, "task_name");
        $task_date = filter_input(INPUT_POST, "task_date");
        $task_date = date('Y-m-d', strtotime($task_date));
        add_task_to_category($category_id, $task_name, $task_date);
        header("Location: .");
        break;
    case 'delete_category':
        $category_id = filter_input(INPUT_GET, "category_id");
        delete_category($category_id);
        header("Location: .");
        break;
    case 'delete_task':
        $task_id = filter_input(INPUT_GET, "task_id");
        $category_id = get_task_by_id($task_id)["category_id"];
        delete_task($task_id);
        header("Location: .");
        break;
    case 'complete':
        $task_id = filter_input(INPUT_GET, "task_id");
        $task_completed = filter_input(INPUT_GET, "task_completed");
        complete($task_id, $task_completed);
        header("Location: .");
        break;
    default:
        echo ('Unknown action: ' . $action);
        exit();
        break;
}