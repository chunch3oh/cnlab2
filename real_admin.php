<?php

session_start();

// 名字非superuser的用戶進來會顯示Superuser Only
if ($_SESSION['login_user'] != 'superuser') {
    die("Superuser Only" . "<br><a href='login.php'>使用其他帳號登入</a>");
}

// connect to data case
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "localhost";
$user = "radius";
$password = "radpass";
$database = "radius";
$db = mysqli_connect($host, $user, $password, $database);
if(mysqli_connect_errno())
{
	echo "FAILED TO CONNECT TO MySQL: " . mysqli_connect_error();
}

// get the userlist


$sql = "SELECT distinct(username) FROM radcheck";
$user_list = mysqli_query($db, $sql);
$number_of_users = mysqli_num_rows($user_list);

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	session_destroy();
	header('location: login.php');
}

?>


<html>

<head>
	<script language="javascript">
	function jump_to_user(username) {
		// do something;
		console.log(username);
		var url = "real_admin_edit.php?username=" + username;
		window.location.assign(url);
	}
	</script>
</head>

<body>

<h1>Hello!</h1>
<h1>Dear <?php echo $_SESSION['login_user']?></h1>

<?php
print "there are ";
print $number_of_users;
print " users.<br/><br />";
?>

<div>
	<table border=1>
		<td>Username</td>
		<td>Hourly Flow Limit</td>
		<td>Daily Session Limit</td>
		<td>Modify</td>
<!-- show user list -->
<?php
for($count=0; $count<$number_of_users; $count++)
{
	$tmp = mysqli_fetch_array($user_list, MYSQLI_ASSOC);
	$tmp_user_name = $tmp['username'];
	$sql = "SELECT * FROM radcheck WHERE username='$tmp_user_name'";
	$result = mysqli_query($db, $sql);
	// initialization
	$hourly_limit = "-1";
	$daily_limit = "-1";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($row['attribute'] == "Max-Hourly-Traffic")
		{
			$hourly_limit = $row['value'];
		}
		else if($row['attribute'] == "Max-Daily-Session")
		{
			$daily_limit = $row['value'];
		}
	}
	print "<tr>";
	print "<td>" . $tmp_user_name . "</td>";
	print "<td>" . $hourly_limit . "</td>";
	print "<td>" . $daily_limit . "</td>";
	print "<td><input type='button' onclick='jump_to_user(\"$tmp_user_name\");' name='$tmp_user_name' value='Edit'></td>";
}
print "<tr>";
?>
<!-- show user list -->
	</table>
	<FORM action="" method="POST">
	<input type='submit' name='logout' value='Log Out'>
	</FORM>
</div>
</body>
</html>
