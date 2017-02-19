<?php echo $angular;?>
<div class="wrapper" ng-app="snackApp" ng-controller="SACtrl" ng-cloak>
            <div class="shelf shelf_5">
                <h1 class="hdg hdg_1">Voting</h1>
            </div>
            <div class="shelf shelf_2" ng-show="error_message">
              <p class="error" style="position:relative;">{{error_message}}
                <span class="btn red-x" ng-click="error_message='';">X</span>
              </p>
            </div>
            <div class="shelf shelf_2" ng-show="votes_remaining>0">
                <p>You are able to vote for up to {{max_monthly_votes}} selections each month.</p>
            </div>
            <div class="shelf shelf_2" ng-show="votes_remaining>0">
                <div class="voteBox">
                    <div class="voteBox-hd">
                        <h2 class="hdg hdg_3">Votes Remaining</h2>
                    </div>
                    <div class="voteBox-body">
                        <p class="counter" ng-class="class_votes_remaining()">{{votes_remaining}}</p>
                    </div>
                </div>
            </div>
            <div class="shelf shelf_2" ng-hide="votes_remaining>0">
                <p class="error">Oops! You have already voted the total allowed times this month.<br />Come back next month to vote again!</p>
            </div>
            <div class="split">
                <div class="shelf shelf_2">
                    <div class="shelf">
                        <h2 class="hdg hdg_2 mix-hdg_centered ">Snacks Always Purchased</h2>
                    </div>
                    <ul class="list list_centered">
                        <li ng-repeat="s in snack_list | filter:{'snack_purchase_type':'Always'}" ng-bind-html="s.snack_name_sce"></li>
                    </ul>
                </div>
            </div>
            <div class="split">
                <div class="shelf shelf_2">
                    <div class="shelf">
                        <h2 class="hdg hdg_2 mix-hdg_centered ">Snacks suggested this month</h2>
                        <input type="text" class="query_input" ng-model="snack_query" placeholder="(Search for a snack)"/>
                    </div>
                    <div class="shelf shelf_5" ng-show="electable_snack_list.length>0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Snack Food</th>
                                    <th scope="col">Current Votes</th>
                                    <th scope="col">VOTE</th>
                                    <th scope="col">Last Date Purchased</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="s in (electable_snack_list = (snack_list | filter:{'snack_purchase_type':'Electable',$:snack_query}))">
                                    <td ng-bind-html="s.snack_name_sce"></td>
                                    <td>{{s.num_votes}}</td>
                                    <td>
                                        <button class="btn btn_clear" ng-click="cast_vote(s.snack_id);"><i class="icon-check" ng-class="class_has_voted(s.user_vote)"></i></button>
                                    </td>
                                    <td>{{s.last_date_purchased_date | date}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
<?php if(!$has_made_a_suggestion):?>
                    <div class="shelf">
                        <center>Not Enough Snacks?  <a href="<?php echo base_url('Snack/suggestions');?>" class="btn">Suggest one!</a></center>
                    </div>
<?php endif;?>
                </div>
            </div>
</div><?php // /wrapper?>
