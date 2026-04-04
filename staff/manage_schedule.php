<div class="dashboard">

<div class="content-card">
    <h3 class="section-title">Create Schedule</h3>

    <?php if(!empty($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="crud-form two-column-form">

        <div class="form-group">
            <label>Day</label>
            <select name="day_of_week" required>
                <option value="">Select Day</option>
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
                <option>Saturday</option>
                <option>Sunday</option>
            </select>
        </div>

        <div class="form-group">
            <label>Start Time</label>
            <input type="time" name="start_time" required>
        </div>

        <div class="form-group">
            <label>End Time</label>
            <input type="time" name="end_time" required>
        </div>

        <div class="form-group">
            <label>Max Patients</label>
            <input type="number" name="max_patients" min="1" max="50" required>
        </div>

        <div class="form-actions full-width">
            <button class="action-btn" name="add_schedule">Save Schedule</button>
        </div>

    </form>
</div>


<div class="content-card">
    <h3 class="section-title">Your Schedules</h3>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Max Patients</th>
                </tr>
            </thead>

            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['day_of_week'] ?></td>
                    <td>
                        <?= date("g:i A", strtotime($row['start_time'])) ?> -
                        <?= date("g:i A", strtotime($row['end_time'])) ?>
                    </td>
                    <td><?= $row['max_patients'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>

</div>