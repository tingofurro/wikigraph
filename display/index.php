<!DOCTYPE html>
<?php
include_once('createJsonTree.php');
$dbPrefix = 'ma_';
if(isset($_GET['dbPrefix'])) $dbPrefix = $_GET['dbPrefix'];
$topic = substr($dbPrefix, 0, -1);
generateTree(0,2, $dbPrefix);
?>
<html>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="<?php echo getRealRoot(); ?>css/index.css" />
    <body>
        <div id="treeLocation"><?php echo getRealRoot(); ?>tree.json</div>
        <div id="webroot"><?php echo $realRoot; ?></div>
        <div id="dbPrefix" class="hide"><?php echo $topic; ?></div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo getRealRoot(); ?>JS/colors.js"></script>
        <script type="text/javascript" src="<?php echo getRealRoot(); ?>JS/tree.js"></script>
        <div id="selectTopic">
            <select onchange="window.location='<?php echo getRoot(); ?>'+this.value;">
                <option value="ma" <?php echo ("ma"==$topic)?"selected":""; ?>>Mathematics</option>
                <option value="cs" <?php echo ("cs"==$topic)?"selected":""; ?>>Computer Science</option>
                <option value="ee" <?php echo ("ee"==$topic)?"selected":""; ?>>Electrical Engineering</option>
                <!-- <option value="med" <?php echo ("med"==$topic)?"selected":""; ?>>Medicine</option> -->
            </select><br />
            Click on a subtopic below to see sub-subtopics.
        </div>
        <iframe src="<?php echo getRoot().$topic; ?>/0" id="graph"></iframe>
    </body>
    <?php
    $cNames = array(); $cNames[0] = ''; $parents = array();
    $c = mysql_query("SELECT * FROM ".$dbPrefix."cluster ORDER BY level, score DESC");
    while($cl = mysql_fetch_array($c)) {
        if(!isset($parents[$cl['parent']])) {$i = 0; $parents[$cl['parent']] = array();}
        else $i = count($parents[$cl['parent']]);
        array_push($parents[$cl['parent']], $cl['id']);

        if($cl['parent']==0) $cNames[$cl['id']] = $i."";
        else $cNames[$cl['id']] = $cNames[$cl['parent']].",".$i;
        ?>
        <div class="hide" id="iList<?php echo $cl['id']; ?>"><?php echo $cNames[$cl['id']]; ?></div>
        <?php
        $i ++;
    }
    ?>
</html>