<nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #008080;">
  <a class="navbar-brand" href="#">Client Side</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="gigs_posted.php">Gigs Posted</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="proposals.php">
          Proposals
          <?php if (($count = getProposalsCount($pdo, $_SESSION['user_id'])) > 0): ?>
            <span class="badge badge-danger ml-1"><?= $count ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
      </li>
    </ul>
  </div>
</nav>