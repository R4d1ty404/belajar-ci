<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profile Information</h5>

                    <table class="table table-borderless">
                        <tr>
                            <th width="180">Username</th>
                            <td><?= session()->get('username') ?></td>
                        </tr>

                        <tr>
                            <th>Role</th>
                            <td>
                                <span class="badge bg-danger">
                                    <?= session()->get('role') ?>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td><?= session()->get('email') ?></td>
                        </tr>

                        <tr>
                            <th>Login Time</th>
                            <td><?= session()->get('login_time') ?></td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if(session()->get('isLoggedIn')): ?>
                                    <span class="badge bg-success">Sudah Login</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum Login</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>