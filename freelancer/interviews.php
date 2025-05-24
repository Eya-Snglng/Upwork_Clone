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
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h1 class="display-4">Your Scheduled Interviews</h1>
        <?php $getAllInterviewsByUserId = getAllInterviewsByUserId($pdo, $_SESSION['user_id']); ?>
        <?php foreach ($getAllInterviewsByUserId as $row) { ?>
          <div class="card shadow mt-4 p-4" gig_interview_id="<?php echo $row['gig_interview_id']; ?>">
            <div class="card-body">
              <h3><?php echo $row['title']; ?></h3>
              <p><?php echo $row['description']; ?></p>
              <p><i><?php echo $row['client_name']; ?></i></p>
              <h5>Status:
                <?php
                if ($row['status'] == "Accepted") {
                  echo "<span class='text-success'>Accepted</span>";
                }
                if ($row['status'] == "Rejected") {
                  echo "<span class='text-danger'>Rejected</span>";
                }
                if ($row['status'] == "Pending") {
                  echo "Pending";
                }
                ?>
              </h5>
              <h4 class="text-success">Time Start: <?php echo $row['time_start']; ?></h4>
              <h4 class="text-success">Time End: <?php echo $row['time_end']; ?></h4>
              <select class="interviewStatus form-control float-right">
                <option value="">Change Status Here</option>
                <option value="Accepted">Accepted</option>
                <option value="Rejected">Rejected</option>
                <option value="Pending">Pending</option>
              </select>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
  <script>
    $('.interviewStatus').on('change', function(event) {
      event.preventDefault();
      var formData = {
        gig_interview_id: $(this).closest('.card').attr('gig_interview_id'),
        status: $(this).val(),
        updateInterviewStatus: 1
      }

      if (formData.gig_interview_id != "" && formData.status != "") {
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            location.reload();
          }
        })
      } else {
        alert("Make sure no fields are empty!");
      }
    });
  </script>
</body>

<?php include 'includes/footer.php'; ?>
<script>
  $('.interviewStatus').on('change', function(event) {
      event.preventDefault();
      var formData = {
        gig_interview_id: $(this).closest('.card').attr('gig_interview_id'),
        status: $(this).val(),
        updateInterviewStatus: 1
      }

      if (formData.gig_interview_id != "" && formData.status != "") {
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            location.reload();
          }
        })
      } else {
        alert("Make sure no fields are empty!");
      }

    })

    //------------remove 120 til 144 ----------
    <
    script >
    // Add this to the script section
    $('#scheduleInterviewForm').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serializeArray();
      formData.push({
        name: 'scheduleInterview',
        value: 1
      });

      $.ajax({
        type: "POST",
        url: "core/handleForms.php",
        data: formData,
        success: function(response) {
          var result = JSON.parse(response);
          alert(result.message);
          if (result.status === 'success') {
            location.reload();
          }
        }
      });
    });
</script>
</script>

</html>