<div class="messages">
   
    <?php

    use App\Core\App;
    /*
    if(isset($_SESSION['errorMessages']) && !empty($_SESSION['errorMessages'])):
        $errors=$_SESSION['errorMessages'];

        foreach($errors as $error){
            echo "<li>$error</li>";
        }

    endif;

    if(isset($_SESSION['successMessages']) && !empty($_SESSION['successMessages'])):
        $successes=$_SESSION['successMessages'];

        foreach($successes as $success){
            echo "<li>$success</li>";
        }

    endif;

    unset($_SESSION['errorMessages']);
    unset($_SESSION['successMessages']);
    */
    if(isset($_SESSION['sim-messages']) && !empty($_SESSION['sim-messages'])):

        $messages=$_SESSION['sim-messages'];
        if(count($messages)>0){
        
            echo "<ul class='messages'>";
        
            foreach($messages as $message){
        
                if($message[1]==1){
                    
                    $type=App::get('config')['message']['successColor'];
                    //$sign='glyphicon glyphicon-ok-sign';
                }else{
                
                    $type=App::get('config')['message']['errorColor'];
                    //$sign='glyphicon glyphicon-exclamation-sign';
                
                }
                //echo "<span class='$sign' aria-hidden='true'></span>";
                echo "<li class='message' style='background-color:".$type."'>".$message[0]."</li>";
            }
        
            echo "<ul>";
        
        }

    endif;

    unset($_SESSION['sim-messages']);


    ?>
    
</div>