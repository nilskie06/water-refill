<?php
$db_path = '/var/www/water-refill/database/database.sqlite';
if (!file_exists($db_path)) die('Database not found');

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    $selected = $_GET['table'] ?? '';
    $data = $columns = [];
    $count = 0;
    if ($selected && in_array($selected, $tables)) {
        $columns = $db->query("PRAGMA table_info(\"$selected\")")->fetchAll(PDO::FETCH_ASSOC);
        $data = $db->query("SELECT * FROM \"$selected\" LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
        $count = $db->query("SELECT COUNT(*) FROM \"$selected\"")->fetchColumn();
    }
} catch (Exception $e) { die('Error: ' . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database - Water Refill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *{font-family:'Inter',sans-serif}body{background:#0f172a;color:#e2e8f0;min-height:100vh}
        .glass{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:.75rem}
        .tl{display:block;padding:8px 12px;border-radius:8px;color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;transition:all .2s}
        .tl:hover{background:rgba(255,255,255,.08);color:#fff}
        .tl.active{background:rgba(6,182,212,.15);color:#22d3ee;border:1px solid rgba(6,182,212,.2)}
        td,th{padding:8px 12px;border-bottom:1px solid rgba(255,255,255,.05);font-size:12px;white-space:nowrap}
        th{color:rgba(255,255,255,.4);text-transform:uppercase;font-size:10px;position:sticky;top:0;background:#0f172a;z-index:1}
        td{color:rgba(255,255,255,.8);max-width:200px;overflow:hidden;text-overflow:ellipsis}
        tr:hover td{background:rgba(255,255,255,.03)}
    </style>
</head>
<body class="p-4 lg:p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center"><span class="text-lg">D</span></div>
        <div><h1 class="text-xl font-bold text-white">Database Viewer</h1><p class="text-white/40 text-xs"><?= count($tables) ?> tables</p></div>
        <a href="/dashboard" class="ml-auto text-cyan-400 text-sm hover:underline">Back to App</a>
    </div>
    <div class="lg:hidden mb-4"><select onchange="window.location='?table='+this.value" class="w-full p-3 rounded-xl bg-white/5 border border-white/10 text-white text-sm">
        <option value="">Select table...</option>
        <?php foreach ($tables as $t): ?><option value="<?= $t ?>" <?= $selected === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
    </select></div>
    <div class="flex gap-4">
        <div class="w-44 shrink-0 hidden lg:block"><div class="glass p-3">
            <p class="text-white/40 text-[10px] uppercase tracking-wider mb-2 px-2">Tables</p>
            <?php foreach ($tables as $t): ?><a href="?table=<?= $t ?>" class="tl <?= $selected === $t ? 'active' : '' ?>"><?= $t ?></a><?php endforeach; ?>
        </div></div>
        <div class="flex-1 min-w-0">
            <?php if ($selected && $data !== false): ?>
                <div class="glass p-4 mb-4"><div class="flex justify-between items-center">
                    <div><h2 class="text-lg font-bold text-white"><?= $selected ?></h2><p class="text-white/30 text-xs"><?= $count ?> rows | <?= count($columns) ?> columns: <?= implode(', ', array_column($columns, 'name')) ?></p></div>
                </div></div>
                <div class="glass overflow-x-auto" style="max-height:70vh;overflow-y:auto">
                    <table class="w-full"><thead><tr><?php foreach ($columns as $col): ?><th><?= $col['name'] ?></th><?php endforeach; ?></tr></thead>
                    <tbody><?php foreach ($data as $row): ?><tr><?php foreach ($columns as $col): ?>
                        <td title="<?= htmlspecialchars((string)($row[$col['name']] ?? '')) ?>"><?= htmlspecialchars(mb_strimwidth((string)($row[$col['name']] ?? 'NULL'), 0, 60, '...')) ?></td>
                    <?php endforeach; ?></tr><?php endforeach; ?></tbody></table>
                </div>
                <?php if ($count > 100): ?><p class="text-white/30 text-xs mt-2 text-center">Showing 100 of <?= $count ?> rows</p><?php endif; ?>
            <?php else: ?>
                <div class="glass p-16 text-center"><p class="text-white/40">Select a table from the sidebar</p></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
