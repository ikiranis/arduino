<?php
/**
 * File: MainPage.php
 * Created by rocean
 * Date: 20/05/16
 * Time: 18:31
 */


function DisplayMainPage() {

    if(isset($_GET['page'])) {
        $NavActiveItem=$_GET['page'];
        Page::setNavActiveItem($_GET['page']);

    }
    else if(isset($_COOKIE['page'])) {
            $NavActiveItem=$_COOKIE['page'];
            Page::setNavActiveItem($_COOKIE['page']);
        }


    ?>
    

        <section>
            <article>
            <?php
                switch ($NavActiveItem) {
                    case 1: Arduino::showDashboard(); break;
                    case 2: Arduino::showTemperatures(); break;
                    case 3: Arduino::showPower(); break;
                    case 4: Arduino::showStatistics(); break;
                    case 5: Arduino::showConfiguration(); break;
                    case 6: Arduino::showLogs(); break;
                }

            ?>
            </article>
        </section>


        <nav>
            <?php echo Page::NavList($NavActiveItem); ?>
        </nav>


 
    
    <?php
}