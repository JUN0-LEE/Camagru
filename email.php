<?php

function send_email($email, $hash) {
    $to = $email;
    $subject = 'Signup | Verification';
    $message = "
    
    Thanks for signing up!
    Your account has been created, you can login after you have activated your account by pressing the url below.
    
    Please click this link to activate your account:
    http://$_SERVER[HTTP_HOST]/verify.php?email=$email&hash=$hash

    ";
    $headers = "From:noreply@$_SERVER[HTTP_HOST]\r\n";
    $mail = mail($to, $subject, $message, $headers);
    if (!$mail) {
        echo "Mail sending failed";
    }
}

function reset_email($email, $new_password) {
    $to = $email;
    $subject = 'Profile | Password reset';

    $message = "
    
    Your new password : $new_password

    Please click this link to login your account:
    http://$_SERVER[HTTP_HOST]/login.php

    ";

    $headers = "From:noreply@$_SERVER[HTTP_HOST]\r\n";
    $mail = mail($to, $subject, $message, $headers);
    if (!$mail) {
        echo "Mail sending failed";
    }
}

function comment_email($email, $from) {
    $to = $email;
    
    $subject = 'Notification | Comment';

    $message = "
    
    $from has commented your post!
    
    ";
    $headers = "From:noreply@$_SERVER[HTTP_HOST]\r\n";
    $mail = mail($to, $subject, $message, $headers);
    if (!$mail) {
        echo "Mail sending failed";
    }
}
?>