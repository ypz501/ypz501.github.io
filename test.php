<?php include "../inc/dbinfo.inc"; ?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<title>旅遊行程管理系統</title>
<style>
  /* 基本樣式 */
  body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 0;
    padding: 0;
  }
  .container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
  }
  h1 {
    text-align: center;
    color: #4CAF50;
    font-size: 2em;
  }
  form {
    background-color: #ffffff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  }
  .label-style {
    font-family: Arial, "微軟正黑體", sans-serif;
    line-height: 1.5;
    font-size: 16px;
    color: #333;
    white-space: nowrap; /* 防止自動換行 */
  }
  label {
    display: inline-block;
    width: 120px;
    font-weight: bold;
    margin-bottom: 10px;
  }
  input[type="text"], input[type="number"] {
    width: calc(100% - 140px);
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  input[type="submit"], button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  input[type="submit"]:hover, button:hover {
    background-color: #45a049;
  }
  .error {
    color: red;
    font-weight: bold;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    overflow: hidden;
  }
  table, th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  th {
    background-color: #4CAF50;
    color: white;
  }
  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
  .actions a {
    text-decoration: none;
    color: #4CAF50;
    font-weight: bold;
    margin: 0 5px;
  }
  .actions a:hover {
    text-decoration: underline;
  }
  .search-bar {
    margin-bottom: 20px;
  }
  .search-bar input[type="text"] {
    width: calc(100% - 100px);
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  .search-bar button {
    width: 80px;
  }
</style>
</head>
<body>

<div class="container">
<h1>旅遊行程管理系統</h1>

<?php
  // 連線資料庫並檢查
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
  if (mysqli_connect_errno()) echo "無法連接到 MySQL: " . mysqli_connect_error();
  $database = mysqli_select_db($connection, DB_DATABASE);
  VerifyLocationsTable($connection, DB_DATABASE);

  // 處理新增資料，僅當未在編輯模式時才執行
  if (isset($_POST['submit']) && !isset($_POST['edit_id'])) {
      $location_name = htmlentities($_POST['名稱'] ?? '');
      $location_description = htmlentities($_POST['描述'] ?? '');
      $location_visit_time = htmlentities($_POST['拜訪時間'] ?? '');

      if (!empty($location_name) && !empty($location_visit_time) && $location_visit_time >= 0) {
          AddLocation($connection, $location_name, $location_description, $location_visit_time);
      } else {
          echo "<p class='error'>請確認所有欄位已正確填寫，且拜訪時間為非負數。</p>";
      }
  }

  // 處理刪除
  if (isset($_GET['delete_id'])) {
      $delete_id = (int)$_GET['delete_id'];
      DeleteLocation($connection, $delete_id);
      header("Location: SamplePage.php"); // 刪除後重新載入頁面
      exit;
  }

  // 處理編輯
  if (isset($_GET['edit_id'])) {
      $edit_id = (int)$_GET['edit_id'];
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
          $new_name = htmlentities($_POST['名稱']);
          $new_description = htmlentities($_POST['描述']);
          $new_visit_time = htmlentities($_POST['拜訪時間']);

          if (!empty($new_name) && !empty($new_visit_time) && $new_visit_time >= 0) {
              UpdateLocation($connection, $edit_id, $new_name, $new_description, $new_visit_time);
              header("Location: SamplePage.php"); // 更新後重新載入頁面
              exit;
          } else {
              echo "<p class='error'>請確認所有欄位已正確填寫，且拜訪時間為非負數。</p>";
          }
      }

      $edit_query = "SELECT * FROM locations WHERE id = $edit_id";
      $edit_result = mysqli_query($connection, $edit_query);
      $edit_data = mysqli_fetch_assoc($edit_result);
      ?>

      <!-- 編輯表單 -->
      <form action="" method="POST">
          <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
          <label for="名稱">地點名稱：</label>
          <input type="text" name="名稱" value="<?php echo htmlspecialchars($edit_data['name']); ?>" maxlength="45" required><br>

          <label for="描述">描述：</label>
          <input type="text" name="描述" value="<?php echo htmlspecialchars($edit_data['description']); ?>" maxlength="90"><br>

          <label for="拜訪時間" class="label-style">拜訪時間（分鐘）：</label>
          <input type="number" name="拜訪時間" value="<?php echo htmlspecialchars($edit_data['visit_time']); ?>" min="1" required><br>

          <input type="submit" name="update" value="更新地點">
          <a href="SamplePage.php"><button type="button">取消編輯</button></a>
      </form>
      <?php
      exit;
  }
?>
<!--<img src="雲端運算圖1.png" alt="誰來規劃行程的梗圖" style="display: block; margin: 0 auto; max-width: 100%; height: auto;">-->
<!-- 搜尋欄位 -->
<div class="search-bar">
  <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="GET">
      <input type="text" name="search" placeholder="搜尋地點名稱">
      <button type="submit">搜尋</button>
  </form>
</div>

<!-- 新增地點表單 -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <label for="名稱">地點名稱：</label>
  <input type="text" name="名稱" maxlength="45" required><br>

  <label for="描述">描述：</label>
  <input type="text" name="描述" maxlength="90"><br>

  <label for="拜訪時間" class="label-style">拜訪時間（分鐘）：</label>
  <input type="number" name="拜訪時間" min="1" required><br>

  <input type="submit" name="submit" value="新增地點">
</form>

<!-- 顯示資料表 -->
<table>
  <tr>
    <th>編號</th>
    <th>地點名稱</th>
    <th>描述</th>
    <th>拜訪時間 (分鐘)</th>
    <th>操作</th>
  </tr>

<?php
  // 查詢地點資料，支援搜尋功能
  $search = isset($_GET['search']) ? htmlentities($_GET['search']) : '';
  $query = "SELECT * FROM locations";
  if (!empty($search)) {
      $query .= " WHERE name LIKE '%$search%'";
  }
  $result = mysqli_query($connection, $query);

  while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>" . $row['id'] . "</td>";
      echo "<td>" . htmlspecialchars($row['name']) . "</td>";
      echo "<td>" . htmlspecialchars($row['description']) . "</td>";
      echo "<td>" . htmlspecialchars($row['visit_time']) . "</td>";
      echo "<td class='actions'>
                <a href='?edit_id=" . $row['id'] . "'>編輯</a> |
                <a href='?delete_id=" . $row['id'] . "'>刪除</a>
            </td>";
      echo "</tr>";
  }
?>

</table>
</div>

<?php
  /* 釋放資料庫連線 */
  mysqli_close($connection);

  /* 新增資料函數 */
  function AddLocation($connection, $name, $description, $visit_time) {
      $n = mysqli_real_escape_string($connection, $name);
      $d = mysqli_real_escape_string($connection, $description);
      $query = "INSERT INTO locations (name, description, visit_time) VALUES ('$n', '$d', '$v');";
      mysqli_query($connection, $query);
  }

  /* 刪除資料函數 */
  function DeleteLocation($connection, $id) {
      $query = "DELETE FROM locations WHERE id = $id;";
      mysqli_query($connection, $query);
  }

  /* 更新資料函數 */
  function UpdateLocation($connection, $id, $name, $description, $visit_time) {
      $n = mysqli_real_escape_string($connection, $name);
      $d = mysqli_real_escape_string($connection, $description);
      $v = mysqli_real_escape_string($connection, $visit_time);
      $query = "UPDATE locations SET name = '$n', description = '$d', visit_time = '$v' WHERE id = $id;";
      mysqli_query($connection, $query);
  }

  /* 確認資料表 */
  function VerifyLocationsTable($connection, $dbName) {
      $query = "CREATE TABLE IF NOT EXISTS locations (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT,
          name VARCHAR(45),
          description VARCHAR(90),
          visit_time INT UNSIGNED,
          PRIMARY KEY (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      mysqli_query($connection, $query);
  }
?>
</body>
</html>
