<?php
$head = "
<script src=\"../js/masonry.pkgd.min.js\"></script>
<link rel='stylesheet' href='../css/column-flow.css'>
<link rel='stylesheet' href='../css/shared.css'>

";
writeHeader($SCHEDULES, $head) ?>
    <div class="container">
        <h3 class="title">Your Schedules</h3>
        <div class="center-align">
            <a href="#add_schedule" class="waves-effect waves-light btn-large modal-trigger blue lighten-1">Add Schedule</a>
            <a href="./index.php?action=show_build_schedule" class="waves-effect waves-light btn-large blue lighten-1">Build Schedule</a>
        </div>

        <div class="modal" id="add_schedule">
            <form action="." method="post">
                <div class="modal-content">
                    <div class="container">
                        <h3>Add Schedule</h3>
                        <input type="hidden" name="action" value="add_schedule">
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="text" id="schedule_name" name="schedule_name" class="validate" required>
                                <label for="schedule_name">Schedule Name</label>
                            </div>
                            <div class="input-field col s12">
                                <textarea id="schedule_desc" name="schedule_desc" class="validate materialize-textarea"></textarea>
                                <label for="schedule_desc">Schedule Description</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    <button type="submit" class="modal-action modal-close waves-effect waves-green btn-flat">Add</button>
                </div>
            </form>
        </div>
        <div class="grid">
            <div class="grid-sizer"></div>
            <div class="gutter-sizer"></div>
            <?php foreach ($schedules as $schedule) {
                $schedule_id = $schedule["schedule_id"];
                $schedule_name = $schedule["schedule_name"];
                $schedule_desc = $schedule["schedule_desc"];
                $items = get_items_by_schedule_id($schedule_id);
                $total_duration = get_total_duration($schedule_id)["tot"] ?>
                    <div class="grid-item card sticky-action hoverable">
                        <div class="card-content">
                            <div class="activator">
                                <span class="card-title activator">
                                    <?php echo $schedule_name ?>
                                    <span class="new badge activator blue lighten-1"
                                          data-badge-caption="<?php echo "Item" . (count($items) != 1 ? "s": "") ?>">
                                        <?php echo count($items) ?>
                                    </span>
                                </span>
                                <blockquote class=activator style="border-left: 5px solid #50ae54">
                                    <p class="activator"><?php echo $schedule_desc ?></p>
                                </blockquote>
                                <div class="divider"></div>
                                <div class="section center-align">
                                    <div class="timer">
                                        <p class="activator"><?php echo int_to_duration($total_duration) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <a href="#" onclick="
                                event.stopPropagation();
                                confirmDeleteSchedule('<?php echo htmlspecialchars(addslashes($schedule_name)) ?>', <?php echo $schedule_id ?>);"
                            >Delete</a>
                            <a href="#Add<?php echo $schedule_id ?>" class="modal-trigger">Add Item</a>
                            <a href='./index.php?action=run&schedule_id=<?php echo $schedule_id ?>'>Run</a>
                        </div>
                        <div class="card-reveal" style="overflow: hidden;">
                            <span class="card-title white"><?php echo $schedule_name ?><i class="material-icons right">close</i></span>
                            <div style="overflow-y: scroll; height: 100%; width: 100%;">
                                <table class="bordered">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Duration</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="scheduleBody<?php echo $schedule_id ?>">
                                    <?php
                                    $num_items = count($items);

                                    for ($i = 0; $i < $num_items; $i++) {
                                        $item = $items[$i];
                                        $item_id = $item["item_id"];
                                        $item_name = $item["item_name"];
                                        $item_duration = $item["item_duration"]; ?>
                                        <tr data-item-id="<?php echo $item_id ?>">
                                            <td class="buttons">
                                                <?php if ($i != 0) { ?>
                                                    <i data-direction="up" class="material-icons clickable move_item">arrow_drop_up</i>
                                                    <br>
                                                <?php } if ($i != $num_items - 1) { ?>
                                                    <i data-direction="down" class="material-icons clickable move_item">arrow_drop_down</i>
                                            </td>
                                                <?php } ?>
                                            <td><?php echo $item_name ?></td>
                                            <td><?php echo int_to_duration($item_duration) ?></td>
                                            <td><i class="material-icons clickable" onclick="
                                                    event.stopPropagation();
                                                    confirmDeleteItem('<?php echo htmlspecialchars(addslashes($item_name)) ?>', <?php echo $item_id ?>, '<?php echo htmlspecialchars(addslashes($schedule_name)) ?>')"
                                                >delete</i>
                                        </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                                <br>
                            </div>
                        </div>
                    </div>

                    <div id="Add<?php echo $schedule_id ?>" class="modal">
                        <form action="." method="post">
                            <div class="modal-content">
                                <div class="container">
                                    <h3>Add Item</h3>
                                    <input type="hidden" name="action" value="add_item">
                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule_id ?>">
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <input type="text" id="item_name" name="item_name" class="validate" required>
                                            <label for="item_name">Item Name</label>
                                        </div>
                                        <div class="input-field col s6">
                                            <input type="text" id="item_duration" name="item_duration" placeholder="HH:MM:SS" pattern="\d{1,2}:\d{2}:\d{2}" class="validate" required>
                                            <label for="item_duration" data-error="Enter a duration in the format HH:MM:SS or H:MM:SS">Item Duration</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="#<?php echo $schedule_id ?>" class="modal-trigger modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                                <button type="submit" class="modal-action modal-close waves-effect waves-green btn-flat">Add</button>
                            </div>
                        </form>
                    </div>
            <?php } ?>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('.modal').modal();
            $('.grid').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: true,
                gutter: '.gutter-sizer'
            });
        });

        $(document).on("click", ".move_item", function() {
            var firstRow = $(this).parent().parent();
            var item_id = firstRow.attr("data-item-id");
            var direction = $(this).attr("data-direction");

            // Figure out which row to switch with
            var secondRow;
            if (direction === "up")
                secondRow = firstRow.prev();
            else
                secondRow = firstRow.next();

            var other_id = secondRow.attr("data-item-id");

            // Swap items in database
            $.ajax({
                type: "POST",
                url: "./index.php?action=swap_items&item_id=" + item_id + "&other_id=" + other_id
            });



            // Switch arrows in rows
            var temp = $(this).parent().html();
            $(this).parent().html(secondRow.find(".buttons").html());
            secondRow.find(".buttons").html(temp);

            // Switch positions of rows
            temp = firstRow.html();
            firstRow.html(secondRow.html());
            secondRow.html(temp);

        });

        function delItem(item_id) {
            location.href='./index.php?action=delete_item&item_id=' + item_id;
        }

        function delSchedule(schedule_id) {
            location.href='./index.php?action=delete_schedule&schedule_id=' + schedule_id;
        }

        function confirmDeleteItem(item_name, item_id, schedule_name) {
            M.toast({
                html: '<span>Delete ' + item_name + ' from ' + schedule_name + '?</span><button class="btn-flat toast-action" onclick="delItem(' + item_id + ')">Confirm</button>',
                displayLength: 10000});
        }

        function confirmDeleteSchedule(schedule_name, schedule_id) {
            M.toast({
                html: '<span>Delete schedule: ' + schedule_name + '?<span><button class="btn-flat toast-action" onclick="delSchedule(' + schedule_id + ')">Confirm</button>',
                displayLength: 10000});
        }
    </script>

<?php writeFooter() ?>