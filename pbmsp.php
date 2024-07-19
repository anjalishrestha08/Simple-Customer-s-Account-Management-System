<?php
session_start();
include("connection.php");

if(isset($_POST['Login'])) {
    $email = checkval($_POST['email']);
    $password = checkval($_POST['Password']);

    $select = "SELECT Email,Password FROM user WHERE Email=? AND Password=?";
    $stmt = mysqli_prepare($con, $select);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
	$row= mysqli_fetch_assoc($result);
	echo "EmaiL ".$row["Email"];

    if(mysqli_num_rows($result) > 0 && $row["Email"]==$email && $row["Password"]==$password) {
        $_SESSION['email'] = $email;
        header("Location: udb.php");
        exit;
    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
        echo "<script>window.location.href = 'bms.html';</script>";
        exit;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function checkval($v){
	$data = "";
	if(strlen($v)>0){
		$data = trim($v);
		$data = stripcslashes($data);
		$data = htmlspecialchars($data);
		return $data; 
}else{
	return $data; 
}
}
?>
