<?php
global $context;
$data = $context->data;
// Group archived quotations by status
$currentQuotations = array();
$openQuotations = array();
$awardedQuotations = array();
$expiredQuotations = array();

foreach ($data as $quotationkey => $quotationValue) {
    switch ($quotationValue['status']) {
        case '1':
            array_push($currentQuotations, $quotationValue);
            break;
        case '2':
            array_push($openQuotations, $quotationValue);
            break;
        case '3':
            array_push($awardedQuotations, $quotationValue);
            break;
        case '4':
            array_push($expiredQuotations, $quotationValue);
            break;
        default:
            array_push($expiredQuotations, $quotationValue);
            break;
    }
}
?>

<style>
    #service-page {
        background-image: linear-gradient(rgba(15, 7, 50, 0.079), rgba(12, 3, 51, 0.084)),
            url('<?php echo url("assets/img/strips/Umdoni-business-strip.jpg") ?>');
        min-height: 40vh;
        position: relative;
        background-repeat: no-repeat;
        background-size: cover;
    }

    #service-page p {
        bottom: 0px;
        position: absolute;
        font-size: 8em !important;
    }

    nav {
        width: 100%;
        position: relative;
        top: 0;
        left: 0;
        padding: 8px 1%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 20;
        background-color: #fff;
        color: #000;
    }

    nav ul li a {
        color: #000;
    }

    nav ul li i {
        color: #000;
    }

    .table td p {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .archive-notice {
        background-color: #f8f9fa;
        border-left: 4px solid #6c757d;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid" id="service-page">
    <div class="row">
        <div class="tag-header">
            <div class="col">
                <p class="h1 m-5 fs-1 text-white">
                    Quotation Archive
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container content-section">
    <div class="row">
        <div class="col-md-12 col-lg-12">

            <div class="archive-notice">
                <h5><i class="bi bi-info-circle"></i> Archived Quotations</h5>
                <p class="mb-0">You are viewing historical quotations that have passed their closing dates. These opportunities are no longer accepting submissions.
                <a href="<?php echo url('quotations'); ?>" class="fw-bold">View current opportunities</a></p>
            </div>

            <p class="fw-lighter fs-3 my-5">
                Browse past quotation opportunities from uMdoni Municipality. These quotations have closed and are maintained for historical reference and transparency.
            </p>

            <p class="h1 text-uppercase fw-normal">
                Archived Quotations (<?php echo count($data); ?> Total)
            </p>
        </div>
    </div>

    <div class="row mt-5">
        <div class=" col-md-12 col-lg-12 col-sm-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-expired-tab" data-bs-toggle="tab" data-bs-target="#nav-expired" type="button" role="tab" aria-controls="nav-expired" aria-selected="true">
                        <p class="fw-bold text-secondary">All Archived (<?php echo count($expiredQuotations); ?>)</p>
                    </button>
                    <button class="nav-link" id="nav-awarded-tab" data-bs-toggle="tab" data-bs-target="#nav-awarded" type="button" role="tab" aria-controls="nav-awarded" aria-selected="false">
                        <p class="fw-bold text-secondary">Awarded (<?php echo count($awardedQuotations); ?>)</p>
                    </button>
                    <button class="nav-link" id="nav-other-tab" data-bs-toggle="tab" data-bs-target="#nav-other" type="button" role="tab" aria-controls="nav-other" aria-selected="false">
                        <p class="fw-bold text-secondary">Other (<?php echo count($currentQuotations) + count($openQuotations); ?>)</p>
                    </button>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <!-- All Archived Quotations Tab -->
                <div class="tab-pane fade show active" id="nav-expired" role="tabpanel" aria-labelledby="nav-expired-tab" tabindex="0">
                    <div class="mt-5">
                        <table class="table" id="table1">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">
                                        <p class="text-uppercase">Title</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Reference</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Created Date</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Closing Date</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($expiredQuotations)) {
                                    echo '<tr><td colspan="5" class="text-center text-muted">No archived quotations found</td></tr>';
                                } else {
                                    foreach ($expiredQuotations as $key => $quotation) {
                                        $key++;
                                        if (isUrlReachable($quotation['location'])) {
                                            echo '
                                            <tr data-id="' . $quotation['id'] . '" class="table-secondary">
                                                <th scope="row">
                                                <a class="text-secondary fw-bold" href="' . $quotation['location'] . '" target="_blank">
                                                 <i class="bi bi-cloud-arrow-down-fill fs-5 text-muted"></i>
                                                </a>
                                                </th>
                                                <td>
                                                <a class="text-secondary fw-bold" href="' . $quotation['location'] . '" target="_blank">' . $quotation["title"] . '</a>
                                                <span class="badge bg-secondary ms-2">Expired</span>
                                                </td>
                                                <td>' . $quotation['reference'] . '</td>
                                                <td>' . formatDate($quotation['createdAt']) . '</td>
                                                <td> ' . formatDate($quotation['dueDate']) . '</td>
                                            </tr>';
                                        } else {
                                            echo '
                                            <tr data-id="' . $quotation['id'] . '" class="table-secondary">
                                                <th scope="row">
                                                <a class="text-secondary fw-bold" href="' . url($quotation['location']) . '" target="_blank">
                                                 <i class="bi bi-cloud-arrow-down-fill fs-5 text-muted"></i>
                                                </a>
                                                </th>
                                                <td>
                                                <a class="text-secondary fw-bold" href="' . url($quotation['location']) . '" target="_blank">' . $quotation["title"] . '</a>
                                                <span class="badge bg-secondary ms-2">Expired</span>
                                                </td>
                                                <td>' . $quotation['reference'] . '</td>
                                                <td>' . formatDate($quotation['createdAt']) . '</td>
                                                <td> ' . formatDate($quotation['dueDate']) . '</td>
                                            </tr>';
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Awarded Quotations Tab -->
                <div class="tab-pane fade" id="nav-awarded" role="tabpanel" aria-labelledby="nav-awarded-tab" tabindex="0">
                    <div class="mt-5">
                        <table class="table" id="table2">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">
                                        <p class="text-uppercase">Title</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Reference</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Created Date</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Closing Date</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($awardedQuotations)) {
                                    echo '<tr><td colspan="5" class="text-center text-muted">No awarded quotations found</td></tr>';
                                } else {
                                    foreach ($awardedQuotations as $key => $quotation) {
                                        $key++;
                                        if (isUrlReachable($quotation['location'])) {
                                            echo '
                                            <tr data-id="' . $quotation['id'] . '">
                                                <th scope="row">
                                                <a class="text-secondary fw-bold" href="' . $quotation['location'] . '" target="_blank">
                                                 <i class="bi bi-cloud-arrow-down-fill fs-5 text-success"></i>
                                                </a>
                                                </th>
                                                <td>
                                                <a class="text-secondary fw-bold" href="' . $quotation['location'] . '" target="_blank">' . $quotation["title"] . '</a>
                                                <span class="badge bg-success ms-2">Awarded</span>
                                                </td>
                                                <td>' . $quotation['reference'] . '</td>
                                                <td>' . formatDate($quotation['createdAt']) . '</td>
                                                <td> ' . formatDate($quotation['dueDate']) . '</td>
                                            </tr>';
                                        } else {
                                            echo '
                                            <tr data-id="' . $quotation['id'] . '">
                                                <th scope="row">
                                                <a class="text-secondary fw-bold" href="' . url($quotation['location']) . '" target="_blank">
                                                 <i class="bi bi-cloud-arrow-down-fill fs-5 text-success"></i>
                                                </a>
                                                </th>
                                                <td>
                                                <a class="text-secondary fw-bold" href="' . url($quotation['location']) . '" target="_blank">' . $quotation["title"] . '</a>
                                                <span class="badge bg-success ms-2">Awarded</span>
                                                </td>
                                                <td>' . $quotation['reference'] . '</td>
                                                <td>' . formatDate($quotation['createdAt']) . '</td>
                                                <td> ' . formatDate($quotation['dueDate']) . '</td>
                                            </tr>';
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Other Quotations Tab -->
                <div class="tab-pane fade" id="nav-other" role="tabpanel" aria-labelledby="nav-other-tab" tabindex="0">
                    <div class="mt-5">
                        <table class="table" id="table3">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">
                                        <p class="text-uppercase">Title</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Reference</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Created Date</p>
                                    </th>
                                    <th scope="col">
                                        <p class="text-uppercase">Closing Date</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $otherQuotations = array_merge($currentQuotations, $openQuotations);
                                if (empty($otherQuotations)) {
                                    echo '<tr><td colspan="5" class="text-center text-muted">No other archived quotations found</td></tr>';
                                } else {
                                    foreach ($otherQuotations as $key => $quotation) {
                                        $key++;
                                        $statusLabel = $quotation['status'] == '1' ? 'Current' : 'Open';
                                        $badgeColor = $quotation['status'] == '1' ? 'primary' : 'info';

                                        if (isUrlReachable($quotation['location'])) {
                                            echo '
                                            <tr data-id="' . $quotation['id'] . '">
                                                <th scope="row">
                                                <a class="text-secondary fw-bold" href="' . $quotation['location'] . '" target="_blank">
                                                 <i class="bi bi-cloud-arrow-down-fill fs-5 text-yellow"></i>
                                                </a>
                                                </th>
                                                <td>
                                                <a class="text-secondary fw-bold" href="' . $quotation['location'] . '" target="_blank">' . $quotation["title"] . '</a>
                                                <span class="badge bg-' . $badgeColor . ' ms-2">' . $statusLabel . '</span>
                                                </td>
                                                <td>' . $quotation['reference'] . '</td>
                                                <td>' . formatDate($quotation['createdAt']) . '</td>
                                                <td> ' . formatDate($quotation['dueDate']) . '</td>
                                            </tr>';
                                        } else {
                                            echo '
                                            <tr data-id="' . $quotation['id'] . '">
                                                <th scope="row">
                                                <a class="text-secondary fw-bold" href="' . url($quotation['location']) . '" target="_blank">
                                                 <i class="bi bi-cloud-arrow-down-fill fs-5 text-yellow"></i>
                                                </a>
                                                </th>
                                                <td>
                                                <a class="text-secondary fw-bold" href="' . url($quotation['location']) . '" target="_blank">' . $quotation["title"] . '</a>
                                                <span class="badge bg-' . $badgeColor . ' ms-2">' . $statusLabel . '</span>
                                                </td>
                                                <td>' . $quotation['reference'] . '</td>
                                                <td>' . formatDate($quotation['createdAt']) . '</td>
                                                <td> ' . formatDate($quotation['dueDate']) . '</td>
                                            </tr>';
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
