<table border="1">
    <tr>
        <?php foreach ($headers as $h): ?>
            <th style="background-color: #f2f2f2;"><?= $h ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($tickets as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= esc($row['title']) ?></td>
            <td><?= esc($row['requester_name'] ?? '') ?></td>
            <td><?= esc($row['cat_name'] ?? '') ?></td>
            <?php if ($isStaff): ?>
                <td><?= esc($row['reporter_name'] ?? '') ?></td>
            <?php endif; ?>
            <td><?= $row['priority'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if (in_array($row['status'], ['RESOLVED', 'CLOSED'])): ?>
                    Selesai
                <?php elseif ($row['status'] === 'PENDING'): ?>
                    Paused
                <?php elseif ($row['sla_deadline']): ?>
                    <?= $row['sla_deadline'] ?>
                <?php else: ?>
                    &mdash;
                <?php endif; ?>
            </td>
            <td><?= esc($row['location'] ?? '') ?></td>
            <td><?= esc($row['drive_link'] ?? '') ?></td>
            <?php if ($isStaff): ?>
                <td><?= esc(!empty($row['assigned_names']) ? $row['assigned_names'] : ($row['assigned_name'] ?? 'Unassigned')) ?></td>
            <?php endif; ?>
            <td><?= date('d/m/y', strtotime($row['created_at'])) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
