<!-- Bearsnet Webserver Homepage
     Coded By: Britton Kjenner 
     Last Updated: 5/9/2013 --!>
<?php session_start() ?>
<html>
<head>
<title>Bearsnet Webserver Homepage</title>
</head>
<body>
<script type="text/javascript">
function inputFocus(i){
    if(i.value == i.defaultValue){
        i.value="";
        i.style.color="#000";
    }
}
function inputBlur(i){
    if(i.value==""){
        i.value=i.defaultValue;
        i.style.color="#888";
    }
}
//Masks the input into the password field
function makePass(i){
    if(i.value == i.defaultValue){
        i.type="password";
        i.value="";
        i.style.color="#000";
    }
}
function blurPass(i){
    if(i.value==""){
        i.type="text";
        i.value="Password";
        i.style.color="#888";
    }
}
</script>
<?php
    if (!isset($_SESSION["authorized"])){
    //If the user is not yet authorized, prompt with a login page
    echo "<center>";
    echo "<h1>Bearsnet Webserver Homepage</h1>";
    echo "<form action='index.php' method='post'>";
    echo "<input type='text' name='username' value='Username' onfocus='inputFocus(this)' onblur='inputBlur(this)'/>";
    echo "<input type='text' name='pwd' value='Password' onfocus='makePass(this)' onblur='blurPass(this)'/>";
    echo "<input type='submit' value='Login'/>";
    echo "</form>";
    echo "</center>";

    $validUser = FALSE;    
    if (isset($_POST["username"])){//Only run if form was submitted
        //Connect to mySQL server
        $conn = mysql_connect("localhost","root","PasswordGoesHere");
        if(!$conn){//check if connection was successful
            die("Could not connect:" . mysql_error());
        }
        $db = mysql_select_db("login_info",$conn);//Select Database
        if(!$db){//Check if successful
            die("Can't use database:" . mysql_error());
        }
        
        //First determine if user id is valid before submitting the query
        //This prevents SQL injection from happening
        $query = "SELECT userID FROM users";
        $result = mysql_query($query);
        while($user = mysql_fetch_array($result)){
            if (strtolower($user[0]) == strtolower($_POST["username"])){
               $validUser = TRUE;
               break;
            }
        }
        
        echo "<center>";
        
        //If username is found in the database, check if password is correct
        if ($validUser){
            $query = "SELECT *
                      FROM users
                      WHERE userID = '" . $_POST["username"] . "'
                      AND password = AES_ENCRYPT('" . $_POST["pwd"] . "','EncryptionKeyGoesHere')";
            $result = mysql_query($query);

            if(mysql_num_rows($result) == 1){
                echo "Login Successful!";
                //Set session data
                $_SESSION["authorized"] = true;
                $_SESSION["username"] = $_POST["username"];
                //And reload page to display authorized version
                header("Location: index.php");
            }else{
                //Wrong password
                echo "Login Failed!";
            }
        }else{
            //User ID is not found in database
            echo "Invalid user ID";
        }
        echo "</center>";

        //close connection to server
        mysql_close($conn);
    }
    }else{
        //Authenticated Users recieve this page
        echo "<center>";
        echo "<a href='logout.php'>Logout</a>";
        echo "</center>";       
    }
?>
</body>
</html>
