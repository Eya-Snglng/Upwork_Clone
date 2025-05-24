<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 1) {
  header("Location: ../client/index.php");
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
      body {
        font-family: "Arial";
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="display-4 text-center">Hello there and welcome! <span class="text-success"><?php echo $_SESSION['username']; ?></span>. Here are all the gigs open. You're allowed to submit your proposal only once!</div>
      <div class="row justify-content-center">
        <div class="col-md-8">

          <?php $getAllGigs = getAllGigs($pdo); ?>
          <?php foreach ($getAllGigs as $row) { ?>
          <div class="card shadow mt-4 p-4">
            <div class="card-header"><h4><?php echo $row['title']; ?> </h4></div>
            <div class="card-body">
              <p><?php echo $row['description']; ?></p>
              <p>
                <i><?php echo $row['username']; ?> </i>
              </p>
              <p>
                <i><?php echo $row['date_added']; ?> </i>
              </p>
              <?php $getProposalByGig = getProposalByGig($pdo, $row['gig_id'], $_SESSION['user_id']); ?>
              <?php if (!empty($getProposalByGig)) { ?>
              <h4 class="text-success"> Your Proposal:</h4>
              <p>
                <?php echo $getProposalByGig['gig_proposal_description']; ?> 
                <i>Submitted: <?php echo $getProposalByGig['date_added']; ?></i>
                <form class="deleteProposalForm">
                  <input type="hidden" class="gig_proposal_id" value="<?php echo $getProposalByGig['gig_proposal_id']; ?>">
                  <input type="submit" class="btn btn-danger float-right" value="Delete Proposal">
                </form>
              <?php } else {  ?>
                <h4 class="text-danger">No proposal yet!</h4>
              <?php } ?>
              </p>
              <form class="submitGigProposal mt-5">
                <div class="form-group">
                  <label for="proposal">Proposal</label>
                  <input type="hidden" value="<?php echo $row['gig_id']; ?>" class="gig_id">
                  <input type="text" placeholder="why are you the best candidate?" class="gig_proposal_description form-control">
                  <input type="submit" class="btn btn-primary mt-2 float-right">
                </div>
              </form>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script>
      $('.submitGigProposal').on('submit', function (event) {
        event.preventDefault();

        var formData = {
          gig_id: $(this).find('.gig_id').val(),
          gig_proposal_description: $(this).find('.gig_proposal_description').val(),
          newGigProposal: 1,
        }

        if (formData.gig_id != "" && formData.gig_proposal_description != "") {
          $.ajax({
            type:"POST",
            url:"core/handleForms.php",
            data:formData,
            success:function (data) {
              if (data) {
                location.reload();
              }
              else {
                alert("You're allowed to submit your proposal only once!");
              }
            }
          })
        }
        else {
          alert("Make sure no input fields are empty!");
        }
      })

      $('.deleteProposalForm').on('submit', function (event) {
        event.preventDefault();

        var formData = {
          gig_proposal_id: $(this).find('.gig_proposal_id').val(),
          deleteGigProposal: 1,
        }

        if(confirm("Are you sure you want to delete this proposal?")) {
          $.ajax({
            type:"POST",
            url:"core/handleForms.php",
            data:formData,
            success: function (data) {
              location.reload();
            }
          })
        }
      })

      
    </script>
  </body>
</html>
