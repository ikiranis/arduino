<?php

/**
 * File: page.php
 * Created by rocean
 * Date: 17/04/16
 * Time: 01:17
 * HTML Page Elements Class
 */


class Page
{
    private $tittle;
    private $meta = array();
    private $script = array();

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    function showHeader()
    {

        ?>

        <HTML>
        <head>
            <title><?php echo $this->tittle; ?></title>

            <?php
            //  Καθορισμός των meta. Ζητάει το string μετά το "<meta "
            if (isset($meta))
                foreach ($this->meta as $a) {
                    echo "<meta " . $a . ">";
                }
            ?>

            <?php
            //  Καθορισμός των scripts αρχείων. Ζητάει το string μετά το "<script "
            if (isset($script))
                foreach ($this->script as $a) {
                    echo "<script " . $a . "></script>";
                }
            ?>

        </head>

        <BODY>

        <?php
    }

// Δέχεται array από strings ή σκέτο string
    function setMeta($meta)
    {

        if (is_array($meta)) {
            foreach ($meta as $item) {
                $this->meta[] = $item;
            }
        } else $this->meta[] = $meta;
    }

// Δέχεται array από strings ή σκέτο string
    function setScript($script)
    {
        if (is_array($script)) {
            foreach ($script as $item) {
                $this->script[] = $item;
            }
        } else $this->script[] = $script;
    }

    function showFooter()
    {
        ?>
        </BODY>
        </HTML>

        <?php
    }


// Function δημιουργίας φόρμας    
// Δέχεται τιμές όταν καλείται με αυτόν τον τρόπο
// $FormElementsArray= array (
//      array('name' => 'email', 'fieldtext' => 'E-mail', 'type' => 'text'),
//      array('name' => 'password', 'fieldtext' => 'Password', 'type' => 'password')
// );


    function MakeForm($action, $form_elements)
    {
        ?>
        <form method="POST" action="<?php echo $action; ?>">
            <?php
            foreach ($form_elements as $item) {
                ?>

                <label for="<?php echo $item['name']; ?>"><?php echo $item['fieldtext']; ?></label>
                <input type="<?php echo $item['type']; ?>" name="<?php echo $item['name']; ?>" value="<?php echo $item['value']; ?>">

                <?php
            }
            ?>

        </form>

        <?php
    }


    function SetSessionTimeout()
    {
        session_start();

        

        $timeout = 60*30; // Number of seconds until it times out. 30 minutes

        // Check if the timeout field exists.
        if (isset($_SESSION['timeout'])) {
            // See if the number of seconds since the last
            // visit is larger than the timeout period.
            $duration = time() - (int)$_SESSION['timeout'];
            if ($duration > $timeout) {
                // Destroy the session
                session_unset();
                session_destroy();
                session_write_close();
            }
            else $_SESSION['timeout'] = time();   // Update the timout field with the current time.
        }
        else echo "Δεν είσαι logged in";


    }


}