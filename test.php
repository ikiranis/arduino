<?php

    if(isset($_POST["submit"])) // Αν πατηθει το κουμπί
    {
        // τρέξε όλα αυτά
        $fname=$_POST["fname"];
        $lname=$_POST["lname"];
        echo $fname.' '.$lname;

        $one=1;
        $two=2;
        $three=3;
        $four=4;
        $five=5;

        echo $one+$two+$three+$four+$five;

    }



?>

<html>

    <head>
        <title>Τεστ</title>
    </head>

    <body>
        <form method="POST" action="test.php">
            
            <p>Δώσε το όνομα σου: <input type="text" name="fname"></p>
            <p>Δώσε το επώνυμο σου: <input type="text" name="lname"></p>
            <p>
                <input type="button" value="1" name="one">
                <input type="button" value="2" name="two">
                <input type="button" value="3" name="three">
                <input type="button" value="4" name="four">
                <input type="button" value="5" name="five">
            </p>


            <input type="submit" name="submit" value="Μίλα ρε!">

        </form>

    </body>



</html>

