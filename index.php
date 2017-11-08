<?php if(!isset($_SESSION)) session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Akses dan Manipulasi Data</title>
<style>
table.datamahasiswa{ border-collapse: collapse; }
table.datamahasiswa td,th{
    border: 1px solid black;
    padding:5px;
}
table.datamahasiswa tr:nth-child(even){
	background:#eee;
}
.boldtext{
    font-weight: bold;
    font-size:1.1em;
}
</style>
</head>
<body>
<?php
if(!isset($_SESSION['admindata']) || $_SESSION['admindata'] == 0){
    ?>
    <h2>Login</h2>
<form method="post">
    <input type="text" name="user" id="user" placeholder="Username">
    <input type="password" name="pass" id="pass" placeholder="Password">
    <button type="submit" name="submit">Submit</button>
</form>
<?php
    if(isset($_POST['submit'])){
        $username = "admin";
        $password = "admin";
        if($_POST['user']==$username&&$_POST['pass']==$password){
            $_SESSION['admindata']=1;
            $_SESSION['name']="Admin";
            ?>
            <script> location.replace("<?php echo $_SERVER['PHP_SELF'].'?act=view';?>"); </script>
            <?php
        }else{
            $_SESSION['admindata']=0;
            echo "Maaf, username atau password salah!<br/>Default user : admin | password : admin ";
        }
    }
}
else{
	require_once 'koneksi.php';
	require_once 'data_handlersendiri.php';
}
?>
</body>
</html>