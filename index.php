<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table Annotator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Table Annotator</h1>
    <form id="upload-form" action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Choose file to upload (CSV, XLS, XML, JSON):</label>
        <input type="file" name="file" id="file">
        <input type="submit" value="Upload">
    </form>

    <div id="table-container"></div>

    <div id="actions">
        <button onclick="performAction('filter')">Filter</button>
        <button onclick="performAction('join')">Join</button>
        <button onclick="performAction('union')">Union</button>
        <button onclick="exportTable()">Export</button>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
