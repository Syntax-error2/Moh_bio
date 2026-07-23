<?php

 
include('session.php');
include('dbcon.php');
 
if(isset($_POST['addNews']))
{
    $news_title=$_POST['news_title'];
    $news_contents=$_POST['news_contents'];
    $dateTime=date('m/d/Y').' | '.date('h:i:s A');
    $posted_by=$_POST['posted_by'];
    
    $checkbox = $_POST['checkbox'];

    for($i=0;$i<count($checkbox);$i++)
    {
        
    $ipAddress = $checkbox[$i];
    
    $insertStmt = $conn->prepare("INSERT INTO news(news_title, news_contents, dateTime, posted_by, ipAddress) VALUES(:news_title, :news_contents, :dateTime, :posted_by, :ipAddress)");
    $insertStmt->execute([
        ':news_title' => $news_title,
        ':news_contents' => $news_contents,
        ':dateTime' => $dateTime,
        ':posted_by' => $posted_by,
        ':ipAddress' => $ipAddress,
    ]);
    
    }
?>

<script> window.location='list_news.php'; </script>

<?php } ?>
 
 
 
<?php
 
if(isset($_POST['editNews']))
{
     
 
    $news_id = $_GET['news_id'] ?? '';
    $updateStmt = $conn->prepare("UPDATE news SET news_title = :news_title, news_contents = :news_contents, posted_by = :posted_by, ipAddress = :ipAddress WHERE news_id = :news_id");
    $updateStmt->execute([
        ':news_title' => $_POST['news_title'],
        ':news_contents' => $_POST['news_contents'],
        ':posted_by' => $_POST['posted_by'],
        ':ipAddress' => $_POST['ipAddress'],
        ':news_id' => $news_id,
    ]);

?>

<script> window.location='list_news.php'; </script>

<?php } ?>




 
<?php
 
if(isset($_POST['deleteNews']))
{

    $news_id = $_GET['news_id'] ?? '';
    $deleteStmt = $conn->prepare("DELETE FROM news WHERE news_id = :news_id");
    $deleteStmt->execute([':news_id' => $news_id]);

?>

<script> window.location='list_news.php'; </script>

<?php } ?>

