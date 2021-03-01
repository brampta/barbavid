<?php
include('../includes/init.php');

if(isset($_GET['code'])){
    $user_validation = $user->validate($_GET['code']);
}

include('../templates/head_start.php');
include('../templates/head_end.php');
include('../header.php');
?>

<?php
if(isset($user_validation) && $user_validation['success']==true){
    ?>
    <p>successfully validated email</p>
    <?php
}else{
    if(isset($user_validation) && in_array('invalid_code',$user_validation['errors'])){
        ?>
        <p>invalid code</p>
        <?php
    }

}

include('../templates/page_end.php');