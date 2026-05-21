<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<div class="dashboard">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="employer_dashboard.php"><i class="fas fa-briefcase"></i> My Postings</a></li>
                <li><a href="employer_applications.php"><i class="fas fa-users"></i> Applications
                        <?php if (isset($pending_count) && $pending_count > 0): ?><span class="badge"
                                style="background: var(--danger); color: white; border-radius: 50%; padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem; vertical-align: top;"><?= $pending_count ?></span><?php endif; ?></a>
                </li>
                <li><a href="employer_messages.php"><i class="fas fa-envelope"></i> Messages
                        <?php if ($unread_notif_count > 0): ?><span class="badge bg-danger rounded-pill"
                                style="font-size: 0.7rem;"><?= $unread_notif_count ?></span><?php endif; ?></a></li>
                <li><a href="employer_post_new.php" class="active"><i class="fas fa-plus-circle"></i> Post New</a></li>
                <li class="mt-2rem-pt-1rem-border">
                    <button type="button" onclick="openModalById('deleteAccountModal')"
                        style="background: none; border: none; color: var(--danger) !important; font-family: inherit; font-size: 0.95rem; cursor: pointer; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.75rem; width: 100%; text-align: left;">
                        <i class="fas fa-user-slash"></i> Delete Account
                    </button>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-shell">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert text-success mb-4"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert text-danger mb-4"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="dashboard-header">
                <div>
                    <h2 class="page-title"><i class="fas fa-plus-circle"></i> Welcome,
                        <?= htmlspecialchars($_SESSION['name']) ?>
                    </h2>
                    <p class="page-subtitle">Create polished internship listings that attract stronger applicants.</p>
                </div>
            </div>

            <section id="post-new" class="mb-8">
                <div class="form-container" style="margin: 0; max-width: 600px;">
                    <h3 class="mb-4">Post a New Internship</h3>
                    <form action="../api/post_internship.php" method="POST" class="needs-validation">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="flex gap-4">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="IT & Software">IT & Software</option>
                                    <option value="Marketing & Sales">Marketing & Sales</option>
                                    <option value="Finance & Accounting">Finance & Accounting</option>
                                    <option value="Design & Creative">Design & Creative</option>
                                    <option value="Engineering">Engineering</option>
                                    <option value="Healthcare and Medical">Healthcare and Medical</option>
                                    <option value="Education and Training">Education and Training</option>
                                    <option value="Human Resources (HR)">Human Resources (HR)</option>
                                    <option value="Business and Management">Business and Management</option>
                                    <option value="Customer Service">Customer Service</option>
                                    <option value="Media and Communication">Media and Communication</option>
                                    <option value="Legal and Law">Legal and Law</option>
                                    <option value="Agriculture and Environmental Studies">Agriculture and Environmental Studies</option>
                                    <option value="Architecture and Construction">Architecture and Construction</option>
                                    <option value="Logistics and Supply Chain">Logistics and Supply Chain</option>
                                    <option value="Tourism and Hospitality">Tourism and Hospitality</option>
                                    <option value="Data Science and AI">Data Science and AI</option>
                                    <option value="Cybersecurity and Networking">Cybersecurity and Networking</option>
                                    <option value="Banking and Insurance">Banking and Insurance</option>
                                    <option value="Economics and Statistics">Economics and Statistics</option>
                                    <option value="Science and Research">Science and Research</option>
                                    <option value="Multimedia and Animation">Multimedia and Animation</option>
                                    <option value="Public Administration">Public Administration</option>
                                    <option value="Social Sciences">Social Sciences</option>
                                    <option value="Journalism and Publishing">Journalism and Publishing</option>
                                    <option value="Pharmacy and Biotechnology">Pharmacy and Biotechnology</option>
                                    <option value="Telecommunications">Telecommunications</option>
                                    <option value="Industrial and Manufacturing">Industrial and Manufacturing</option>
                                    <option value="Project Management">Project Management</option>
                                    <option value="Entrepreneurship and Startups">Entrepreneurship and Startups</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label">Stipend</label>
                                <input type="text" name="stipend" class="form-control" value="Unpaid">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Requirements</label>
                            <textarea name="requirements" class="form-control" placeholder="Optional"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Opportunity</button>
                    </form>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">Delete Company Account</h5>
                <button type="button" class="btn-close"
                    onclick="closeModalElement(document.getElementById('deleteAccountModal'))"></button>
            </div>
            <div class="modal-body">
                <p><strong>CRITICAL WARNING:</strong> Are you sure you want to permanently delete your company account?
                </p>
                <p class="mt-1rem">This action will permanently remove:</p>
                <ul class="warning-list">
                    <li>Your company profile and all details</li>
                    <li>All your active internship postings</li>
                    <li>All applications received from students</li>
                    <li>All your messages and notifications</li>
                </ul>
                <p class="text-danger text-danger-bold-sm">
                    <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline"
                    onclick="closeModalElement(document.getElementById('deleteAccountModal'))">Cancel</button>
                <form action="../api/delete_account.php" method="POST">
                    <button type="submit" class="btn btn-primary btn-danger-solid">Yes, Delete My Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>