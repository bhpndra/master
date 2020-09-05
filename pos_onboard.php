<?
 
 echo "POS Testing";
//000405500246

//000505022546

//000605025881

//000705039262
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<body>
    
    <?php 
    $num=" ";

    for ($i = 0; $i<14; $i++) 
    {
       
        $num .= mt_rand(0,9);
        
        
    }
    
    // echo $num;
    $request="NTPPOS$num";
    $id=str_replace(" ","","$request");
    // echo $id;
    ?>
    <?php 
        include('include/connect.php');
        if(isset($_POST['add']))
        {
            // echo "source";
            // die();
            $source = $_POST['source'];
            $key = $_POST['key'];
            $request = $_POST['request'];
            $created=date('Y-m-d H:i:s');
            $query = mysqli_query($conn,"insert into `pos_request`(`source`,`pos_key`,`request_id`,`created_on`) values ('$source','$key','$request','$created')");
            
        }
        ?>
    <form method="post" action="">
        <input type="hidden" name="source" value="NTP">
        <input type="hidden" name="key" value="258694652">
        <input type="hidden" name="request" value="<?=$id?>">
        <button type="submit" id="submit" name="add" class="btn btn-primary"><a target="_blank" href="https://eazypaymbluat.icicibank.com/POSService/#/landing?source=NTP&key=258694652&requestID=<?=$id?>">POS Onboarding</a></button>
    </form>
     
</body>
</html>
  


















