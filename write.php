<?php
//文字作成
$str = date("Y-m-d H:i:s");

// フォームデータが送信されたかをチェック
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // データを変数に格納
   $name = $_POST['name'];
   $email = $_POST['email'];
   $age = $_POST['age'];
   $satisfaction = $_POST['satisfaction'];
   $comment = $_POST['comment'];

   // CSVファイルに書き込む
$file = fopen('result.csv', 'a');

// 表題を表す文字列
$header = "番号,回答日時,名前,メールアドレス,年齢,満足度,コメント\n";

// ファイルが空の場合、表題を書き込む
if (filesize('result.csv') == 0) {
    // BOM (Byte Order Mark) を付与して文字化けを防ぐ
    fwrite($file, "\xEF\xBB\xBF");
    fwrite($file, $header);
}


// 番号を自動付与
$line = count(file('result.csv')) ;

// UTF-8の場合
// データをCSVファイルに書き込む
$data = [$line, $str, $name, $email, $age, $satisfaction, $comment];
fputcsv($file, $data);
fclose($file);

}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://cdn.tailwindcss.com"></script>
   <title>アンケート回答完了</title>
</head>

<header class="">
        <?php include 'header.php'; ?>
    </header>

<body> 
    <div class="text-center">
    <h1 class="mt-28 text-4xl my-5" >アンケート回答完了</h1>
   <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
       <p class="text-2xl mb-10">アンケートへの回答を受け付けました。ありがとうございました。</p>
   <?php else : ?>
       <p class="my-5">このページにはフォームからアクセスしてください。</p>

   <?php endif; ?>
   <a href="read.php" class="text-4xl text-blue-500">アンケート集計を見る</a>

    </div> 

</body>
</html>