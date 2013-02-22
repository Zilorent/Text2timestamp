<?php
 if (isset($_POST['get'])){
    include("text2timestamp.php"); 
    $time_ = new text2timestamp($_POST['time']);
    
    if ($time_->is_error_message()){
        echo $time_->get_error_message();
    }
    else{
        $time=$time_->get_timestamp();
        $action=$time_->get_action(); 
        echo date('H:i:s Y/m/d',$time).' - '.$time.' - '.$action.'<br/>';     
    }
 }
 else{
     ?>
        <form method="post">
            Tell time
            <input type="text" name="time">
            <input type="submit" name="get" value="get time"> 
        </form>
     <?php
 }
?>