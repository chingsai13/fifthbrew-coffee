<?php
require_once 'includes/init.php';
$page_title = "About Us";
include 'includes/header.php';

$members = [
    ['name' => 'Willand Jairo O. Ching', 'role' => 'Documentation & Testing', 'photo' => 'images.png'],
    ['name' => 'Marc Angelo Ching Sai', 'role' => 'Database Design', 'photo' => 'images.png'],
    ['name' => 'Arianne M. Ducanes', 'role' => 'UI/UX Designer', 'photo' => 'images.png'],
    ['name' => 'Benj Lorenz Regoso', 'role' => 'Full-Stack Developer (Front-end & Back-end)', 'photo' => 'benj.png'],
];

function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $first = $parts[0][0] ?? '';
    $last = count($parts) > 1 ? $parts[count($parts) - 1][0] : '';
    return strtoupper($first . $last);
}
?>
<div class="page-wrap page-wrap-wide">
    <h1>About Us</h1>

    <h2>Backstory</h2>
    <p>5th Brew was started by five friends who wanted to bring good, honest coffee to everyone.
    From our first cup to yours, we're all about quality beans, simple recipes, and a warm place to slow down for a bit.</p>

    <h2>Members</h2>
    <div class="team-grid">
        <?php foreach ($members as $m):
            $photo_path = __DIR__ . '/assets/team/' . $m['photo'];
            $has_photo = file_exists($photo_path);
        ?>
            <div class="team-card">
                <div class="team-photo">
                    <?php if ($has_photo): ?>
                        <img src="assets/team/<?php echo htmlspecialchars($m['photo']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>">
                    <?php else: ?>
                        <span><?php echo initials($m['name']); ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="team-name"><?php echo htmlspecialchars($m['name']); ?></h3>
                <p class="team-role"><?php echo htmlspecialchars($m['role']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>