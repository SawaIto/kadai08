<?php
// CSVファイルを読み込む
$data = file('result.csv');
array_shift($data); // 1行目(ヘッダー行)を除外

// 満足度のラベル
$satisfaction_labels = array(
    '1' => '非常に不満',
    '2' => '不満',
    '3' => '普通',
    '4' => '満足',
    '5' => '大変満足'
);

// 満足度の集計
$satisfaction_counts = array(
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0
);
$total_responses = 0;

// 回答人数を初期化
foreach ($data as $line) {
    $fields = str_getcsv($line);
    if (count($fields) >= 6) { // フィールドの数をチェック
        $satisfaction = trim($fields[5]); // 満足度のフィールドインデックス (6番目のフィールド)
        if (array_key_exists($satisfaction, $satisfaction_counts)) {
            $satisfaction_counts[$satisfaction]++;
            $total_responses++; // 回答人数をカウント
        }
    }
}

// 円グラフのデータを準備
$chart_data = array();
foreach ($satisfaction_counts as $satisfaction => $count) {
    $percentage = ($count / $total_responses) * 100;
    $satisfaction_label = $satisfaction_labels[$satisfaction];
    $chart_data[] = array($satisfaction_label, $count, $percentage . '%');
}

// 年齢別の回答人数の集計
$age_counts = array();

foreach ($data as $line) {
    $fields = str_getcsv($line);
    if (count($fields) >= 4) { // フィールドの数をチェック
        $age = trim($fields[4]); // 年齢のフィールドインデックス (5番目のフィールド)
        if (!isset($age_counts[$age])) {
            $age_counts[$age] = 0;
        }
        $age_counts[$age]++;
    }
}

// 年齢別の回答人数の棒グラフデータを準備
$age_chart_data = array();
foreach ($age_counts as $age => $count) {
    $age_chart_data[] = array($age, $count);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>アンケート結果</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart', 'bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // 満足度のデータ
            var data = google.visualization.arrayToDataTable([
                ['満足度', '回答人数', {
                    role: 'annotation'
                }],
                <?php foreach ($chart_data as $item) {
                    echo "['" . $item[0] . "', " . $item[1] . ", '" . $item[2] . "'],";
                } ?>
            ]);

            var options = {
                title: '満足度の割合',
                chartArea: {
                    width: '80%'
                },
                pieHole: 0.4,
                colors: ['#e0440e', '#e67e22', '#f1c40f', '#2ecc71', '#27ae60'],
            };

            var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
            chart.draw(data, options);

            // 年齢別のデータ
            var ageData = google.visualization.arrayToDataTable([
                ['年齢', '回答人数'],
                <?php foreach ($age_chart_data as $item) {
                    echo "['" . $item[0] . "', " . $item[1] . "],";
                } ?>
            ]);

            var ageOptions = {
                title: '年齢別の回答人数',
                chartArea: {
                    width: '50%'
                },
                hAxis: {
                    title: '回答人数',
                    minValue: 0
                },
                vAxis: {
                    title: '年齢'
                }
            };

            var ageChart = new google.visualization.BarChart(document.getElementById('age_chart'));
            ageChart.draw(ageData, ageOptions);
        }
    </script>
</head>

<body>
    <header class="">
        <?php include 'header.php'; ?>
    </header>
    <h1 class="text-2xl text-center px-5 mt-28 sm:mt-20 bg-blue-100 p-2 rounded ">アンケート結果 【回答数 <?php echo $total_responses; ?>】 </h1>
    <div class="flex justify-center flex-wrap">
        <div class="w-full sm:w-1/2 md:w-1/2 lg:w-1/2 xl:w-1/2 mt-4 sm:mt-0">
            <div id="pie_chart" style="width: 400px; height: 300px;" class="w-full md:w-4/5 lg:w-3/5 xl:w-2/5 mx-auto mt-5 border border-gray-500 rounded-lg p-4"></div>
        </div>
        <div class="w-full sm:w-1/2 md:w-1/2 lg:w-1/2 xl:w-1/2 mt-4 sm:mt-0">
            <div id="age_chart" style="width: 400px; height: 300px;" class="w-full md:w-4/5 lg:w-3/5 xl:w-2/5 mx-auto mt-5 border border-gray-500 rounded-lg p-4"></div>
        </div>
    </div>

    <div id="COMMENT" class="anchor"></div>
    <div class="mx-auto text-left">
        <h1 class="text-2xl my-5 text-center bg-blue-100 p-2 rounded w-full">コメント</h1>
    </div>
    <div class="mx-auto max-w-screen-md text-left">

            <?php
        foreach ($data as $line) {
            $fields = str_getcsv($line); // CSVフィールドを取得
            $numFields = count($fields);
            if ($numFields >= 1) {
                $name = $fields[2]; // 名前のフィールドインデックス (3番目のフィールド)
            } else {
                $name = 'Unknown';
            }
            if ($numFields >= 7) {
                $comment = trim($fields[6]); // コメントのフィールドインデックス (7番目のフィールド)
            } else {
                $comment = '';
            }
            if (!empty($comment)) {
                echo "<p class=\"text-sm sm:text-base md:text-lg\"><strong>$name</strong>: <span class=\"text-sm sm:text-base md:text-lg\">$comment</span></p>";
            }
        }
        ?>
    </div>
</body>
<footer class="mt-10">
    <?php include 'footer.php'; ?>
</footer>

</html>