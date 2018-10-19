<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>ToDo:]- Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" /><![endif]-->
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/vieworder.css" rel="stylesheet" type="text/css" />
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <!-- END: load jquery -->
        <script type="text/javascript" src="js/table/jquery.dataTables.min.js"></script>
        <body>
            <?php
            //make sure they are logged in and activated.
            require_once('inc.functions.php');
            secure();
            ?>



            <div class="container_12">
                <?php
                require_once('inc.header.php');
                ?>
                <div class="grid_4">
                    <div class="box round first fullpage">
                        <h2>Categories</h2>

                        <div class="block">
                            <ul>
                                <li><span class="inlineblock ui-icon ui-icon-plus"></span><a href="categories.php?parent_id=0"> add</a></li>
                                <?php
                                include "include/db.php";
                                $db = new db('boostpr1_boostpromotions');

                                if (isset($_GET['cPath'])) {
                                    $cPath = (int) $_GET['cPath'];
                                } else {
                                    $cPath = 0;
                                }
                                //MAKE TREEVIEW OF CATEGORIES
                                $categories = $db->find('all', "zen_categories_description a 
                                    INNER JOIN zen_categories b 
                                    ON a.categories_id = b.categories_id ", '1=1 
                                    ORDER BY b.parent_id DESC, b.sort_order ASC, a.categories_name ASC', [
                                        ], "b.parent_id, a.categories_name, a.categories_id, 
                                        (SELECT COUNT(products_id) FROM zen_products_to_categories 
                                            WHERE categories_id = a.categories_id) AS total");


                                $hasactive = false;

                                function buildtree($rows, $parent_id) {
                                    global $cPath, $hasactive;

                                    $branch = [];
                                    foreach ($rows as $row):
                                        if ($row['parent_id'] == $parent_id):
                                            if ($cPath == $row['categories_id']):
                                                $row['active'] = true;
                                                $hasactive = $parent_id;
                                            endif;
                                            $sub = buildtree($rows, $row['categories_id']);
                                            if ($sub):
                                                $row['sub'] = $sub;
                                                if ($hasactive == $row['categories_id']):
                                                    $row['hasactive'] = true;
                                                    $hasactive = $row['parent_id'];
                                                endif;
                                            endif;
                                            $branch[] = $row;
                                        endif;
                                    endforeach;

                                    return $branch;
                                }

                                $tree = buildtree($categories, 0);

                                function genTree($tree) {
                                    global $bread_title;

                                    foreach ($tree as $row):
                                        $actclass = '';
                                        if (isset($row['sub'])):
                                            if (isset($row['hasactive'])):
                                                $bread_title .= $row['categories_name'] . " <span class='inlineblock ui-icon ui-icon-carat-1-e'></span> ";
                                                $actclass = "class='active'";
                                            endif;

                                            echo "<li $actclass ><span class='inlineblock ui-icon ui-icon-folder-collapsed'></span>"
                                            . $row['categories_name']
                                            . "<a href='categories.php?cPath=" . $row['categories_id']
                                            . "'> edit</a><ul><li><span class='inlineblock ui-icon ui-icon-plus'></span><a href='categories.php?parent_id="
                                            . $row['categories_id'] . "'> add</a></li>";
                                            genTree($row['sub']);
                                            echo "</ul></li>";
                                        else:
                                            if (isset($row['active'])) {
                                                $bread_title .= $row['categories_name'];
                                                $actclass = "class='active'";
                                            }

                                            echo "<li><span class='inlineblock ui-icon ui-icon-document'></span>"
                                            . $row['categories_name']
                                            . " <small>("
                                            . $row['total']
                                            . ")</small><a $actclass href='categories.php?cPath="
                                            . $row['categories_id']
                                            . "'> edit</a></li>";
                                        endif;
                                    endforeach;
                                }

                                //function echoes stuff
                                genTree($tree);
                                ?>
                            </ul>

                        </div>
                    </div>
                </div>
                <div class="grid_4" >
                    <div class="box round first">
                        <?php
                        $category = $db->find('first', "zen_categories a
						INNER JOIN zen_categories_description b
						ON a.categories_id = b.categories_id", "a.categories_id=$cPath
                                                ORDER BY a.parent_id");


                        if ($category):
                            ?>
                            <h2>Edit Category # <?= $category['categories_id']; ?> </h2><br/>
                            <form enctype='multipart/form-data' action='category_edit.php?Cid=<?= $category['categories_id']; ?>' method='post'>
                                Categories ID # <?= $category['categories_id']; ?> <br/>
                                Image <input type='file' name='categories_image' /><br/>
                                <?php if ($category['categories_image'] != "") { ?>
                                    <?= $category['categories_image']; ?><br/>
                                    <img src='http://boostpromotions.com/images/<?= $category['categories_image']; ?>' /><br/>
                                <?php } ?>
                                Parent ID<input size='5' type='text' name='parent_id' value='<?= $category['parent_id']; ?>' /><br/>
                                Name<input size='15' type='text' name='categories_name' value='<?= $category['categories_name']; ?>' /><br/>
                                Description<input type='text' name='categories_description' value='<?= $category['categories_description']; ?>' /><br/>
                                Size<input size='5' type='text' name='size' value='<?= $category['size']; ?>' /><br/>
                                Sort Order<input size='5' type='text' name='sort_order' value='<?= $category['sort_order']; ?>' /><br/>
                                Status<input size='5' type='text' name='categories_status' value='<?= $category['categories_status']; ?>' /><br/>
                                <button class='btn btn-blue' name='submitted' type='submit'>update</button>
                                <a onclick='return confirm("Are you sure? THIS CANNOT BE UNDONE!")' href='category_delete.php?categories_id=<?= $category['categories_id']; ?>'>
                                    <button class='btn btn-red' type='button'>Delete</button></a> 
                            </form>
                            <?php
                        endif;

                        if (isset($_GET['parent_id'])) {
                            $new_kid = mysql_real_escape_string($_GET['parent_id']);
                            ?>
                            <h2>New Category in Parent Id <?php echo $new_kid; ?></h2>
                            <form action="category_new.php" method="post" name="categories">
                                Image
                                <input type="file" name="categories_image" placeholder="Categories Image" />
                                <br/>
                                <input type="hidden" name="parent_id" value="<?php echo $new_kid; ?>" />
                                Name
                                <input type="text" name="categories_name" placeholder="Categories Name" />
                                <br/>Description
                                <input type="text" name="categories_description" placeholder="Categories Description" />
                                <br/>Size
                                <input type="text" name="size" placeholder="size" />
                                <br/>Sort Order
                                <input type="text" name="sort_order" placeholder="Sort Order" size="5"/>

                                <input type="hidden" value="1" name="category_status" />
                                <br/><button class="btn btn-green" type="submit" name="submitted">Add Row</button>
                                </tr>
                            </form>
                        <?php } ?>

                    </div>                        
                </div>

                <div class="clear">
                </div>
            </div>
            <div class="clear">
            </div> 
            <div id="site_info">
                <p>
                    Copyright <a href="#">ToDo:]</a>. All Rights Reserved.
                </p>
            </div>
        </body>
</html>
