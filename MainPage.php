<?php
/**
 * File: MainPage.php
 * Created by rocean
 * Date: 20/05/16
 * Time: 18:31
 */


// Πέρασμα της INTERVAL_VALUE στην javascript
?>
    <script type="text/javascript">
        var IntervalValue=<?php echo INTERVAL_VALUE; ?>;
    </script>

<?php




function DisplayMainPage() {

    if(isset($_GET['page'])) {
        $NavActiveItem=$_GET['page'];
        Page::setNavActiveItem($_GET['page']);

    }
    else if(isset($_COOKIE['page'])) {
            $NavActiveItem=$_COOKIE['page'];
            Page::setNavActiveItem($_COOKIE['page']);
        }

    if(!isset($NavActiveItem)) $NavActiveItem=1;

    global $lang;

    $languages_text=$lang->print_languages('lang_id',' ',true,false);
    
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
            <div id="languages">
                <?php echo $languages_text; ?>
            </div>
            <?php echo Page::NavList($NavActiveItem); ?>
            <div id="MysqlStatus">
                <span></span><?php echo __('sensors_status'); ?></span> <span id="MysqlStatusText"></span>
            </div>
        </nav>


 
    
    <?php
}