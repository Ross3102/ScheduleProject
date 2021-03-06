<?php writeHeader($SCHEDULES) ?>
<div class="container">
    <h3 class="title"><?php echo $schedule["schedule_name"] ?></h3>
    <div class="center-align">
        <div class="timer"></div>
        <br>
        <div class="schedule_actions">
            <i class="material-icons medium clickable" onclick="location.href='..'">stop</i>
            <i class="material-icons medium clickable" onclick="pause()" id="pause">pause</i>
            <i class="material-icons medium clickable" onclick="nextAssignment(true)">fast_forward</i>
        </div>
    </div>
    <table class="centered">
        <thead>
        <tr>
            <th>Item Name</th>
            <th>Item Duration</th>
            <th>Finish By</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item) { ?>
            <tr>
                <td><?php echo $item["item_name"] ?></td>
                <td><?php echo int_to_duration($item["item_duration"]) ?></td>
                <td id="<?php echo $item['item_id'] ?>"></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="modal" id="extendFinish" style="width: 30%">
        <div class="modal-content center-align">
            <p>Press Extend to add more time to the current item or press Finish to continue to the next item.</p>
        </div>
        <div class="modal-footer">
            <a href="#extendAmount" class="modal-trigger modal-action modal-close waves-effect waves-green btn-flat">Extend</a>
            <button onclick="nextAssignment()" class="modal-action modal-close waves-effect waves-green btn-flat">Finish</button>
        </div>
    </div>

    <div class="modal" id="extendAmount" style="width: 45%">
        <div class="modal-content center-align">
            <h5>Choose the amount of time to extend the assignment by:</h5>
            <div class="row">
                <div class="input-field col s6 offset-s3">
                    <input type="text" id="amount" placeholder="HH:MM:SS">
                    <label for="amount">Item Duration</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="nextAssignment()" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</button>
            <button onclick="extend()" class="waves-effect waves-green btn-flat">Extend</button>
        </div>
    </div>
</div>

<?php writeFooter() ?>

<script>

    $(".modal").modal({
        dismissible: false
    });

    function extend() {
        amount = $("#amount");
        var duration = amount.val();
        if (/\d{1,2}:\d{2}:\d{2}/.test(duration)) {
            amount.val("");
            $("#extendAmount").modal("close");
            duration = duration.split(":");
            items[itemIndex]["item_duration"] = parseInt(duration[0]) * 3600 + parseInt(duration[1]) * 60 + parseInt(duration[2]);
            updateTimes();
            updateTimer();
            interval = setInterval(wait, 1000)
        } else {
            M.toast({html: 'Enter an amount in the format HH:MM:SS or H:MM:SS'});
        }

    }

    function pause() {
        if (paused) {
            $("#pause").text("pause");
        } else {
            $("#pause").text("play_arrow");
        }
        paused = !paused;
    }

    function nextAssignment(button=false) {
        var offset = 0;
        if (itemIndex >= 0) {
            items[itemIndex]["item_duration"] = 0;
            offset = -1;
        }
        itemIndex++;
        if (itemIndex === items.length)
            location.href="./index.php?action=confirm&schedule_id=<?php echo $schedule_id ?>";
        updateTimes(offset);
        updateTimer();
        $("tr").css("backgroundColor", "white");
        $("tbody tr:nth-child(" + (itemIndex + 1) + ")").css("backgroundColor", "#1de9b6");
        if (!button) {
            interval = setInterval(wait, 1000);
        }
    }

    function updateTimer() {
        var mil = items[itemIndex]["item_duration"] * 1000;
        var hours = parseInt(mil / (60*60*1000));
        mil %= 60*60*1000;
        var minutes = ("0" + parseInt(mil / (60*1000))).slice(-2);
        mil %= 60*1000;
        var seconds = ("0" + parseInt(mil / 1000)).slice(-2);
        $(".timer").html(hours + ":" + minutes + ":" + seconds);
        sendNotification(hours + ":" + minutes + ":" + seconds);
        updateTimes(); // TODO maybe find way to do this better?
    }

    function updateTimes(offset=0) {
        var time = new Date();
        items.slice(itemIndex + offset).forEach(function(item) {
            time = new Date(time.getTime() + item['item_duration'] * 1000);
            $("#" + item['item_id']).html(time.toLocaleTimeString());
        });
    }

    function sendNotification(body) {
        Notification.requestPermission(function (status) {  // status is "granted", if accepted by user
            var n = new Notification("Time left", {
                body: body
                // icon: '/path/to/icon.png' // optional
            });
        });
    }

    function wait() {
        if (paused) {
            updateTimes();
        } else {
            items[itemIndex]["item_duration"] -= 1;
            updateTimer();
            if (items[itemIndex]["item_duration"] == 0) {
                $("#extendFinish").modal("open");
                clearInterval(interval);
            }
        }
    }

    var paused = false;
    var itemIndex=-1;
    var items = <?php echo json_encode($items) ?>;
    var interval;
    nextAssignment();

</script>