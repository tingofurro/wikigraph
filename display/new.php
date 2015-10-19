<!DOCTYPE html>
<?php
include_once('createJsonTree.php');
generateTree(0,2);
?>
<html>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?php echo getRealRoot(); ?>css/tree.css" />
    <body>
        <div id="treeLocation"><?php echo getRealRoot(); ?>tree.json</div>
        <div id="webroot"><?php echo $realRoot; ?></div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo getRealRoot(); ?>JS/colors.js"></script>
        <script type="text/javascript" src="<?php echo getRealRoot(); ?>JS/tree.js"></script>
        <iframe src="<?php echo $realRoot; ?>/0" id="graph"></iframe>
    </body>
    <?php
    $cNames = array(); $cNames[0] = ''; $lastParent = 0; $i = 0;
    $c = mysql_query("SELECT * FROM cluster ORDER BY id");
    while($cl = mysql_fetch_array($c)) {
        if($cl['parent'] != $lastParent) {$lastParent = $cl['parent']; $i = 0;}
        if($cl['parent']==0) $cNames[$cl['id']] = $i."";
        else $cNames[$cl['id']] = $cNames[$cl['parent']].",".$i;
        ?>
        <div class="hide" id="iList<?php echo $cl['id']; ?>"><?php echo $cNames[$cl['id']]; ?></div>
        <?php
        $i ++;
    }
    ?>
</html>