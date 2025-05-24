<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 0) {
  header("Location: ../employees/index.php");
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
    <div class="display-4 text-center">Gigs Posted. Double click to edit</div>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <?php $getAllGigsByUserId = getAllGigsByUserId($pdo, $_SESSION['user_id']); ?>
        <?php foreach ($getAllGigsByUserId as $row) { ?>
          <div class="gigContainer card shadow mt-4 p-4" gig_id="<?php echo $row['gig_id'] ?>">
            <div class="card-header">
              <h4><?php echo $row['title']; ?></h4>
              <button class="deleteGigBtn btn btn-danger float-right">Delete Gig</button>
            </div>
            <div class="card-body">
              <p><?php echo $row['description']; ?></p>
              <p><i><?php echo $row['date_added'] ?></i></p>
              <p><i><?php echo $row['username'] ?></i></p>
              <!-- In the gig cards section, modify the "See Gig Proposals" link to show count: -->
              <div class="float-right">
                <?php
                $proposal_count = getProposalsCountForGig($pdo, $row['gig_id']);
                $badge = $proposal_count > 0 ?
                  '<span class="badge badge-danger ml-1">' . $proposal_count . '</span>' : '';
                ?>
                <a href="get_gig_proposals.php?gig_id=<?= $row['gig_id'] ?>">
                  View Proposals <?= $badge ?>
                </a>
              </div>
              <form class="editGigForm mt-4 d-none">
                <div class="form-group">
                  <input type="hidden" value="<?php echo $row['gig_id']; ?>" class="form-control gig_id" required>
                  <label for="exampleInputEmail1">Title</label>
                  <input type="text" value="<?php echo $row['title']; ?>" class="form-control title" required>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Description</label>
                  <input type="text" value="<?php echo $row['description']; ?>" class="form-control description" required>
                  <input type="submit" class="btn btn-primary float-right mt-4" required>
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
    $('.gigContainer').on('dblclick', function(event) {
      var editForm = $(this).find('.editGigForm');
      editForm.toggleClass('d-none');
    })

    $('.deleteGigBtn').on('click', function(event) {
      var formData = {
        gig_id: $(this).closest('.gigContainer').attr('gig_id'),
        deleteGig: 1
      }
      if (formData.gigID != "") {
        if (confirm("Are you sure you want to delete this gig?")) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data) {
              location.reload();
            }
          })
        }
      } else {
        alert("An error occured with your input")
      }
    })

    $('.editGigForm').on('submit', function(event) {
      event.preventDefault();
      var formData = {
        gig_id: $(this).find('.gig_id').val(),
        title: $(this).find('.title').val(),
        description: $(this).find('.description').val(),
        updateGig: 1
      }
      if (formData.gig_id != "" && formData.title != "" && formData.description != "") {
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            location.reload();
          }
        })
      } else {
        alert("Make sure the input fields are not empty!")
      }
    })
  </script>
</body>

</html>