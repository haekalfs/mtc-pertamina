<!DOCTYPE html>
<html>
<head>
    <title>Instagram Data</title>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
    <script>
        window.onload = function () {
            var dataPoints = [];
            @foreach($data['data'] as $item)
                dataPoints.push({ label: "{{ $item['caption'] }}", y: Math.floor(Math.random() * 100) });
            @endforeach

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Instagram Data"
                },
                axisY: {
                    title: "Value"
                },
                data: [{
                    type: "column",
                    yValueFormatString: "#,##0.## units",
                    dataPoints: dataPoints
                }]
            });
            chart.render();
        }
    </script>
</body>
</html>
