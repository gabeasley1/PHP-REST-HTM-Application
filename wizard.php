<!DOCTYPE html>
<?
require('classes.php');
$page = 1;
if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
}
?>
<html>
    <head>
        <title>Wizard Example</title>
        <link type='text/css' rel='Stylesheet' 
              href='/css/custom-theme/wizard.css' />
    </head>
    <body>
        <div class='content'>
                <?
                $next = Wizard::hasNext($page);
                $prev = Wizard::hasPrev($page);
                ?>

                <? if ($prev): ?>
            <div class='left-page'>
                <? else: ?>
            <div class='left-page' style='visibility: hidden;'>
                <? endif; ?>
                <!--<a id='page-prev' href='#'></a>-->
            </div>
                <? if ($next): ?>
            <div class='right-page'>
                <? else: ?>
            <div class='right-page' style='visibility: hidden;'>
                <? endif; ?>
                <!--<a id='page-next' href='#'></a>-->
            </div>
            <div class='page current-page'>
                <div class='page-wrapper'>
                    <div class='page-content'>
                        <?= Wizard::getPage($page); ?>
                    </div>
                </div>
            </div>
        </div>
        <div id='page-number' style='display:none;'><?=$page?></div>
        <script type='text/javascript' src='/js/wizard.min.js'></script>
    </body>
</html>
