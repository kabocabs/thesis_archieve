<?php
require 'functions.php';
auth();

$q = "%".($_GET['q'] ?? '')."%";
$stmt = $pdo->prepare("
SELECT t.*, u.name 
FROM thesis t 
JOIN users u ON u.id=t.user_id
WHERE status='approved'
AND (title LIKE ? OR keywords LIKE ? OR adviser LIKE ? OR u.name LIKE ?)
");
$stmt->execute([$q,$q,$q,$q]);
?>
<form>
<input name="q" placeholder="Search">
<button>Search</button>
</form>

<table>
<?php foreach($stmt as $t): ?>
<tr>
<td><?= $t['title'] ?></td>
<td><?= $t['name'] ?></td>
<td><a href="<?= $pdo->query("SELECT file_path FROM files WHERE thesis_id={$t['id']}")->fetchColumn(); ?>">Download</a></td>
</tr>
<?php endforeach; ?>
</table>
