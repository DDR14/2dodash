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
        <link rel="stylesheet"no type="text/css" href="css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" /><![endif]-->
        <link href="css/table/demo_page.css" rel="stylesheet" type="text/css" />
        <!--Jquery UI CSS-->        
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/vieworder.css" rel="stylesheet" type="text/css" />       
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery-ui/jquery-ui.min.js"></script>
        <script src="js/table/jquery.dataTables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/table/dataTables.select.min.js"></script>
        <script type="text/javascript" src="js/table/dataTables.buttons.min.js"></script>
        <!-- END: load jquery -->
        <script src="js/setup.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                setDatePicker('ship_by');
                setDatePickerOrder('pick_date');
                setSidebarHeight();

                $("#popup").dialog({autoOpen: false, modal: true});
                $("#popup2").dialog({autoOpen: false, modal: true});
                $("a.zframe").on("click", function (e) {
                    e.preventDefault();
                    var action = $(this).attr("data-action");
                    var title = $(this).attr("data-title");
                    var popid = $(this).attr("data-popid");

                    var qty = $(this).attr("data-qty");
                    var pid = $(this).attr('data-pid');
                    if (qty) {
                        $("#qty").val(qty);
                        $("#pid").val(pid);
                    }

                    $("#" + popid + " form").attr("action", action);
                    $("#" + popid).dialog("option", "title", title).dialog("open");
                    return false;
                });
            });
            $(function () {
                var iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
                var dialog = $("<div></div>").append(iframe).appendTo("body").dialog({
                    autoOpen: false,
                    modal: true,
                    resizable: false,
                    width: "auto",
                    height: "auto",
                    close: function () {
                        iframe.attr("src", "");
                    }
                });
                $("a.xframe").on("click", function (e) {
                    e.preventDefault();
                    var src = $(this).attr("href");
                    var title = $(this).attr("data-title");
                    var width = $(this).attr("data-width");
                    var height = $(this).attr("data-height");
                    iframe.attr({
                        width: +width,
                        height: +height,
                        src: src
                    });
                    dialog.dialog("option", "title", title).dialog("open");
                });
            });
        </script>
        <style>
            tr.group,
