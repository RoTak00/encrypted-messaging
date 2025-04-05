<?= $head ?>

<div class="wrapper">

    <div class="banner">
    </div>
    <div class="content">
        <div class="mb-4">
            <p><strong>Sent on <?= $date ?>:</strong></p>
            <div class="alert alert-secondary"><?= htmlspecialchars($message) ?></div>
        </div>

        <?php if (!empty($responses)): ?>
            <div class="mb-4">
                <p><strong>Responses:</strong></p>
                <ul class="list-group">
                    <?php foreach ($responses as $r): ?>
                        <li class="list-group-item"><?= htmlspecialchars($r) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a href="<?= $home ?>">Send a new message</a>
    </div>






</div>
<?= $footer ?>