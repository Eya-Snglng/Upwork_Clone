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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
        body {
            font-family: "Arial";
        }

        .proposal-card {
            transition: all 0.3s ease;
        }

        .proposal-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .interview-badge {
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card shadow">
                    <div class="card-header text-white" style="background-color: #008080;">
                        <h3>All Proposals</h3>
                        <p class="mb-0">Showing all proposals received for your gigs</p>
                    </div>

                    <div class="card-body">
                        <?php $proposals = getAllProposalsForClient($pdo, $_SESSION['user_id']); ?>
                        <?php if (!empty($proposals)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Gig Title</th>
                                            <th>Freelancer</th>
                                            <th>Proposal</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($proposals as $proposal): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($proposal['gig_title']) ?></td>
                                                <td><?= htmlspecialchars($proposal['freelancer_name']) ?></td>
                                                <td class="text-truncate" style="max-width: 200px;">
                                                    <?= htmlspecialchars($proposal['gig_proposal_description']) ?>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($proposal['date_added'])) ?></td>
                                                <td>
                                                    <?php if (!empty($proposal['interview_time'])): ?>
                                                        <span class="badge <?= $proposal['interview_status'] == 'Accepted' ? 'badge-success' : 'badge-warning' ?> interview-badge">
                                                            <?= $proposal['interview_status'] ?? 'Pending' ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary interview-badge">No interview</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="get_gig_proposals.php?gig_id=<?= $proposal['gig_id'] ?>"
                                                        class="btn btn-sm btn-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No proposals received yet. When freelancers submit proposals, they'll appear here.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>