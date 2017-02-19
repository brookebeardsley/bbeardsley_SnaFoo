<script>
var snackApp = angular.module('snackApp',[]);

snackApp.controller('SACtrl', function ($scope,$filter,$sce,$http,$timeout) {
    $scope.snack_list        = <?php echo json_encode($snack_list,JSON_NUMERIC_CHECK);?>;
    $scope.user_votes        = <?php echo json_encode($user_votes,JSON_NUMERIC_CHECK);?>;
    $scope.votes_remaining   = 0;
    $scope.max_monthly_votes = <?php echo $app_data['max_monthly_votes'];?>;
    var user_votes           = [];
    $scope.error_message     = '';

    // functions
    $scope.cast_vote = function(snack_id)
    {
      // front end validation
      // Run out of votes?
      if (!$scope.votes_remaining>0)
      {
        $scope.error_message     = "Sorry, you have already voted <?php echo $app_data['max_monthly_votes'];?> times this month!";
        return false;
      }
      // double vote?
      if (user_votes.indexOf(snack_id)!==-1)
      {
        $scope.error_message     = "Sorry, you have already voted for this item.";
        return false;
      }

      // we should probably cast the vote!
      var uri = '<?php echo base_url('snack/ng_cast_vote');?>';
      var dpkg = {snack_id:snack_id};
      $http.post(uri,dpkg)
       .then(function(resp){
         if (resp.data.status!='success')
           $scope.error_message     = resp.data.message;
        fetch_updates();
      }); // end http.post
    } // end cast_vote

    $scope.class_has_voted = function(b)
    {
      if (b)
        return 'icon-check_voted';
      else
        return 'icon-check_noVote';
    } // end class_has_voted

    $scope.class_votes_remaining = function(){
      switch($scope.votes_remaining)
      {
        case 3: return 'counter_green'; break;
        case 2: return 'counter_yellow'; break;
        case 1: return 'counter_red'; break;
        default: return 'isHidden'; break;
      } // end switch votes_remaining
    }; // end class_votes_remaining

    // internal functions (model-ish)
    var fetch_updates = function()
    {
      var uri = '<?php echo base_url('snack/get_snack_vote_data');?>';
      $http.get(uri)
       .then(function(resp){
         if (resp.data.status!='success')
           $scope.error_message     = resp.data.message;
         else
         {
           $scope.snack_list = resp.data.snack_list;
           $scope.user_votes = resp.data.user_votes;
         }
        init();
      }); // end http.get
    } // end fetch_updates

    // init
    var init = function(){
      // merge the user votes into the snack list .. but I'm prepping as my arrays are un-indexed (angular requirement)
      user_votes = [];
      for (var i = 0; i<$scope.user_votes.length; i++)
      {
        user_votes.push($scope.user_votes[i].snack_id);
      } // end foreach user_vote

      // prepping the snack_list (merge in the user votes, and handle the text rendering
      for (var i = 0; i<$scope.snack_list.length; i++)
      {
        // format snack name
        $scope.snack_list[i].snack_name_sce = $sce.trustAsHtml($scope.snack_list[i].snack_name);
        // translate last purchase date
        if ($scope.snack_list[i].last_date_purchased && $scope.snack_list[i].last_date_purchased>='2000-00-00')
        {
           var d = new Date($scope.snack_list[i].last_date_purchased);
          d.setTime( d.getTime() + d.getTimezoneOffset()*60*1000 );
          $scope.snack_list[i].last_date_purchased_date = d;
        }
        else
        {
          $scope.snack_list[i].last_date_purchased_date = '(never)';
        }
        // merge in the user vote
        $scope.snack_list[i].user_vote = (user_votes.indexOf($scope.snack_list[i].snack_id)!=-1);
      } // end foreach snack_list
      $scope.votes_remaining = $scope.max_monthly_votes - $scope.user_votes.length;
console.log($scope.snack_list);
    } // end init

    init()
}); // end controller
</script>