tr.group:hover {
    background-color: #ddd !important;
    text-align: center;
}
</style>
    </head>
    <body>

        <?php
        //make sure they are logged in and activated.
        require_once('inc.functions.php');
        connectToSQL();
        secure();
        mysql_close();
        ?>

        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>
            <div class="grid_2">
                <div class="box sidemenu">
                    <?php
                    require_once 'inc.sidebar.php';
                    ?>
                </div>
            </div>
            <div class="grid_10">
                <div class="first grid">
                    <?php 
                    connectToSQL();
                    $xmethod = isset($_GET['method'])?$_GET['method']:'';
                    $xposted = isset($_GET['posted'])?$_GET['posted']:'0';
                    if($xposted == '0'){
                        echo '<h2>Make Deposits</h2>';
                    }else{
                        echo '<h2>Payments Deposited</h2>';
                    }
                    $new_arr = [];
                    if(isset($_POST['charges_ids'])){
                        foreach((array)$_POST['charges_ids'] as $value ){
                            $value = substr($value, 4);
                            $new_arr[] = $value;
                        }
                        $filter = implode(",", $new_arr);
                        
                        if(isset($_POST['deposit_payments'])){
                            $qry = "UPDATE charges SET posted = 1,posted_date = CURDATE() WHERE `id` IN ($filter)";
                            mysql_query($qry) or die(mysql_error());
                            echo '<div class="message success">' . mysql_affected_rows() . ' Selected entries are moved to deposited folder.</div>';
                        }elseif(isset($_POST['undo_posting'])){
                            $qry = "UPDATE charges SET posted = 0,posted_date = 0 WHERE `id` IN ($filter)";
                            mysql_query($qry) or die(mysql_error());
                            echo '<div class="message success">' . mysql_affected_rows() . ' Selected entries are moved back.</div>';
                        }                    
                    }
                    ?>
                    <table class="data display dataTable" id="example">
                        <thead>
                            <tr style="background-color: #DFE3E7;border-bottom: 1px solid #DDD;padding: 10px;">
                                <td colspan="4" >
                                    <?php                                                                      
                                    $qry = "SELECT method, LEFT(method,20) AS mtext, COUNT(method) AS ctr FROM charges GROUP BY method";
                                    $result = mysql_query($qry) or die(mysql_error());
                                    $opt_method = '';
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $opt_method .= "<option value='{$row['method']}'>" . htmlspecialchars($row['mtext']) . " ({$row['ctr']})</option>";
                                    }
                                    ?>
                                    <form method="get" action="">
                                        <label> View Payment Method Type <select name="method">
                                                <option value="">All Types</option>
                                                <?php echo $opt_method ?>
                                            </select></label> <input type="submit" value="Go" />
                                    </form>                                    
                                </td>
                                <td colspan="3" class="dt-right">
                                    <h5><small class='small'>Payments Subtotal</small> $<span id='totals'>0.00</span></h5>
                                </td>
                                <td  class="dt-right">  
                                    <form id="deposit_payments" action="" method="post" >
                                    <?php if($xposted == '0'){ ?>
                                    <button type="submit" 
                                            name="deposit_payments"
                                            class="btn btn-green btn-icon btn-check"><span></span> Deposit Payments</button>
                                    <?php } else { ?>
                                        <button type="submit" 
                                            name="undo_posting"
                                            class="btn btn-maroon btn-icon btn-minus"><span></span> Undo Posting</button>
                                        <?php } ?>
                                    </form>                                        
                                </td>
                            </tr>

                            <tr>
                                <th>ID</th>
                                <th>Orders ID</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Insert Date</th>                                    
                                <th>Posted Date</th>
                                <th>Posted</th>
                                <th>Customers ID</th>
                            </tr>
                        </thead>                             
                        <tbody>
                            <?php if($xposted != '0'){
                                company_db_connect(1);     
                                $qry = "SELECT sum(`amount`) AS date_total,`posted_date` FROM boostpr1_tododash.charges WHERE posted <> '0' GROUP BY `posted_date` ORDER BY posted_date DESC";
                                $result = mysql_query($qry) or die(mysql_error());
                                while ($row = mysql_fetch_assoc($result)) {                                    
                                    echo '<tr class="group"><td colspan="8">'. date('d M Y',strtotime($row['posted_date'])) . ' <b>$'. number_format($row['date_total'],2).'</b></td></tr>';
                                    $qry2 = "SELECT a.*,CONCAT(b.customers_lastname,', ', b.customers_firstname) AS customers_name FROM boostpr1_tododash.charges a "
                                            . "INNER JOIN zen_customers b ON a.customers_id = b.customers_id WHERE a.posted_date = '{$row['posted_date']}'";
                                    $result2 = mysql_query($qry2) or die(mysql_error());
                                    while ($row = mysql_fetch_assoc($result2)) {
                                        echo "<tr id='row_{$row['id']}'><td>{$row['id']}</td><td>
<a href='vieworder.php?orderid={$row['orders_id']}&companyid=1'>view #{$row['orders_id']}
</a></td><td class='dt-amount'>$". number_format($row['amount'],2)."</td><td>{$row['method']}</td>
<td>{$row['insert_date']}</td><td>{$row['posted_date']}</td><td>{$row['posted']}</td>
<td>{$row['customers_name']}</td></tr>";
                                    }
                                }
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Orders ID</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Insert Date</th>                                    
                                <th>Posted Date</th>
                                <th>Posted</th>
                                <th>Customers ID</th>
                            </tr>
                        </tfoot>
                    </table>
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
        <script type="text/javascript">
            $(document).ready(function () {
                var intVal = function ( i ) {
                return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                var selected=[];
                var totals = 0;
                <?php if($xposted == '0'){?>
                var table = $('#example').DataTable({
                    <?php //if($xposted != '0'){?>
//                    "drawCallback": function ( settings ) {
//                        var api = this.api();
//                        var rows = api.rows( {page:'current'} ).nodes();
//                        var last=null;
//                        api.column(5, {page:'current'} ).data().each( function ( group, i ) {
//                            if ( last !== group ) {
//                                $(rows).eq( i ).before(
//                                    '<tr class="group"><td colspan="8">'+group+'</td></tr>'
//                                );
//                                last = group;
//                            }
//                        } );
//                    },
//                    "order": [[5, "desc"]],
//                    "aoColumns": [
//                    { "bSortable": false },
//                    { "bSortable": false },
//                    { "bSortable": false },
//                    { "bSortable": false },
//                    { "bSortable": false },
//                        null,
//                        { "bSortable": false },
//                        { "bSortable": false }
//                      ],
                     <?php //} else { ?>
                    "order": [[0, "desc"]],
                    "aaSorting": [],
                     <?php //} ?>
                    "columnDefs": [
                        { className: "dt-right", "targets": [ 2 ] },
                        { "targets": 1,
                            "render": function ( data ) {
                                return '<a href="vieworder.php?orderid='
                                        + data+'&amp;companyid=1">view #'
                                + data +'</a>';
                            }
                        }
                      ],
                    "processing": true,
                    "serverSide": true,
                    "ajax": "inc.ajax_deposit.php?method=<?php echo $xmethod; ?>&posted=<?php echo $xposted; ?>",                    
                    "rowCallback": function( row, data ) {
                        if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
                            $(row).addClass('selected');
                        }
                    },
                    //"stateSave": true,
                    "iDisplayLength": 25,
                            dom: 'Bfrtip',
                    "buttons": [
                        {
                            text: 'Select all',
                            action: function () {
                                table.rows().select();
                                totals = 0;
                                var ids = $.map(table.rows().data(), function (item) {
                                    totals += intVal(item[2]);
                                    return item.DT_RowId;
                                });
                                selected = ids; 
                                $('#totals').text(totals.toFixed(2));
                            }
                        },
                        {
                            text: 'Select none',
                            action: function () {
                                table.rows().deselect();
                                selected = [];
                                totals = 0;
                                $('#totals').text('0.00');
                            }
                        }
                    ]
                });
                <?php } ?>
                $('#example tbody').on('click', 'tr', function () {
                    var id = this.id;
                    if(id!==''){
                        var index = $.inArray(id, selected);
                        var amount = $(this).children()[2].innerText;

                        if ( index === -1 ) {                        
                            selected.push( id );
                            totals += intVal(amount);
                        } else {
                            selected.splice( index, 1 );
                            totals -= intVal(amount);
                        }
                        console.log(intVal(amount));                    
                        $(this).toggleClass('selected');
                        $('#totals').text(totals.toFixed(2));
                    }
                } );
                $('#example').on('draw.dt', function () {
                    setSidebarHeight();
                });
                $('#deposit_payments').submit( function (e) {
                    if(confirm('Process a total of $' + totals.toFixed(2)  + '?')){
                        $.each(selected, function(i, val) {
                            $('<input />').attr('type', 'hidden')
                           .attr('name', "charges_ids[]")
                           .attr('value', val)
                           .appendTo('#deposit_payments');
                           selected += '\n' + val;
                        });
                    }else{
                        e.preventDefault();
                        return;
                    }
                });
            });
        </script>
    </body>
</html>
