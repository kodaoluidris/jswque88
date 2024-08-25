<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
        #loading {
            display: block;
            font-size: 24px;
            text-align: center;
            margin-top: 50px;
        }
        #continue-button {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div id="loading">Loading, please wait...</div>
    <div id="continue-button">
        <form action="fetch.php" method="GET">
            <input type="hidden" name="key" value="<?php echo htmlspecialchars($_GET['key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Continue</button>
        </form>
    </div>

    <script>
        setTimeout(function() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('continue-button').style.display = 'block';
        }, 5000); // 5-second delay
    </script>
</body>
</html>
