<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=careeros', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$rows = $pdo->query("
    SELECT t.id, t.slug, t.title,
           COUNT(q.id) as total,
           SUM(q.type = 'MCQ') as mcq
    FROM topics t
    LEFT JOIN questions q ON q.topic_id = t.id
    WHERE t.slug IN ('php-basics-junior','php-intermediate','php-advanced')
    GROUP BY t.id, t.slug, t.title
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    echo "Topic: {$r['title']} (id={$r['id']})\n";
    echo "  Total questions: {$r['total']}, MCQ: {$r['mcq']}\n";
}
