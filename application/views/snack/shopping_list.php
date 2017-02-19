    <div class="wrapper">
        <div class="content" role="main">
            <div class="shelf shelf_5" style="position:relative;">
                <h2 class="hdg hdg_1"><?php echo date('F Y');?> Shopping List</h1>
<?php if(false): // disabled: no export available.  Most people would just screen shot this on their phone anyway?>
                <button class="btn float-right">Export List</button>
<?php endif;?>
            </div>
            <div class="shelf shelf_1 middle_50">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Snack Name</th>
                            <th scope="col">Purchase Location</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php foreach($shopping_list as $s):?>
                        <tr>
                            <td><?php echo $s['snack_name'];?></td>
                            <td><?php echo $s['snack_location_long'];?></td>
                        </tr>
                      <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /content -->
    </div>
