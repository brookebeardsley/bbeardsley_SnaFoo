    <div class="wrapper">
        <div class="content" role="main">
            <div class="shelf shelf_5">
                <h2 class="hdg hdg_1">Suggestions</h1>
            </div>
            <div class="shelf shelf_2">
                <div class="error isHidden">You have attempted to add more than the allowed number of suggestions per month!
                    <br />There is a total of one allowed suggestion per month.</div>
                <div class="error isHidden">You have attempted to add a suggestion that already exists!</div>
                <div class="error isHidden">You have not completed information requested.</div>
            </div>
            <div class="content-centered">
                <div class="shelf shelf_2">
                    <form method="POST" action="<?php echo base_url('Snack/post_suggestion');?> " class="form" novalidate>
                      <?php if(count($potential_snack_suggestions)>0):?>
                        <fieldset class="shelf shelf_2">
                            <div class="shelf shelf_2">
                                <div class="shelf">
                                    <label for="snackOptions">
                                        <h2 class="hdg hdg_2">Select a snack from the list</h2>
                                    </label>
                                </div>
                                <select name="snackOptions" id="snackOptions">
                                    <option value="-1" selected disabled>(Select an Option or fill out your own below)</option>
                                  <?php foreach($potential_snack_suggestions as $s):?>
                                    <option value="<?php echo $s['snack_id'];?>"><?php echo $s['snack_name'];?></option>
                                  <?php endforeach;?>
                                </select>
                            </div>
                        </fieldset>
                        <div class="shelf shelf_5">
                            <p class="hdg hdg_1">or</p>
                        </div>
                      <?php endif;?>
                        <fieldset class="shelf shelf_5">
                            <div class="shelf">
                                <label for="suggestionInput">
                                    <h2 class="hdg hdg_2">Enter new snack suggestion &amp; purchasing location</h2>
                                </label>
                            </div>
                            <div class="shelf">
                                <input type="text" name="suggestion_name" id="suggestionInput" placeholder="Snack Suggestion" />
                            </div>
                            <div class="shelf">
                                <label for="suggestionLocation" class="isHidden">Location</label>
                                <input type="text" name="suggestion_location" id="suggestionLocation" placeholder="Location" class="" />
                            </div>
                        </fieldset>
                        <input type="submit" value="Suggest this Snack!" class="btn">
                    </form>
                </div>
            </div>
        </div>
        <!-- /content -->
    </div>
