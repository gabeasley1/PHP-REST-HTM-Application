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
        <script type='text/javascript' 
            src='/closure-library/closure/goog/base.js'></script>
        <script type='text/javascript'>
            goog.require('goog.History');
            goog.require('goog.Uri');
            goog.require('goog.dom');
            goog.require('goog.dom.forms');
            goog.require('goog.dom.query');
            goog.require('goog.fx.AnimationQueue');
            goog.require('goog.fx');
            goog.require('goog.fx.dom');
            goog.require('goog.history.EventType');
            goog.require('goog.history.Html5History');
            goog.require('goog.math.Coordinate');
            goog.require('goog.net.XhrIo');
            goog.require('goog.style');
            goog.require('goog.ui.Dialog');
        </script>
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
        <script type='text/javascript' src='/js/wizard.js'></script>
    </body>
</html>
