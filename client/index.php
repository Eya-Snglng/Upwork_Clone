<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 0) {
  header("Location: ../freelancer/index.php");
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
    <h1 class="text-center mt-4">Hello there and welcome! <span class="text-success"><?php echo $_SESSION['username']; ?></span>. Here are all the gigs open
    </h1>
    <div class="row">
      <div class="col text-center">
        <button class="showCreateGigForm btn btn-primary">Create New Gig!</button>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <form class="createNewGig d-none">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="title form-control">
          </div>
          <div class="form-group">
            <label for="title">Description</label>
            <input type="text" class="description form-control">
            <input type="submit" class="btn btn-primary float-right mt-4">
          </div>
        </form>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <?php $getAllGigs = getAllGigs($pdo); ?>
        <?php if (!empty($getAllGigs)) { ?>
          <?php foreach ($getAllGigs as $row) { ?>
            <div class="card shadow mt-4 p-4">
              <div class="card-header">
                <h4><?php echo $row['title']; ?> </h4>
              </div>
              <div class="card-body">
                <p><?php echo $row['description']; ?></p>
                <p>
                  <i><?php echo $row['username']; ?></i>
                </p>
                <p>
                  <i><?php echo $row['date_added']; ?></i>
                </p>
              </div>
            </div>
          <?php } ?>
        <?php } else { ?>
          <h3 class="text-center mt-4">No records yet</h3>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
  <script>
    $('.showCreateGigForm').on('click', function(event) {
      $('.createNewGig').toggleClass('d-none');
    })

    $('.createNewGig').on('submit', function(event) {
      event.preventDefault();

      var formData = {
        title: $(this).find('.title').val(),
        description: $(this).find('.description').val(),
        createNewGig: 1
      }

      if (formData.title != "" && formData.description != "") {
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            location.reload()
          }
        })
      } else {
        alert("Make sure there are no empty input fields!")
      }

    })
  </script>
</body>

</html>