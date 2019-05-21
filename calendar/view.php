<?php
$head = '
<style>

.container {
    padding: 10px;
}

th {
    text-align: center;
}

tr {
    min-height: 100px !important;
}

td {
border: 1px solid black;
vertical-align: top !important;
width: 14.2857142857%;
}
td div {
    padding: 5px;
    margin: 1px;
    font-size: .75em;
}

#today {
    background-color: rgba(0, 0, 255, 0.2);
}

</style>';
writeHeader($CALENDAR, $head);
$numCategories = count($categories);
?>
<div class="container main z-depth-4">
    <form action="./index.php" class="row">
        <input type="hidden" name="action" value="show_month">
        <a style="margin-right: 5px;" href="./index.php?action=show_calendar" class="col s1 offset-s2 btn btn-large blue waves-effect waves-light"><i class="material-icons">today</i> Now</a>
        <a href="./index.php?action=show_month&month=<?php echo ($month_num + 10) % 12 + 1 ?>&year=<?php echo $year_num + intdiv($month_num - 13, 12)?>" class="col s1 btn btn-large blue waves-effect waves-light"><i class="material-icons">chevron_left</i></a>
        <div class="input-field col s3">
            <select name="month" id="month">
                <?php
                    $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    for ($i = 0; $i < count($months); $i++) {
                        $month = $months[$i] ?>
                        <option value="<?php echo $i + 1 ?>"><?php echo $month ?></option>
                    <?php } ?>
            </select>
            <label for="month">Select Month</label>
        </div>
        <div class="input-field col s2">
            <input type="number" name="year" id="year" value="<?php echo $year_num ?>">
            <label for="year">Year</label>
        </div>
        <a href="./index.php?action=show_month&month=<?php echo ($month_num) % 12 + 1 ?>&year=<?php echo $year_num + intdiv($month_num, 12)?>" class="col s1 btn btn-large blue waves-effect waves-light"><i class="material-icons">chevron_right</i></a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Sunday</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?php
                        $month_lengths = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
                        if ($year_num % 4 == 0 && $year_num % 100 != 0)
                            $month_lengths[1] = 29;
                        $cur_date = 1;
                        for ($i=0; $i < $weekday; $i++) {
                            echo "</td><td>";
                        }
                        echo $cur_date;
                        foreach ($all_tasks as $task):
                            $task_id = $task["task_id"];
                            $category_id = $task["category_id"];
                            $category_name = $task["category_name"];
                            $category_color = $task["category_color"];
                            $task_name = $task["task_name"];
                            $task_date = $task["table_date"];
                            $form_date = $task["form_date"];
                            $split_date = explode("/", $task_date);
                            $date_month = $split_date["0"];
                            $date_day = $split_date["1"];
                            $date_year = $split_date["2"];
                            $task_completed = $task["task_completed"];
                            if ($date_month == $month_num && $date_year == $year_num) {
                                while ($cur_date < $date_day) {
                                    echo "</td>";
                                    $cur_date++; $weekday++;
                                    if ($weekday == 7) {
                                        $weekday = 0;
                                        echo "</tr><tr>";
                                    }
                                    if ($cur_date == date('d') && $date_month == date('m') && $date_year == date('Y'))
                                        echo "<td id='today'>" . $cur_date;
                                    else
                                        echo "<td>" . $cur_date;
                                }
                                ?>
                                <div style="color: white; background-color: <?php echo $category_color ?>"><?php echo $task_name ?></div>
                            <?php } endforeach;

                            while ($cur_date < $month_lengths[$month_num - 1]) {
                                echo "</td>";
                                $cur_date++;
                                $weekday++;
                                if ($weekday == 7) {
                                    $weekday = 0;
                                    echo "</tr><tr>";
                                }
                                echo "<td>" . $cur_date;
                            }
                            while ($weekday < 6) {
                                echo "</td><td>";
                                $weekday++;
                            } ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $("select").val(<?php echo $month_num ?>);
        $("select").material_select();
    });

    $("#month").change(function() {
        $("form").submit();
    });

    $("#year").change(function() {
        $("form").submit();
    });

</script>

<?php writeFooter() ?>